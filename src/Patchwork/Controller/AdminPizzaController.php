<?php

namespace Patchwork\Controller;

class AdminPizzaController extends AdminController
{
    protected function route($app, $auth, $class = 'pizza')
    {
        $ctrl = parent::route($app, $auth, $class);
        return $ctrl;
    }
}
