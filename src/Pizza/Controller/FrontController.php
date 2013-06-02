<?php

namespace Pizza\Controller;

use Patchwork\Controller\FrontController as BaseFrontController;

class FrontController extends BaseFrontController
{
    protected function route($app)
    {
        $ctrl = parent::route($app, $class);



        // Admin root

        $ctrl->get(
            '/admin',
            function () use ($app) {
                return $app->redirect($app['url_generator']->generate('pizza.list'));
            }
        );

        
        
        return $ctrl;
    }
}
