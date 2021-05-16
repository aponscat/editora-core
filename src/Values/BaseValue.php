<?php

namespace Omatech\Editora\Values;

use Omatech\Editora\Structure\BaseAttribute;
use Omatech\Editora\Utils\Strings;

class BaseValue
{
    protected $attribute;
    protected $value;
    private $subValues;

    public function __construct(BaseAttribute $attribute, $value=null)
    {
        $this->attribute=$attribute;
        $this->setSubValues($value);
        $this->setValue($value);
        $this->validate();
    }

    public function setSubValues($value=null)
    {
        if (isset($value) && is_array($value)) {
            foreach ($value as $key=>$val) {
                //echo "comparando $key con ".$this->attribute->getKey()."\n";
                if (Strings::startsWith($key.'.', $this->attribute->getKey())) {
                    //echo "Subvalue: $key\n";
                    $this->subValues[Strings::substringAfter($key, '.')]=$val;
                }
            }
        }
    }

    public function getSubValuesData($language='ALL'): ?array
    {
        if ($this->subValues) {
            $res=[];
            foreach ($this->subValues as $key=>$val) {
                if ($language!='ALL') {
                    if (stripos($key, ':')!==false) {// per idioma
                        $valueLanguage=Strings::substringAfter($key, ':');
                        if ($valueLanguage==$language) {
                            $res+=[Strings::substringBefore($key, ':')=>$val];
                        }
                    } else {
                        $res+=[$key=>$val];
                    }
                }
            }
            return $res;
        }
        return null;
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
