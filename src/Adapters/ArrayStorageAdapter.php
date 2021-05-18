<?php

namespace Omatech\Editora\Adapters;

use Omatech\Editora\Ports\CmsStorageInterface

class ArrayStorageAdapter implements CmsStorageInterface
{
    private static $instances=[];
    private static $values=[];
    private static $relationInstances=[];

    public function __construct()
    {
    }

    public static function exists(BaseValue $value): bool
    {
        return array_key_exists($value->getID(), self::$values);
    }

    public static function put(BaseValue $value)
    {
        self::values[$value->getID()]=$value;
    }

    public static function get(BaseValue $value)
    {
        return self::$values[$value->getID()];
    }

}
