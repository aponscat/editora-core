<?php

namespace Omatech\Editora\Ports;

use Omatech\Editora\Domain\CmsData\Instance;

interface CmsStorageInstanceInterface
{
    public static function exists(string $id): bool;
    public static function put(string $id, Instance $instance);
    public static function get(string $id): Instance;
    public static function all(): array;
}
