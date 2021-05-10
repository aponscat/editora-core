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

    public function getKey(): string
    {
        return $this->attribute->getKey();
    }

    public function getKeyVal(): array
    {
        return [$this->getKey()=>$this->getValue()];
    }

    public function getData($language='ALL'): ?array
    {
        $attributeLanguage=$this->attribute->getLanguage();
        if ($attributeLanguage=='ALL') {
            return $this->getKeyVal();
        } else {
            if ($language!='ALL') {
                if ($attributeLanguage==$language) {
                    return $this->getKeyVal();
                }
            } else {
                return $this->getKeyVal();
            }
        }
        return null;
    }

    public function validate(): bool
    {
        return true;
    }
}
