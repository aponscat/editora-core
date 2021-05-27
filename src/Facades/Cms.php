<?php

namespace Omatech\Editora\Facades;

use Omatech\Editora\Structure\Clas;
use Omatech\Editora\Data\Instance;

class Cms implements \JsonSerializable
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

    public function jsonSerialize()
    {
        return $this->structure->jsonSerialize();
    }

    public function putInstance(Instance $instance)
    {
        return $instance->put($this->storage);
    }

    public function putJSONInstance(string $json)
    {
        $jsonInstance=json_decode($json, true);
        assert(json_last_error() == JSON_ERROR_NONE);
        if (!isset($jsonInstance['metadata']['class']))
        {
            throw new \Exception("metadata.class not found in json: $json\n");
        }
        $class=$this->getClass($jsonInstance['metadata']['class']);
        $instance=Instance::createFromJSONWithMetadata($class, $json);
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
