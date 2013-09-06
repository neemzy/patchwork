<?php

define('BASE_PATH', __DIR__);
require_once(BASE_PATH.'/vendor/autoload.php');

use Symfony\Component\HttpFoundation\Session\Session;
use Patchwork\Controller\AdminController;
use Patchwork\Controller\ApiController;
use Patchwork\Helper\RedBean as R;

// Configuration

define('MODEL_NAMESPACE', 'Pizza\\Model\\');

define('DB_HOST', 'localhost');
define('DB_NAME', 'pizza');
define('DB_USER', 'root');
define('DB_PASS', 'admin');

define('ADMIN_ROOT', 'pizza.list');
define('ADMIN_USER', 'pizza');
define('ADMIN_PASS', 'admin');



// Scaffolding

error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 1);
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'fr_FR.UTF8');
date_default_timezone_set('Europe/Paris');



// Environments && database

$app = new Silex\Application();
$app['environ'] = new Environ\Environ();

$app['environ']->add(
    'dev',
    function () {
        return preg_match('/localhost/', $_SERVER['SERVER_NAME']);
    },
    function () {
        R::addDatabase('dev', 'sqlite:'.BASE_PATH.'/db/dev.sqlite');
        R::selectDatabase('dev');
        R::$toolbox->getRedBean()->setBeanHelper(new Patchwork\Helper\BeanHelper());
    }
)->add(
    'test',
    function () {
        return false;
    },
    function () {
        R::addDatabase('test', 'sqlite:'.BASE_PATH.'/db/test.sqlite');
        R::selectDatabase('test');
        R::$toolbox->getRedBean()->setBeanHelper(new Patchwork\Helper\BeanHelper());
    }
)->add(
    'prod',
    function () {
        return true;
    },
    function () use ($app) {
        R::addDatabase('prod', 'mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
        R::selectDatabase('prod');
        R::$toolbox->getRedBean()->setBeanHelper(new Patchwork\Helper\BeanHelper());
        R::freeze(true);

        $app->error(
            function (\Exception $e, $code) use ($app) {
                $message = $e->getMessage();
                switch ($code) {
                    case 404:
                        $message = 'La page que vous recherchez n\'existe pas ou est indisponible.';
                        break;
                }
                return $app['twig']->render('front/error.twig', compact('message'));
            }
        );
    }
);

$app['environ']->init();
$app['debug'] = (! $app['environ']->is('prod'));



// Controllers

$app['controllers_factory'] = function () use ($app) {
    return new Patchwork\Helper\ControllerCollection($app['route_factory']);
};

$app->mount(
    '/',
    new Patchwork\Controller\FrontController()
);

$app->mount(
    '/admin/pizza',
    AdminController::getInstanceFor('pizza')
);

$app->mount(
    '/api/pizza',
    ApiController::getInstanceFor('pizza')
);



// Session

$app['session'] = $app->share(
    function () {
        $session = new Session();
        $session->start();
        return $session;
    }
);



// Services

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => BASE_PATH.'/views'));
$app['twig']->addExtension(new Entea\Twig\Extension\AssetExtension($app, array('asset.directory' => str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).'assets')));
$app['twig']->addFunction('strpos', new Twig_Function_Function('strpos'));
$app['twig']->addFilter('vulgarize', new Twig_Filter_Function('Patchwork\Helper\Tools::vulgarize'));
$app['twig']->addFilter('var_dump', new Twig_Filter_Function('var_dump'));
$app['twig']->addFunction('twitter', new Twig_Function_Function('Patchwork\Helper\Tools::twitter'));
$app['twig']->addFunction('facebook', new Twig_Function_Function('Patchwork\Helper\Tools::facebook'));

$app->register(new Silex\Provider\TranslationServiceProvider(), array('locale' => 'fr'));
$app['translator.domains'] = array(
    'messages' => array(
        'fr' => array(
            '[title]' => 'Titre',
            '[content]' => 'Contenu',
            'This value should not be blank.' => 'Ce champ est requis.',
            'This value is not valid.' => 'Ce champ est invalide.',
            'This value is not a valid email address.' => 'Ce champ doit contenir une adresse e-mail valide.'
        ),
    ),
);

$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = new Swift_MailTransport();
