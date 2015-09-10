<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
mb_internal_encoding('UTF-8');
require_once(dirname(__DIR__).'/vendor/autoload.php');

use DerAlex\Silex\YamlConfigServiceProvider;
use Entea\Twig\Extension\AssetExtension;
use Neemzy\Environ\Environment;
use Neemzy\Patchwork\Controller\AdminController;
use Neemzy\Patchwork\Controller\FrontController;
use Neemzy\Patchwork\Service\Hydrator\Provider as HydratorServiceProvider;
use Neemzy\Silex\Provider\EnvironServiceProvider;
use Neemzy\Silex\Provider\RedBean\ServiceProvider as RedBeanServiceProvider;
use Neemzy\Twig\Extension\Share\ShareExtension;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Loader\YamlFileLoader;

// Silex
Request::enableHttpMethodParameterOverride();
$app = new Application();
$app['base_path'] = dirname(__DIR__);

// Environ
$app->register(
    new EnvironServiceProvider(
        [
            'test' => new Environment(
                function () {
                    return preg_match('/BrowserKit|PhantomJS/', $_SERVER['HTTP_USER_AGENT']);
                },
                function () {
                }
            ),
            'dev' => new Environment(
                function () {
                    return preg_match('/localhost|192\.168/', $_SERVER['SERVER_NAME']);
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

$app['debug'] = $app['environ']->is('dev');

// Configuration and localization
$app->register(new YamlConfigServiceProvider($app['base_path'].'/app/config/settings/'.$app['environ']->get().'.yml'));
$app['locale'] = $app['config']['locale'];
setlocale(LC_ALL, $app['config']['full_locale']);
date_default_timezone_set($app['config']['timezone']);

// RedBean
$app->register(
    new RedBeanServiceProvider(),
    [
        'redbean.database' => str_replace('%base_path%', $app['base_path'], $app['config']['redbean']['database']),
        'redbean.username' => $app['config']['redbean']['username'],
        'redbean.password' => $app['config']['redbean']['password'],
        'redbean.namespace' => $app['config']['redbean']['namespace']
    ]
);

if (!$app['debug']) {
    $app['redbean']->freeze(true);
    $app['redbean']->useWriterCache(true);
}

// Monolog
$app->register(
    new MonologServiceProvider(),
    [
        'monolog.logfile' => $app['base_path'].'/var/log/'.$app['environ']->get().'_'.date('Y-m-d').'.log',
        'monolog.level' => constant('Monolog\Logger::'.strtoupper($app['config']['log_level'])),
        'monolog.name' => $app['environ']->get()
    ]
);

// Swift Mailer
$app->register(new SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = new Swift_MailTransport();

// Translator
$app->register(new TranslationServiceProvider());

$app['translator'] = $app->share(
    $app->extend(
        'translator',
        function ($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $dir = $app['base_path'].'/app/config/i18n/';

            foreach (scandir($dir) as $file) {
                if (preg_match('/([a-z]+\.)?'.$app['locale'].'.yml/', $file, $matches)) {
                    array_shift($matches);
                    $domain = rtrim(implode('', $matches), '.') ?: null;
                    $translator->addResource('yaml', $app['base_path'].'/app/config/i18n/'.$file, $app['locale'], $domain);
                }
            }

            return $translator;
        }
    )
);

// Misc
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new HydratorServiceProvider());

// Twig
$app->register(new TwigServiceProvider(), ['twig.path' => $app['base_path'].'/app/views', 'twig.options' => ['debug' => $app['debug']]]);
$app['twig']->addExtension(new Twig_Extension_Debug());
$app['twig']->addExtension(new Twig_Extensions_Extension_Intl());
$app['twig']->addExtension(new Twig_Extensions_Extension_Text());
$app['twig']->addExtension(new AssetExtension($app, ['asset.directory' => str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).'assets']));
$app['twig']->addExtension(ShareExtension::getInstance());

// Session
$app['session'] = $app->share(
    function () {
        $session = new Session();
        $session->start();

        return $session;
    }
);

// Controllers
$app->mount('/admin/pizza', new AdminController('pizza'));
$app->mount('/', new FrontController());

return $app;
