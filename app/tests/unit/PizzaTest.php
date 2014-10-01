<?php

use \RedBean_Facade as R;
use Patchwork\Exception;

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
     * Checks a pizza can be created
     *
     * @return void
     */
    public function testCanCreateAPizza()
    {
        $this->assertNotNull($this->pizza);
    }



    /**
     * Checks an invalid pizza cannot be saved
     *
     * @depends testCanCreateAPizza
     *
     * @expectedException Patchwork\Exception
     * @return void
     */
    public function testCannotSaveAnInvalidPizza()
    {
        $this->pizza->save();
    }



    /**
     * Checks a valid pizza can be saved
     *
     * @depends testCanCreateAPizza
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
        } catch (Exception $e) {
            $success = false;
        }

        $this->assertTrue($success);
    }
}
