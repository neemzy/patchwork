<?php

namespace Neemzy\Patchwork\Tests;

class EntityControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testHydrate()
    {
        $model = $this->getMock('Neemzy\Patchwork\Model\Entity');
        $model->expects($this->once())->method('dispatch')->with($this->equalTo('upload'));

        $model->expects($this->once())->method('getAsserts')->will(
            $this->returnValue(
                [
                    'field1' => null,
                    'field2' => null,
                    'spaced' => null,
                    'html' => null,
                    'data' => null
                ]
            )
        );

        $fileBag = $this->getMock('FileBag');
        $fileBag->method('has')->will($this->returnValue(false));
        //$fileBag->method('has')->will($this->returnValue(true))->with($this->equalTo('data'));
        $fileBag->method('get')->will($this->returnValue('asdf.txt'))->with($this->equalTo('data'));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->files = $fileBag;

        $request->method('get')->will(
            $this->returnValueMap(
                [
                    ['field1', 'field2', 'spaced', 'html'],
                    ['Value 1', 'Value 2', " trim me\n", '<br />']
                ]
            )
        );

        $reflection = new \ReflectionClass('Neemzy\Patchwork\Tests\TestController');
        $method = $reflection->getMethod('hydrate');
        $method->setAccessible(true);

        $controller = new TestController();
        $method->invokeArgs($controller, [&$model, $request]);

        $this->assertEquals('Value 1', $model->field1);
        $this->assertEquals('Value 2', $model->field2);
        $this->assertEquals('trim me', $model->spaced);
        $this->assertEquals('', $model->html);
        $this->assertEquals('asdf.txt', $model->data);
    }
}
