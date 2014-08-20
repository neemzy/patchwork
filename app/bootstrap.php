<?php

define('BASE_PATH', dirname(__DIR__));
require_once(BASE_PATH.'/vendor/autoload.php');

use Silex\Provider\UrlGeneratorServiceProvider as UrlGenerator;
use Silex\Provider\TwigServiceProvider as Twig;
use Silex\Provider\ValidatorServiceProvider as Validator;
use Silex\Provider\TranslationServiceProvider as Translation;
use Silex\Provider\SwiftmailerServiceProvider as Swiftmailer;
use Silex\Provider\MonologServiceProvider as Monolog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Monolog\Logger;
use \RedBean_Facade as R;
use Entea\Twig\Extension\AssetExtension;
use Patchwork\App;
use Patchwork\ControllerCollection;
use Patchwork\Controller\AdminController;
use Patchwork\Controller\ApiController;
use Patchwork\Controller\FrontController;
use Environ\Environ;
use ShareExtension\ShareExtension;

/**
 * Basics
 */
define('REDBEAN_MODEL_PREFIX', 'Pizza\\Model\\');
define('ADMIN_ROOT', 'pizza.list');
define('ADMIN_USER', 'pizza');
define('ADMIN_PASS', 'admin');

error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 1);
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'fr_FR.UTF8');
date_default_timezone_set('Europe/Paris');

Request::enableHttpMethodParameterOverride();



/**
 * Environments
 */
$app = App::getInstance();
$app['environ'] = new Environ();
$date = date('Y-m-d');

$app['environ']
    ->add(
        'test',
        function () {
            return (!$_SERVER['HTTP_USER_AGENT'] || preg_match('/BrowserKit|PhantomJS/', $_SERVER['HTTP_USER_AGENT']));
        },
        function () use ($app) {
            R::addDatabase('test', 'sqlite:'.BASE_PATH.'/var/db/test.sqlite');
            R::selectDatabase('test');

            $app->register(
                new Monolog(),
                [
                    'monolog.logfile' => BASE_PATH.'/var/log/test.log',
                    'monolog.level' => Logger::INFO,
                    'monolog.name' => 'test'
                ]
            );
        }
    )
    ->add(
        'dev',
        function () {
            return preg_match('/localhost|192\.168|patch\.work/', $_SERVER['SERVER_NAME']);
        },
        function () use ($app) {
            R::addDatabase('dev', 'sqlite:'.BASE_PATH.'/var/db/dev.sqlite');
            R::selectDatabase('dev');

            $app->register(
                new Monolog(),
                [
                    'monolog.logfile' => BASE_PATH.'/var/log/dev.log',
                    'monolog.level' => Logger::DEBUG,
                    'monolog.name' => 'dev'
                ]
            );
        }
    )
    ->add(
        'prod',
        function () {
            return true;
        },
        function () use ($app) {
            error_reporting(0);

            R::addDatabase('prod', 'mysql:host=localhost;dbname=pizza', 'pizza', 'admin');
            R::selectDatabase('prod');
            R::freeze(true);
            R::useWriterCache(true);

            $app->register(
                new Monolog(),
                [
                    'monolog.logfile' => BASE_PATH.'/var/log/'.date('Y-m-d').'.log',
                    'monolog.level' => Logger::WARNING,
                    'monolog.name' => 'prod'
                ]
            );

            $app->error(
                function (\Exception $e, $code) use ($app) {
                    $message = $e->getMessage();

                    switch ($code) {
                        case Response::HTTP_NOT_FOUND:
                            $message = $app['translator']->trans('The page you are looking for does not exist or is unavailable.');
                            break;
                    }

                    return $app['twig']->render('front/partials/error.twig', compact('message'));
                }
            );
        }
    );

$app['environ']->init();
$app['debug'] = !$app['environ']->is('prod');



/**
 * Services
 */
$app['session'] = $app->share(
    function () {
        $session = new Session();
        $session->start();

        return $session;
    }
);

$app->register(new UrlGenerator());
$app->register(new Validator());
$app->register(new Translation(), ['locale_fallback' => 'fr']);

$app['translator'] = $app->share(
    $app->extend(
        'translator',
        function ($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', BASE_PATH.'/app/i18n/fr.yml', 'fr');

            return $translator;
        }
    )
);

$app->register(new Twig(), ['twig.path' => BASE_PATH.'/app/views']);
$app['twig']->addExtension(new AssetExtension($app, ['asset.directory' => str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).'assets']));
$app['twig']->addExtension(new ShareExtension());
$app['twig']->addFunction('strpos', new Twig_Function_Function('strpos'));
$app['twig']->addFilter('dump', new Twig_Filter_Function('Patchwork\Tools::dump', ['is_safe' => ['all']]));
$app['twig']->addFilter('vulgarize', new Twig_Filter_Function('Patchwork\Tools::vulgarize'));
$app['twig']->addGlobal('title', 'Patchwork');
$app['twig']->addGlobal('description', '#PHP 5.4+ web framework powered by #Composer #Silex #RedBean #NPM');

$app->register(new Swiftmailer());
$app['swiftmailer.transport'] = new Swift_MailTransport();



/**
 * Controllers
 */
$app['controllers_factory'] = function () use ($app) {
    return new ControllerCollection($app['route_factory']);
};

$app->mount(
    '/admin/pizza',
    AdminController::getInstanceFor('pizza')
);

$app->mount(
    '/api/pizza',
    ApiController::getInstanceFor('pizza')
);

$app->mount(
    '/',
    new FrontController()
);



/**
 * ETags
 */
$app['debug'] || $app->after(
    function (Request $request, Response $response) {
        $response->setVary('Accept-Encoding');
        $response->headers->set('ETag', md5($response->getContent()));
        $response->isNotModified($request);

        return $response;
    }
);



return $app;
