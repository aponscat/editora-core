<?php

namespace Omatech\Editora\Domain\CmsData;

class TranslatableKey
{
    private string $key;
    private array $translations;

    private function __construct($key, $translations)
    {
        $this->key=$key;
        $this->translations=$translations;
    }

    public static function createFromJson($key, $jsonTranslations)
    {
        assert(!empty($key) && !empty($jsonTranslations));
        $translations=json_decode($jsonTranslations, true);
        return self::createFromArray($key, $translations);
    }

    public static function createFromArray($key, $translations)
    {
        assert(!empty($key) && !empty($translations) && is_array($translations));
        self::validateTranslationArray($translations);
        return new self($key, $translations);
    }

    public static function validateTranslationArray($translations)
    {
        foreach ($translations as $language=>$translation) {
            assert(strlen($language)==2);
        }
    }

    public function get($language)
    {
        assert(!empty($language));
        foreach ($this->translations as $languageKey=>$translation) {
            if ($language==$languageKey) {
                return $translation;
            }
        }
        return $this->key;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function getKey()
    {
        return $this->key;
    }
}
