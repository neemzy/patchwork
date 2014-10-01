<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 1);

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
use DerAlex\Silex\YamlConfigServiceProvider;
use Entea\Twig\Extension\AssetExtension;
use Patchwork\App;
use Patchwork\ControllerCollection;
use Patchwork\RedBean\ServiceProvider as RedBeanServiceProvider;
use Patchwork\Controller\AdminController;
use Patchwork\Controller\ApiController;
use Patchwork\Controller\FrontController;
use Environ\Environ;
use ShareExtension\ShareExtension;

/**
 * Environments
 */
$app = App::getInstance();
$app['environ'] = new Environ();

$app['environ']
    ->add(
        'test',
        function () {
            return (!$_SERVER['HTTP_USER_AGENT'] || preg_match('/BrowserKit|PhantomJS/', $_SERVER['HTTP_USER_AGENT']));
        },
        function () {
        }
    )
    ->add(
        'dev',
        function () {
            return preg_match('/localhost|192\.168|patch\.work/', $_SERVER['SERVER_NAME']);
        },
        function () {
        }
    )
    ->add(
        'prod',
        function () {
            return true;
        },
        function () use ($app) {
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

            $app->after(
                function (Request $request, Response $response) {
                    $response->setVary('Accept-Encoding');
                    $response->headers->set('ETag', md5($response->getContent()));
                    $response->isNotModified($request);

                    return $response;
                }
            );
        }
    );

$app['environ']->init();
$app['debug'] = !$app['environ']->is('prod');



/**
 * Services
 */
$app->register(new YamlConfigServiceProvider(BASE_PATH.'/app/config/settings/'.$app['environ']->get().'.yml'));

define('REDBEAN_MODEL_PREFIX', $app['config']['redbean_prefix']);
$app->register(new RedBeanServiceProvider(str_replace('%base_path%', BASE_PATH, $app['config']['database']), $app['config']['db_user'], $app['config']['db_pass']));

if (!$app['debug']) {
    $app['redbean']->freeze(true);
    $app['redbean']->useWriterCache(true);
}

$app->register(
    new Monolog(),
    [
        'monolog.logfile' => BASE_PATH.'/var/log/'.$app['environ']->get().'_'.date('Y-m-d').'.log',
        'monolog.level' => constant('Monolog\Logger::'.strtoupper($app['config']['log_level'])),
        'monolog.name' => $app['environ']->get()
    ]
);

$app->register(new UrlGenerator());
$app->register(new Validator());
$app->register(new Translation(), ['locale_fallback' => $app['config']['locale']]);

$app['translator'] = $app->share(
    $app->extend(
        'translator',
        function ($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', BASE_PATH.'/app/config/i18n/'.$app['config']['locale'].'.yml', $app['config']['locale']);

            return $translator;
        }
    )
);

$app->register(new Twig(), ['twig.path' => BASE_PATH.'/app/views']);
$app['twig']->addExtension(new Twig_Extensions_Extension_Intl());
$app['twig']->addExtension(new Twig_Extensions_Extension_Text());
$app['twig']->addExtension(new AssetExtension($app, ['asset.directory' => str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).'assets']));
$app['twig']->addExtension(new ShareExtension());
$app['twig']->addFunction('strpos', new Twig_Function_Function('strpos'));
$app['twig']->addFilter('dump', new Twig_Filter_Function('Patchwork\Tools::dump', ['is_safe' => ['all']]));
$app['twig']->addFilter('vulgarize', new Twig_Filter_Function('Patchwork\Tools::vulgarize'));

$app->register(new Swiftmailer());
$app['swiftmailer.transport'] = new Swift_MailTransport();

$app['session'] = $app->share(
    function () {
        $session = new Session();
        $session->start();

        return $session;
    }
);



/**
 * Config
 */
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, $app['config']['full_locale']);
date_default_timezone_set($app['config']['timezone']);

Request::enableHttpMethodParameterOverride();



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



return $app;
