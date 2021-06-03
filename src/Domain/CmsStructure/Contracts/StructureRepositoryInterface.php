<?php

namespace Omatech\Editora\Domain\CmsStructure\Contracts;

use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;

interface StructureRepositoryInterface
{
    public static function read($resource): CmsStructure;
    public static function write($resource, CmsStructure $structure): void;
}
