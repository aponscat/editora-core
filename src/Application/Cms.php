<?php

namespace Omatech\Editora\Application;

use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Data\Contracts\InstanceRepositoryInterface;
use Omatech\Editora\Application\Contracts\CmsInterface;

class Cms implements CmsInterface
{
    private Structure $structure;
    private InstanceRepositoryInterface $storage;

    public function __construct($structure, $storage)
    {
        $this->structure=$structure;
        $this->storage=$storage;
    }

    public function getClass(string $key): Clazz
    {
        return $this->structure->getClass($key);
    }

    public function createInstance(Instance $instance): void
    {
        $this->storage->create($instance);
    }

    public function createArrayInstance($arr): Instance
    {
        assert(isset($arr['metadata']['class']));
        $class=$this->getClass($arr['metadata']['class']);
        $instance=Instance::fromArray($class, $arr);
        $this->createInstance($instance);
        return $instance;
    }

    public function getInstanceByID(string $id): Instance
    {
        return $this->storage->read($id);
    }

    public function getAllInstances(): ?array
    {
        return $this->storage::all();
    }

    public function filterInstances(array $instances, $filterFunction): array
    {
        $res=[];
        foreach ($instances as $key=>$instance) {
            $filteredInstance=$filterFunction($instance);
            if ($filteredInstance) {
                $res[$key]=$filteredInstance;
            }
        }
        return $res;
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }
}
