<?php

namespace Omatech\Editora\Adapters;

use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Data\BaseInstance;

class ArrayStorageAdapter implements CmsStorageInstanceInterface
{
    private static $instances;

    public function __construct()
    {
        self::$instances=[];
    }

    public static function exists(string $id): bool
    {
        return array_key_exists($id, self::$instances);
    }

    public static function put(string $id, BaseInstance $instance)
    {
        echo json_encode($instance);
        echo "$id\n";
        self::$instances[$id]=json_encode($instance);
    }

    public static function get(string $id): BaseInstance
    {
        $json=self::$instances[$id];
        echo "*$json*\n";
        return BaseInstance::createFromJSONWithMetadata ($json);
    }

}
