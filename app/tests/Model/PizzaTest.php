<?php

namespace Tests\Model;

use \RedBean_Facade as R;

class PizzaTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->pizza = R::dispense('pizza');
    }



    public function testCanCreateAPizza()
    {
        $this->assertNotNull($this->pizza);
    }



    /**
     * @expectedException Patchwork\Exception
     */
    public function testCannotSaveAnInvalidPizza()
    {
        $this->pizza->save();
    }



    public function testCanSaveAValidPizza()
    {
        $this->pizza->title = 'title';
        $this->pizza->content = 'content';
        $this->pizza->toggle();

        $this->assertTrue($this->pizza->save() > 0);
    }
}
