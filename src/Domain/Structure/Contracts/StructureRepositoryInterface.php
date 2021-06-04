<?php

namespace Omatech\Editora\Domain\Structure\Contracts;

use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Structure\Structure;

interface StructureRepositoryInterface
{
    public static function read(string $path): Structure;
    public static function write(Structure $structure, string $path): void;
}
