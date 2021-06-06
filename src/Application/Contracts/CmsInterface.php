<?php

namespace Omatech\Editora\Application\Contracts;

use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Data\Contracts\InstanceRepositoryInterface;

interface CmsInterface
{
    public function __construct($structure, $storage);
    public function getClass(string $key): Clazz;
    public function createInstance(Instance $instance):void;
    public function createInstanceFromArray($arr): Instance;
    public function getInstanceByID(string $id): Instance;
    public function getAllInstances(): ?array;
    public function filterInstances(array $instances, $filterFunction): ?array;
    public function getStructure(): Structure;
}
