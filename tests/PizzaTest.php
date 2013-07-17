<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use Patchwork\Helper\RedBean as R;

class PizzaTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        R::setup('sqlite:test.db');
        R::$toolbox->getRedBean()->setBeanHelper(new Patchwork\Helper\BeanHelper());
        $this->pizza = R::dispense('pizza');
    }



    public function testCanCreateAPizza()
    {
        $this->assertNotNull($this->pizza);
    }
}
