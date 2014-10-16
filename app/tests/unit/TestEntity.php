<?php

namespace Neemzy\Patchwork\Tests;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Neemzy\Patchwork\Model\Entity;

class TestEntity extends Entity
{
    /**
     * @var mixed To avoid getting to the inner bean
     */

    public $field1;

    /**
     * @var mixed To avoid getting to the inner bean
     */
    public $field2;

    /**
     * @var mixed To avoid getting to the inner bean
     */
    public $field3;

    /**
     * @var mixed To avoid getting to the inner bean
     */
    public $field4;



    /**
     * Blank interface method implementation
     *
     * @return void
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
    }
}
