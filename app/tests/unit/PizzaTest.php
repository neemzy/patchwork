<?php

use Silex\Application;
use Silex\Provider\ValidatorServiceProvider;
use Pizza\Model\Pizza;

class PizzaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test warmer
     * Instantiates model and validator
     */
    public function setUp()
    {
        $this->pizza = new Pizza();
        $this->pizza->loadBean(new RedBean_OODBBean());

        $app = new Application();
        $app->register(new ValidatorServiceProvider());
        $this->validator = $app['validator'];
    }



    /**
     * Checks a blank pizza does not validate
     *
     * @return void
     */
    public function testInvalidPizza()
    {
        $this->assertNotEmpty($this->validator->validate($this->pizza));
    }



    /**
     * Checks a valorized pizza validates
     *
     * @return void
     */
    public function testValidPizza()
    {
        $this->pizza->title = 'title';
        $this->pizza->content = 'content';
        $this->pizza->image = __DIR__.'/../../../vendor/neemzy/patchwork-core/assets/img/nicEditorIcons.gif';

        $this->assertEmpty($this->validator->validate($this->pizza));
    }
}
