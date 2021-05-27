<?php

namespace Omatech\Editora\Adapters;

use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Structure\Clas;
use Omatech\Editora\Data\Instance;
use Omatech\Editora\Structure\CmsStructure;

class ArrayStorageAdapter implements CmsStorageInstanceInterface
{
    private static $instances;
    private static $structure;

    public function __construct(CmsStructure $structure)
    {
        self::$instances=[];
        self::$structure=$structure;
    }

    public static function exists(string $id): bool
    {
        return array_key_exists($id, self::$instances);
    }

    public static function put(string $id, Instance $instance)
    {
        self::$instances[$id]=json_encode($instance);
    }

    public static function get(string $id): Instance
    {
        $json=self::$instances[$id];
        return self::decodeInstanceJSON($json);
    }

    public static function all(): array
    {
        $ret=[];
        if (!empty(self::$instances)) {
            foreach (self::$instances as $id=>$json) {
                $ret[$id]=self::decodeInstanceJSON($json);
            }
        }
        return $ret;
    }

    private static function decodeInstanceJSON($json)
    {
        $jsonArray=json_decode($json, true);
        $classKey=$jsonArray['metadata']['class'];
        $class=self::$structure->getClass($classKey);
        
        return Instance::createFromJSONWithMetadata($class, $json);
    }
}
