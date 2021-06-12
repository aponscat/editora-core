<?php

namespace Omatech\Editora\Domain\Structure;

class Structure
{
    private ?array $classes;
    private ?array $languages;

    private function __construct($languages, $classes)
    {
        $this->languages=$languages;
        $this->classes=$classes;
    }

    public static function createEmptyStructure()
    {
        return new self(null, null);
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function getClass(string $key): Clazz
    {
        assert(!empty($key));
        $parsedClassKeys='';
        if ($this->classes) {
            foreach ($this->classes as $class) {
                $parsedClassKeys.=' '.$class->getKey();
                if ($class->getKey()==$key) {
                    return $class;
                }
            }
        }
        throw new \Exception("$key class not found valid keys are: $parsedClassKeys!");
    }

    public function toArray()
    {
        $res=['classes'=>$this->serializeClasses()
    , 'languages'=>$this->languages];
        return $res;
    }

    public function addLanguage(string $isoCode)
    {
        $this->languages[]=$isoCode;
    }

    public function addClass(Clazz $class)
    {
        $this->classes[]=$class;
        foreach ($class->getAttributes() as $attribute) {
            $this->attributes[]=$attribute;
        }
    }
}
