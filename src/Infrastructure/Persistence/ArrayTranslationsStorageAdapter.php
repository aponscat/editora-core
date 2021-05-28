<?php

namespace Omatech\Editora\Infrastructure\Persistence;

use Omatech\Editora\Domain\CmsData\TranslatableKey;
use Omatech\Editora\Ports\TranslationsStorageInterface;

class ArrayTranslationsStorageAdapter implements TranslationsStorageInterface
{
    private static $translations=[];

    public static function exists(string $key): bool
    {
        assert(stripos($key, ' ')===false);
        return array_key_exists($key, self::$translations);
    }

    public static function put(TranslatableKey $translation)
    {
        $key=$translation->getKey();
        if (self::exists($key)) {
            foreach ($translation->getTranslations() as $language=>$value) {
                self::$translations[$key][$language]=$value;
            }
        } else {
            self::$translations[$key]=$translation;
        }
    }

    public static function get(string $key): ?TranslatableKey
    {
        assert(stripos($key, ' ')===false);
        if (self::exists($key)) {
            return self::$translations[$key];
        }
        return null;
    }

    public static function putTranslated(string $key, string $language, $value)
    {
        assert(stripos($key, ' ')===false);
        assert(strlen($language)==2);
        if (self::exists($key)) {
            self::$translations[$key][$language]=$value;
        } else {
            self::$translations[$key]=TranslatableKey::createFromArray($key, [$language=>$value]);
        }
    }

    public static function getTranslation(string $key, string $language):string
    {
        if (self::exists($key)) {
            return self::get($key)->get($language);
        }
        return $key;
    }
}
