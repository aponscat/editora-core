<?php

namespace Omatech\Editora\Adapters;

use Omatech\Editora\Data\TranslatableKey;

interface TranslationsStorageAdapterInterface
{
    public static function exists(string $key): bool;
    public static function put(TranslatableKey $translation);
    public static function get(string $key): ?TranslatableKey;
    public static function putTranslated(string $key, string $language, $value);
    public static function getTranslation(string $key, string $language):string;
}
