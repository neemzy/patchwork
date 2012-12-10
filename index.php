<?php

require_once(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/settings.inc.php');

use Symfony\Component\HttpFoundation\Session\Session;
use \RedBean_Facade as R;

// Scaffolding
error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 1);
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'fr_FR.UTF8');
$app = new Silex\Application();

// ORM
R::setup('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
R::$toolbox->getRedBean()->setBeanHelper(new Patchwork\Helper\Bean());

// Controllers
$app->mount('/', new Patchwork\Controller\FrontController());
$app->mount('/admin/pizza', new Patchwork\Controller\AdminPizzaController());

// Session
$app['session'] = $app->share(function()
{
    $session = new Session();
    $session->start();
    return $session;
});

// Providers
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/views'));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array('locale' => 'fr'));

// Translations
$app['translator.domains'] = $translations;

// Twig extensions
$app['twig']->addExtension(new Entea\Twig\Extension\AssetExtension($app));
$app['twig']->addFunction('strpos', new Twig_Function_Function('strpos'));
$app['twig']->addFunction('fb_link', new Twig_Function_Function('Tools::fb_link'));
$app['twig']->addFunction('tw_link', new Twig_Function_Function('Tools::tw_link'));
$app['twig']->addFunction('gp_link', new Twig_Function_Function('Tools::gp_link'));
$app['twig']->addFilter('var_dump', new Twig_Filter_Function('var_dump'));

// Environment
$app['debug'] = true;
if ( ! $app['debug'])
{
    R::freeze(true);

    // Error handler
    $app->error(function(\Exception $e, $code) use ($app)
    {
        $message = $e->getMessage();
        switch ($code)
        {
            case 404:
                $message = 'La page que vous recherchez n\'existe pas ou est indisponible.';
            break;
        }
        return $app['twig']->render('front/error.twig', array('message' => $message));
    });
}

$app->run();
