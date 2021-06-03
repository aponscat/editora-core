<?php

namespace Omatech\Editora\Domain\Data;

use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Utils\Strings;

class Value
{
    protected Attribute $attribute;
    protected $value;
    private ?array $subValues=null;

    public function __construct(Attribute $attribute, $value=null, $hydrateOnly=false)
    {
        $this->attribute=$attribute;
        if ($hydrateOnly)
        {
            if (isset($value['value']) && isset($value['subvalues']))
            {
                $this->value=$value['value'];
                $this->subValues=$value['subvalues'];
            }
            else
            {
                $this->value=$value;
            }
        }
        else
        {
            $this->setSubValues($value);
            $this->setValue($value);
        }
        $this->validate();
    }

    public function toArray()
    {
        if ($this->subValues) {
            return
            [$this->attribute->getFullyQualifiedKey()=>
            ['value'=>$this->value
            , 'subvalues'=>$this->subValuesToArray()]
            ];
        }
        return
        [$this->attribute->getFullyQualifiedKey()=>$this->value];
    }

    public static function hydrate(Attribute $attribute, $value=null)
    {
        return new self($attribute, $value, true);
    }

    public function setSubValues($value=null)
    {
        if (isset($value) && is_array($value)) {
            foreach ($value as $key=>$val) {
                if ($this->attribute->existsSubAttribute($key)) {
                    $this->subValues[Strings::substringAfter($key, '.')]=$val;
                }
            }
        }
    }

    public function getSubValue($key)
    {
        if ($this->subValues && isset($this->subValues[$key])) {
            return $this->subValues[$key];
        }
        return null;
    }

    public function hasSubValue($key): bool
    {
        return ($this->subValues && isset($this->subValues[$key]));
    }

    public function getSubValuesData($language='ALL'): ?array
    {
        if ($this->attribute->hasSubAttributes($language)) {
            $atriKey=$this->attribute->getKey();
            $res=[];
            foreach ($this->attribute->getSubAttributes($language) as $subattribute) {
                $subFullKey=$subattribute->getFullyQualifiedKey();
                $subkey=$subattribute->getKey();
                if ($this->hasSubValue($subFullKey)) {
                    $subval=$this->getSubValue($subFullKey);
                    $res+=[$subkey=>$subval];
                }
            }
            return $res;
        }
        return null;
    }

    public function subValuesToArray(): ?array
    {
        //assert(isset($thuis->subValues) && is_array($this->subValues));
        $atriKey=$this->attribute->getKey();
        $res=[];
        foreach ($this->subValues as $key=>$val) {
            $res+=["$key"=>$val];
        }
        return $res;
    }



    public function setValue($value)
    {
        $this->value=$value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getKey(): string
    {
        return $this->attribute->getKey();
    }

    public function getFullyQualifiedKey(): string
    {
        return $this->attribute->getFullyQualifiedKey();
    }

    public function getKeyVal(): array
    {
        return [$this->getKey()=>$this->getValue()];
    }

    public function getData($language='ALL')
    {
        $res=$this->getSingleData($language);
        if ($this->getSubValuesData($language)) {
            foreach ($this->getSubValuesData($language) as $key=>$subvalue) {
                $res+=[$this->attribute->getKey().'.'.$key=>$subvalue];
            }
        }
        return $res;
    }

    public function getSingleData($language='ALL'): ?array
    {
        if ($this->attribute->availableInLanguage($language)) {
            return $this->getKeyVal();
        }
        return null;
    }

    public function getMultilanguageData(): ?array
    {
        return [$this->getFullyQualifiedKey()=>$this->getValue()];
    }

    public function validate(): bool
    {
        return true;
    }
}