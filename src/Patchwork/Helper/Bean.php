<?php

namespace Patchwork\Helper;

class Bean extends \RedBean_BeanHelper_Facade
{
    public function getModelForBean(\RedBean_OODBBean $bean)
    {
        $modelName = 'Patchwork\\Model\\'.ucfirst($bean->getMeta('type'));
        if ( ! class_exists($modelName))
            return null;
        $obj = \RedBean_ModelHelper::factory($modelName);
        $obj->loadBean($bean);
        return $obj;
    }
}