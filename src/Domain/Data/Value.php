<?php

namespace Omatech\Editora\Domain\Data;

use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Utils\Strings;

class Value
{
    protected Attribute $attribute;
    protected $value;

    public function __construct(Attribute $attribute, $value=null, $hydrateOnly=false)
    {
        $this->attribute=$attribute;
        if ($hydrateOnly)
        {
          $this->value=$value;  
        }
        else
        {
            $this->setValue($value);
        }
        $this->validate();
    }

    public function toArray()
    {
        return [$this->attribute->getFullyQualifiedKey()=>$this->value];
    }

    public static function hydrate(Attribute $attribute, $value=null)
    {
        return new self($attribute, $value, true);
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
        return $this->getSingleData($language);
    }

    public function getIndexableData($language='ALL')
    {
        return $this->getSingleIndexableData($language);
    }

    public function getSingleData($language='ALL'): ?array
    {
        if ($this->attribute->availableInLanguage($language)) {
            if ($language=='ALL')
            {
                return $this->getMultilanguageData();
            }
            return $this->getKeyVal();
        }
        return null;
    }

    public function getSingleIndexableData($language='ALL'): ?array
    {
        if ($this->attribute->availableInLanguage($language)
        && $this->attribute->isIndexable()) {
            if ($language=='ALL')
            {
                return $this->getMultilanguageData();
            }
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
