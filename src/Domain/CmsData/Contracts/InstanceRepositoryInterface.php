<?php

namespace Omatech\Editora\Domain\CmsData\Contracts;

use Omatech\Editora\Domain\CmsData\Instance;

interface InstanceRepositoryInterface
{
    public static function exists(string $id): bool;
    public static function put(Instance $instance): void;
    public static function get(string $id): Instance;
    public static function all(): array;
}
