<?php

namespace Omatech\Editora;

class BaseValue
{
    protected $attribute;
    protected $value;

    public function __construct(BaseAttribute $attribute, $value=null)
    {
        $this->attribute=$attribute;
        $this->setValue($value);
        $this->validate();
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

    public function getMultilanguageData(): ?array
    {
        return [$this->getFullyQualifiedKey()=>$this->getValue()];
    }

    public function validate(): bool
    {
        return true;
    }
}
