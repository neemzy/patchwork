<?php

namespace Patchwork\Controller;

class AdminPizzaController extends AdminController
{
    protected function route($app, $auth, $class = 'pizza')
    {
        return parent::route($app, $auth, $class);
    }
}
