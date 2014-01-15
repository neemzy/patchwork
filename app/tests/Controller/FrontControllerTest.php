<?php

namespace Tests\Controller;

class FrontControllerTest extends \PHPUnit_Extensions_Selenium2TestCase
{
    public function setUp()
    {
        $this->setHost('localhost');
        $this->setPort(4444);
        $this->setBrowser('phantomjs');
        $this->setBrowserUrl('http://patch.work');
    }



    public function testLayout()
    {
        $this->url('/');

        $this->assertEquals($this->byCssSelector('meta[name="author"]')->attribute('content'), 'Be Seen');
    }
}
