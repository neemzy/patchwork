<?php

require_once(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/vendor/gabordemooij/redbean/RedBean/redbean.inc.php');
require_once(__DIR__.'/helpers/AssetExtension.php');
require_once(__DIR__.'/controllers/FrontController.php');
require_once(__DIR__.'/controllers/AdminController.php');

use Symfony\Component\HttpFoundation\Session\Session;

// Scaffolding
error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 1);
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'fr_FR.UTF8');
$app = new Silex\Application();
R::setup('mysql:host=localhost;dbname=patchwork', 'root', 'admin');

// Controllers
$app->mount('/', new FrontController());
$app->mount('/admin', new AdminController());

// Services
$app['session'] = $app->share(function() {
    $session = new Session();
    $session->start();
    return $session;
});
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/views'));
$app['twig']->addExtension(new \Entea\Twig\Extension\AssetExtension($app));
$app['twig']->addFilter('var_dump', new \Twig_Filter_Function('var_dump'));

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
