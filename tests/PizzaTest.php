<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');
require_once(dirname(__DIR__).'/config.php');

class PizzaTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateAPizza()
    {
        $class = MODEL_NAMESPACE.'Pizza';
        $pizza = new $class();
        $this->assertNotNull($pizza);
    }
}
