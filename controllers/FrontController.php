<?php

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



        // Homepage
        $ctrl->get('/', function() use ($app)
        {
			$root = str_replace('index.php/', '', $app['url_generator']->generate('home'));
			if ($_SERVER['REQUEST_URI'] != $root)
				return $app->redirect($root, 301);
            return $app['twig']->render('front/home.twig');
        })->bind('home');



        return $ctrl;
    }
}
