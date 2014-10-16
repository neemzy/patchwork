<?php

namespace Neemzy\Patchwork\Tests;

class EntityControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks model hydratation with scalar values
     *
     * @return void
     */
    public function testHydrate()
    {
        $model = $this->getMock('Neemzy\Patchwork\Tests\TestEntity', ['getAsserts']);

        $model->expects($this->once())->method('getAsserts')->will(
            $this->returnValue(
                [
                    'field1' => null,
                    'field2' => null,
                    'field3' => null,
                    'field4' => null
                ]
            )
        );

        $fileBag = $this->getMock('FileBag', ['has']);
        $fileBag->expects($this->any())->method('has')->will($this->returnValue(false));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', ['get']);
        $request->files = $fileBag;

        $request->expects($this->any())->method('get')->will(
            $this->returnValueMap(
                [
                    ['field1', null, false, 'Value 1'],
                    ['field2', null, false, 'Value 2'],
                    ['field3', null, false, " trim me\n"],
                    ['field4', null, false, '<br />']
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
        $this->assertEquals('trim me', $model->field3);
        $this->assertEquals('', $model->field4);
    }



    /**
     * Checks model validation
     *
     * @return void
     */
    public function testValidate()
    {
        $model = $this->getMock('Neemzy\Patchwork\Tests\TestEntity');

        $error1 = $this->getMock('Symfony\Component\Validator\ConstraintViolation', ['getPropertyPath', 'getMessage'], [], '', false);
        $error1->expects($this->once())->method('getPropertyPath')->will($this->returnValue('field1'));
        $error1->expects($this->once())->method('getMessage')->will($this->returnValue('First error message'));

        $error2 = $this->getMock('Symfony\Component\Validator\ConstraintViolation', ['getPropertyPath', 'getMessage'], [], '', false);
        $error2->expects($this->once())->method('getPropertyPath')->will($this->returnValue('field2'));
        $error2->expects($this->once())->method('getMessage')->will($this->returnValue('Second error message'));

        $validator = $this->getMock('Symfony\Component\Validator\Validator', ['validate'], [], '', false);
        $validator->expects($this->once())->method('validate')->will($this->returnValue([$error1, $error2]))->with($this->equalTo($model));

        // mocker le validateur
        // validate retourne un tableau d'objets $error

        $reflection = new \ReflectionClass('Neemzy\Patchwork\Tests\TestController');
        $method = $reflection->getMethod('validate');
        $method->setAccessible(true);

        $controller = new TestController();
        $errors = $method->invokeArgs($controller, [$model, $validator]);

        $this->assertEquals('First error message', $errors[0]['field1']);
        $this->assertEquals('Second error message', $errors[1]['field2']);
    }
}
