<?php

namespace Pizza\Controller;

use Patchwork\Controller\AdminController;

class AdminPizzaController extends AdminController
{
    protected function route($app, $auth, $class = 'pizza')
    {
        return parent::route($app, $auth, $class);
    }
}
