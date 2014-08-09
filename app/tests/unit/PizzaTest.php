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
     * Checks if we can create a pizza
     *
     * @return void
     */
    public function testCanCreateAPizza()
    {
        $this->assertNotNull($this->pizza);
    }



    /**
     * Checks if we cannot save an invalid pizza
     *
     * @expectedException Patchwork\Exception
     * @return void
     */
    public function testCannotSaveAnInvalidPizza()
    {
        $this->pizza->save();
    }



    /**
     * Checks if we can save a valid pizza
     *
     * @return void
     */
    public function testCanSaveAValidPizza()
    {
        $this->pizza->title = 'title';
        $this->pizza->content = 'content';
        $this->pizza->toggle();

        $success = true;

        try {
            $this->pizza->save();
        } catch (Patchwork\Exception $e) {
            $success = false;
        }

        $this->assertTrue($success);
    }
}
