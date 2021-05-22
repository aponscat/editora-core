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

    public function putInstanceWithID(string $id, BaseInstance $instance)
    {
        $this->storage::put($id, $instance);
        return $id;
    }

    public function getInstanceByID(string $id): BaseInstance
    {
        $instance=$this->storage::get($id);
        return $instance;
    }
}
