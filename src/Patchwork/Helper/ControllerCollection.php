<?php

namespace Patchwork\Helper;

class ControllerCollection extends \Silex\ControllerCollection
{
    public function cancel($path, $methods)
    {
        $methods = array_map('strtoupper', (array)$methods);

        foreach ($this->controllers as $key => $controller)
        {
            $route = $controller->getRoute();

            if (($route->getPath() == $path) && (count(array_intersect($methods, $route->getMethods()))))
            {
                if ( ! count($methods = array_diff($route->getMethods(), $methods)))
                    unset($this->controllers[$key]);

                else
                    $controller->getRoute()->setMethods($methods);
            }
        }

        return $this;
    }
}
