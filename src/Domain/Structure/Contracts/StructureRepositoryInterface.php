<?php

namespace Omatech\Editora\Domain\Structure\Contracts;

use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Structure\Structure;

interface StructureRepositoryInterface
{
    public static function read($resource): Structure;
    public static function write($resource, Structure $structure): void;
}
