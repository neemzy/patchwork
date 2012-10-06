<?php

namespace Entea\Twig\Extension;

class AssetExtension extends \Twig_Extension {
    private $app;

    function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }


    public function getFunctions()
    {
        return array(
            'asset'    => new \Twig_Function_Method($this, 'asset'),
        );
    }

    public function asset($url) {
        return sprintf('%s/assets/%s', $this->app['request']->getBasePath(), ltrim($url, '/'));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'entea_asset';
    }
}
