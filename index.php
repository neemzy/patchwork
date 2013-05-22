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
$app['controllers_factory'] = function() use ($app) { return new Patchwork\Helper\ControllerCollection($app['route_factory']); };
$app->mount('/', new Patchwork\Controller\FrontController());
$app->mount('/admin/pizza', new Patchwork\Controller\AdminPizzaController());

// Session
$app['session'] = $app->share(function()
{
    $session = new Session();
    $session->start();
    return $session;
});

// Misc providers
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/views'));
$app['twig']->addExtension(new Entea\Twig\Extension\AssetExtension($app));
$app['twig']->addFunction('strpos', new Twig_Function_Function('strpos'));
$app['twig']->addFunction('fb_link', new Twig_Function_Function('Patchwork\Helper\Tools::fb_link'));
$app['twig']->addFunction('tw_link', new Twig_Function_Function('Patchwork\Helper\Tools::tw_link'));
$app['twig']->addFunction('gp_link', new Twig_Function_Function('Patchwork\Helper\Tools::gp_link'));
$app['twig']->addFunction('li_link', new Twig_Function_Function('Patchwork\Helper\Tools::li_link'));
$app['twig']->addFilter('vulgarize', new Twig_Filter_Function('Patchwork\Helper\Tools::vulgarize'));
$app['twig']->addFilter('var_dump', new Twig_Filter_Function('var_dump'));

// Translations
$app->register(new Silex\Provider\TranslationServiceProvider(), array('locale' => 'fr'));
$app['translator.domains'] = $translations;

// Swift Mailer
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = new Swift_MailTransport();

// Environment
if ( ! ($app['debug'] = DEBUG_MODE))
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
        return $app['twig']->render('front/error.twig', compact('message'));
    });
}

$app->run();
