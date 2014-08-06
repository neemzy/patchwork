<?php

use \RedBean_Facade as R;

class PizzaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test warmer
     * Instantiates a pizza
     *
     * @return void
     */
    protected function setUp()
    {
        $this->pizza = R::dispense('pizza');
    }



    /**
     * Test we can create a pizza
     *
     * @return void
     */
    public function testCanCreateAPizza()
    {
        $this->assertNotNull($this->pizza);
    }



    /**
     * Test we cannot save an invalid pizza
     *
     * @expectedException Patchwork\Exception
     * @return void
     */
    public function testCannotSaveAnInvalidPizza()
    {
        $this->pizza->save();
    }



    /**
     * Test we can save a valid pizza
     *
     * @return void
     */
    public function testCanSaveAValidPizza()
    {
        $this->pizza->title = 'title';
        $this->pizza->content = 'content';
        $this->pizza->toggle();

        $this->assertTrue($this->pizza->save() > 0);
    }
}
