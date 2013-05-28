<?php

namespace Patchwork\Helper;

class Facade extends \RedBean_Facade
{
    public static function typeHasField($type, $field)
    {
        return !! self::getCell('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ?', array($type, $field));
    }
}