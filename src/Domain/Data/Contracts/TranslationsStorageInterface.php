<?php

namespace Omatech\Editora\Domain\Data\Contracts;

use Omatech\Editora\Domain\Data\Ztatic;

interface TranslationsStorageInterface
{
    public static function exists(string $key): bool;
    public static function put(Ztatic $translation);
    public static function get(string $key): ?Ztatic;
    public static function putTranslated(string $key, string $language, $value);
    public static function getTranslation(string $key, string $language):string;
}
