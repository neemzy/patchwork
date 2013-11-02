<?php

require_once(__DIR__.'/../bootstrap.php');

use Patchwork\Helper\RedBean as R;

$app['environ']->set('test');

/**
 * @backupGlobals disabled
 */
class PizzaTest extends PHPUnit_Framework_TestCase
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
     * @expectedException Patchwork\Helper\Exception
     */
    public function testCannotSaveAnInvalidPizza()
    {
        R::store($this->pizza);
    }



    public function testCanSaveAValidPizza()
    {
        $this->pizza->title = 'title';
        $this->pizza->content = 'content';
        $this->assertTrue(R::store($this->pizza) > 0);
    }
}
