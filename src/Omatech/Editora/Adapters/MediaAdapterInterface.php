<?php

namespace Omatech\Editora\Adapters;

interface MediaAdapterInterface
{
    public static function exists(string $path): bool;
    public static function put(string $path, $content);
    public static function get(string $path);
}
