<?php

namespace Omatech\Editora\Domain\Data\Contracts;

use Omatech\Editora\Domain\Data\Instance;

interface InstanceRepositoryInterface
{
    public static function exists(string $id): bool;
    public static function create(Instance $instance): void;
    public static function update(Instance $instance): void;
    public static function read(string $id): Instance;
    public static function delete(string $id): void;
    public static function all(): array;
}
