<?php

namespace Omatech\Editora\Infrastructure\Persistence\Memory;

use Omatech\Editora\Domain\CmsData\Contracts\InstanceRepositoryInterface;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;

class ArrayInstanceRepository implements InstanceRepositoryInterface
{
    private static array $instances;
    private static CmsStructure $structure;

    public function __construct(CmsStructure $structure)
    {
        self::$instances=[];
        self::$structure=$structure;
    }

    public static function exists(string $id): bool
    {
        return array_key_exists($id, self::$instances);
    }

    public static function create(Instance $instance): void
    {
        if ($instance->hasID())
        {
            $id=$instance->ID();
        }
        else
        {
            $id=uniqid();
            $instance->setID($id);
        }
        self::$instances[$id]=$instance->toArray();
    }

    public static function update(Instance $instance): void
    {
        assert($instance->hasID());
        $id=$instance->ID();
        self::$instances[$id]=$instance->toArray();
    }    

    public static function delete(string $id): void
    {
        unset(self::$instances[$id]);
    }    

    public static function read(string $id): Instance
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
        return Instance::hydrateFromArray($class, $arr);
    }
}
