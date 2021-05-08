<?php

namespace Omatech\Editora;

class BaseValue
{
    private $attribute;
    private $value;

    public function __construct(BaseAttribute $attribute, $value=null)
    {
        $this->attribute=$attribute;
        $this->value=$value;
    }

    public function setValue($value)
    {
        $this->value=$value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getKey()
    {
        return $this->attribute->getKey();
    }

    public function getData($language='ALL')
    {
        $attributeLanguage=$this->attribute->getLanguage();
        if ($attributeLanguage=='ALL') {
            return [$this->getKey()=>$this->value];
        } else {
            if ($language!='ALL') {
                if ($attributeLanguage==$language) {
                    return [$this->getKey()=>$this->value];
                }
            } else {
                return [$this->getKey()=>$this->value];
            }
        }
    }

    public function validate()
    {
        return true;
    }
}
