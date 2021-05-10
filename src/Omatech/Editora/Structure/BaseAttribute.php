<?php

namespace Omatech\Editora\Structure;

use Omatech\Editora\Values\BaseValue;

class BaseAttribute
{
    protected string $key;
    protected string $language='ALL';
    protected bool $mandatory=false;
    protected string $valueType='Omatech\Editora\Values\BaseValue';

    public function __construct($key, $config=null, $valueType=null)
    {
        $this->key=$key;

        if ($valueType!==null) {
            if (class_exists($valueType)) {
                $this->valueType=$valueType;
            } else {
                throw new \Exception("Invalid value type $valueType class not found for attribute $key");
            }
        }

        if ($config==null) {
            $language='ALL';
            $mandatory=false;
        }

        if (isset($config['language'])) {
            assert(strlen($config['language']==2));
            $this->language=$config['language'];
        }

        if (isset($config['mandatory'])) {
            assert(is_bool($config['mandatory']));
            $this->mandatory=$config['mandatory'];
        }
    }

    public function getData(): array
    {
        return [$this->getKey()=>
        ['language'=>$this->language
        ,'mandatory'=>$this->mandatory
        ]];
    }

    public function createValue($value): BaseValue
    {
        $class=$this->valueType;
        return new $class($this, $value);
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getFullyQualifiedKey(): string
    {
        if ($this->language!='ALL') {
            return $this->getKey().":".$this->language;
        }
        return $this->getKey();
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
