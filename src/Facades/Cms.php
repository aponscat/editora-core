<?php

namespace Omatech\Editora\Facades;

use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Data\BaseInstance;

class Cms implements \JsonSerializable
{
    private $structure;
    private $storage;

    public function __construct($structure, $storage)
    {
        $this->structure=$structure;
        $this->storage=$storage;
    }

    public function getClass(string $key): BaseClass
    {
        return $this->structure->getClass($key);
    }

    public function jsonSerialize()
    {
        return $this->structure->jsonSerialize();
    }

    public function putInstance(BaseInstance $instance)
    {
        return $instance->put($this->storage);
    }

    public function putJSONInstance(string $json)
    {
        $jsonInstance=json_decode($json, true);
        $class=$this->getClass($jsonInstance['metadata']['class']);
        $instance=BaseInstance::createFromJSONWithMetadata($class, $json);
        return $this->putInstance($instance);
    }

    public function getInstanceByID(string $id): BaseInstance
    {
        return BaseInstance::get($id, $this->storage);
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
}
