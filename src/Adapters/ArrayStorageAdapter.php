<?php

namespace Omatech\Editora\Adapters;

use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Data\BaseInstance;
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

    public static function put(string $id, BaseInstance $instance)
    {
        //echo json_encode($instance);
        //echo "$id\n";
        self::$instances[$id]=json_encode($instance);
    }

    public static function get(string $id): BaseInstance
    {
        //print_r(self::$instances);
        $json=self::$instances[$id];
        $jsonArray=json_decode($json, true);
        $classKey=$jsonArray['metadata']['class'];
        $class=self::$structure->getClass($classKey);
        //var_dump($class);die;
        
        return BaseInstance::createFromJSONWithMetadata ($class, $json);
    }

}
