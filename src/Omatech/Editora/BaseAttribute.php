<?php

namespace Omatech\Editora;

class BaseAttribute
{
    private string $key;
    private string $language='ALL';
    private bool $mandatory=false;
    private string $valueType='\Omatech\Editora\BaseValue';

    public function __construct($key, $config=null)
    {
        $this->key=$key;
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

        if (isset($config['valueType'])) {
            $this->valueType=$config['valueType'];
        }
    }

    public function getData(): array
    {
        return [$this->key=>
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

    public function getLanguage(): string
    {
        return $this->language;
    }
}
