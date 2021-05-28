<?php

namespace Omatech\Editora\Ports;

use Omatech\Editora\Domain\CmsData\TranslatableKey;

interface TranslationsStorageInterface
{
    public static function exists(string $key): bool;
    public static function put(TranslatableKey $translation);
    public static function get(string $key): ?TranslatableKey;
    public static function putTranslated(string $key, string $language, $value);
    public static function getTranslation(string $key, string $language):string;
}
