<?php

namespace Patchwork\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $ctrl = $app['controllers_factory'];

        // robots.txt
        $ctrl->get('/robots.txt', function() use ($app)
        {
            $response = new Response('User-agent: *'.PHP_EOL.($app['debug'] ? 'Disallow: /' : 'Sitemap: '.$app['url_generator']->generate('home').'sitemap.xml'));
            $response->headers->set('Content-Type', 'text/plain');
            return $response;
        });

        // LESS
        $ctrl->get('/assets/css/{file}.less', function($file) use ($app)
        {
            $dir = dirname(dirname(dirname(__DIR__))).'/assets/css/';
            $less = $dir.$file.'.less';
            $css = $dir.$file.'.css';
            if ( ! file_exists($less))
                $app->abort(404);
            if ( ! $app['debug'])
            {
                if ( ! file_exists($css))
                    system('lessc -x '.$less.' > '.$css);
                $response = new Response(file_get_contents($css));
            }
            else
            {
                $output = array();
                exec('lessc '.$less, $output);
                $response = new Response(implode(PHP_EOL, $output));
            }
            $response->headers->set('Content-Type', 'text/css');
            return $response;
        });



        // Homepage
        $ctrl->get('/', function() use ($app)
        {
			$root = str_replace('index.php/', '', $app['url_generator']->generate('home'));
			if ($_SERVER['REQUEST_URI'] != $root)
				return $app->redirect($root, 301);
            return $app['twig']->render('front/home.twig');
        })->bind('home');



        // Admin root
        $ctrl->get('/admin', function() use ($app)
        {
            return $app->redirect($app['url_generator']->generate('pizza.list'));
        });



        return $ctrl;
    }
}
