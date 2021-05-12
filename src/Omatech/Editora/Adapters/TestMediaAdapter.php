<?php

namespace Omatech\Editora\Adapters;

class TestMediaAdapter implements MediaAdapterInterface
{
    private static $files=[];

    public function __construct()
    {
    }

    public static function exists(string $path): bool
    {
        return array_key_exists($path, self::$files);
    }

    public static function put(string $path, $content)
    {
        self::$files[$path]=$content;
    }

    public static function get(string $path)
    {
        return self::$files[$path];
    }
}
