<?php

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\DefaultTranslator;
use Patchwork\App;
use Pizza\Model\Pizza;

class PizzaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test warmer
     * Instantiates validator
     */
    public function setUp()
    {
        $app = App::getInstance();

        $this->validator = new Validator(
            $app['validator.mapping.class_metadata_factory'],
            $app['validator.validator_factory'],
            new DefaultTranslator()
        );
    }

    /**
     * Checks a blank pizza does not validate
     *
     * @return void
     */
    public function testInvalidPizza()
    {
        $pizza = new Pizza();

        $this->assertNotEmpty($this->validator->validate($pizza));
    }



    /**
     * Checks a valorized pizza validates
     *
     * @return void
     */
    public function testValidPizza()
    {
        $pizza = new Pizza();

        $pizza->title = 'title';
        $pizza->content = 'content';
        $pizza->image = __DIR__.'/../../../vendor/neemzy/patchwork-core/assets/img/nicEditorIcons.gif';

        $this->assertEmpty($this->validator->validate($pizza));
    }
}
