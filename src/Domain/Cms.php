<?php

namespace Omatech\Editora\Domain;

use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsData\Instance;

class Cms
{
    private $structure;
    private $storage;

    public function __construct($structure, $storage)
    {
        $this->structure=$structure;
        $this->storage=$storage;
    }

    public function getClass(string $key): Clas
    {
        return $this->structure->getClass($key);
    }

    public function putInstance(Instance $instance)
    {
        return $instance->put($this->storage);
    }

    public function putArrayInstance($arr)
    {
        assert(isset($arr['metadata']['class']));
        $class=$this->getClass($arr['metadata']['class']);
        $instance=Instance::fromArray($class, $arr);
        return $this->putInstance($instance);
    }

    public function getInstanceByID(string $id): Instance
    {
        return Instance::get($id, $this->storage);
    }

    public function getAllInstances()
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

    public function getStructure()
    {
        return $this->structure;
    }

}
