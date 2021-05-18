<?php

namespace Omatech\Editora\Ports;

interface MediaInterface
{
    public static function exists(string $path): bool;
    public static function put(string $path, $content);
    public static function get(string $path);
}
