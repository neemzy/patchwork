<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');
require_once(dirname(__DIR__).'/config.php');

class PizzaTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $class = MODEL_NAMESPACE.'Pizza';
        $this->pizza = new $class;
    }

    public function testCanCreateAPizza()
    {
        $this->assertNotNull($this->pizza);
    }
}
