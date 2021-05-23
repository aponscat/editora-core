<?php

namespace Omatech\Editora\Ports;

use Omatech\Editora\Data\BaseInstance;

interface CmsStorageInstanceInterface
{
    public static function exists(string $id): bool;
    public static function put(string $id, BaseInstance $instance);
    public static function get(string $id): BaseInstance;
    public static function all(): array;
}
