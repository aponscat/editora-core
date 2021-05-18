<?php

namespace Omatech\Editora\Ports;

interface CmsStorageInterface
{
    public static function exists(BaseValue $value): bool;
    public static function put(BaseValue $value);
    public static function get(string $key): BaseValue;
}
