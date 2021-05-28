<?php

namespace Omatech\Editora\Infrastructure\Persistence;

use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;

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
        self::$instances[$id]=$instance->toArray();
    }

    public static function get(string $id): Instance
    {
        $arr=self::$instances[$id];
        return self::hydrateInstance($arr);
    }

    public static function all(): array
    {
        $ret=[];
        if (!empty(self::$instances)) {
            foreach (self::$instances as $id=>$arr) {
                $ret[$id]=self::hydrateInstance($arr);
            }
        }
        return $ret;
    }

    private static function hydrateInstance($arr): Instance
    {
        $classKey=$arr['metadata']['class'];
        $class=self::$structure->getClass($classKey);
        return Instance::fromArray($class, $arr);
    }

}
