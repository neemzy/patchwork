<?php

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $ctrl = $app['controllers_factory'];



        // Administration homepage
        $ctrl->get('/', function() use ($app)
        {
            return $app['twig']->render('admin/home.twig');
        })->bind('admin.home');



        return $ctrl;
    }
}
