<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 1);

define('BASE_PATH', dirname(__DIR__));
require_once(BASE_PATH.'/vendor/autoload.php');

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use DerAlex\Silex\YamlConfigServiceProvider;
use Entea\Twig\Extension\AssetExtension;
use Patchwork\App;
use Patchwork\ControllerCollection;
use Patchwork\Controller\AdminController;
use Patchwork\Controller\ApiController;
use Patchwork\Controller\FrontController;
use Neemzy\Silex\Provider\RedBean\ServiceProvider as RedBeanServiceProvider;
use Neemzy\Silex\Provider\EnvironServiceProvider;
use Neemzy\Environ\Environment;
use Neemzy\Twig\Extension\ShareExtension;

$app = App::getInstance();



/**
 * Service providers
 */
$app->register(
    new EnvironServiceProvider(
        [
            'test' => new Environment(
                function () {
                    return (!$_SERVER['HTTP_USER_AGENT'] || preg_match('/BrowserKit|PhantomJS/', $_SERVER['HTTP_USER_AGENT']));
                },
                function () {
                }
            ),
            'dev' => new Environment(
                function () {
                    return preg_match('/localhost|192\.168|patch\.work/', $_SERVER['SERVER_NAME']);
                },
                function () {
                }
            ),
            'prod' => new Environment(
                function () {
                    return true;
                },
                function () use ($app) {
                    $app->error(
                        function (Exception $e, $code) use ($app) {
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
            )
        ]
    )
);

$app->register(new YamlConfigServiceProvider(BASE_PATH.'/app/config/settings/'.$app['environ']->get().'.yml'));

$app->register(
    new RedBeanServiceProvider(
        str_replace('%base_path%', BASE_PATH, $app['config']['database']),
        $app['config']['db_user'],
        $app['config']['db_pass']
    )
);

$app->register(
    new MonologServiceProvider(),
    [
        'monolog.logfile' => BASE_PATH.'/var/log/'.$app['environ']->get().'_'.date('Y-m-d').'.log',
        'monolog.level' => constant('Monolog\Logger::'.strtoupper($app['config']['log_level'])),
        'monolog.name' => $app['environ']->get()
    ]
);

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new TranslationServiceProvider(), ['locale_fallback' => $app['config']['locale']]);

$app['translator'] = $app->share(
    $app->extend(
        'translator',
        function ($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $dir = BASE_PATH.'/app/config/i18n/';

            foreach (scandir($dir) as $file) {
                if (preg_match('/([a-z]+\.)?'.$app['config']['locale'].'.yml/', $file, $matches)) {
                    array_shift($matches);
                    $domain = rtrim(implode('', $matches), '.') ?: null;
                    $translator->addResource('yaml', BASE_PATH.'/app/config/i18n/'.$file, $app['config']['locale'], $domain);
                }
            }

            return $translator;
        }
    )
);


$app->register(new TwigServiceProvider(), ['twig.path' => BASE_PATH.'/app/views']);
$app['twig']->addExtension(new Twig_Extensions_Extension_Intl());
$app['twig']->addExtension(new Twig_Extensions_Extension_Text());
$app['twig']->addExtension(new AssetExtension($app, ['asset.directory' => str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).'assets']));
$app['twig']->addExtension(new ShareExtension());
$app['twig']->addFunction('strpos', new Twig_Function_Function('strpos'));
$app['twig']->addFilter('dump', new Twig_Filter_Function('Patchwork\Tools::dump', ['is_safe' => ['all']]));
$app['twig']->addFilter('vulgarize', new Twig_Filter_Function('Patchwork\Tools::vulgarize'));

$app->register(new SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = new Swift_MailTransport();

$app['session'] = $app->share(
    function () {
        $session = new Session();
        $session->start();

        return $session;
    }
);



/**
 * Configuration
 */
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, $app['config']['full_locale']);
date_default_timezone_set($app['config']['timezone']);

define('REDBEAN_MODEL_PREFIX', $app['config']['redbean_prefix']);
Request::enableHttpMethodParameterOverride();
$app['locale'] = $app['config']['locale'];
$app['debug'] = !$app['environ']->is('prod');

if (!$app['debug']) {
    $app['redbean']->freeze(true);
    $app['redbean']->useWriterCache(true);
}



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
