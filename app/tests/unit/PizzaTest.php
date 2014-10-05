<?php

use Patchwork\App;

class PizzaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks a blank pizza does not validate
     *
     * @return void
     */
    public function testInvalidPizza()
    {
        $pizza = App::getInstance()['redbean']->dispense('pizza');

        $this->assertNotEmpty($pizza->validate());
    }



    /**
     * Checks a valorized pizza validates
     *
     * @return void
     */
    public function testValidPizza()
    {
        $pizza = App::getInstance()['redbean']->dispense('pizza');
        $pizza->title = 'title';
        $pizza->content = 'content';

        $this->assertEmpty($pizza->validate());
    }
}
