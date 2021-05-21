<?php

namespace Omatech\Editora\Facades;

use Omatech\Editora\Structure\BaseClass;

class Cms implements \JsonSerializable {
    private $structure;
    private $storage;

    public function __construct ($structure, $storage)
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
}