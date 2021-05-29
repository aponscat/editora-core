<?php

namespace Omatech\Editora\Domain\CmsData\Contracts;

interface MediaInterface
{
    public static function exists(string $path): bool;
    public static function put(string $path, $content);
    public static function get(string $path);
}
