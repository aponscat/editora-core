<?php

namespace Omatech\Editora\Infrastructure\Persistence\Memory;

use Omatech\Editora\Domain\Data\Ztatic;
use Omatech\Editora\Domain\Data\Contracts\TranslationsStorageInterface;

class ArrayTranslationsStorageAdapter implements TranslationsStorageInterface
{
    private static array $translations=[];

    public static function exists(string $key): bool
    {
        assert(stripos($key, ' ')===false);
        return array_key_exists($key, self::$translations);
    }

    public static function put(Ztatic $translation)
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

    public static function get(string $key): ?Ztatic
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
            self::$translations[$key]=Ztatic::createFromArray($key, [$language=>$value]);
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
