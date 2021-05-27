<?php

namespace Omatech\Editora\Structure;

use Omatech\Editora\Values\Value;
use Omatech\Editora\Utils\Strings;
use Omatech\Editora\Utils\Jsons;


class Attribute implements \JsonSerializable
{
    protected $key;
    protected $language='ALL';
    protected $mandatory=false;
    protected $valueType='Omatech\Editora\Values\Value';
    protected $adapters=null;
    protected $subattributes=null;
    protected $langSeparator=':';
    private $config;

    public function __construct($key, $config=null, $valueType=null)
    {
        $language='ALL';
        if (stripos($key, $this->langSeparator)!==false) {// one lang
            $language=Strings::substringAfter($key, $this->langSeparator);
            $key=Strings::substringBefore($key, $this->langSeparator);
        }
        assert(strlen($language)==2||$language=='ALL');
        $this->language=$language;

        $this->key=$key;
        $this->config=$config;

        if ($valueType!==null) {
            if (class_exists($valueType)) {
                $this->valueType=$valueType;
            } else {
                throw new \Exception("Invalid value type $valueType class not found for attribute $key");
            }
        }

        if ($config==null) {
            $mandatory=false;
        }

        if (isset($config['mandatory'])) {
            assert(is_bool($config['mandatory']));
            $this->mandatory=$config['mandatory'];
        }

        if (isset($config['adapters'])) {
            $this->adapters=$config['adapters'];
        }

        if (isset($config['subattributes'])) {
            foreach ($config['subattributes'] as $subattribute) {
                assert(!empty($subattribute['key']));
                $subattributeConfig=null;
                if (isset($subattribute['config'])) {
                    $subattributeConfig=$subattribute['config'];
                }
                $subattributeValueType=null;
                if (isset($subattribute['valueType'])) {
                    $subattributeValueType=$subattribute['valueType'];
                }
                $this->subattributes[]=new self($subattribute['key'], $subattributeConfig, $subattributeValueType);
            }
        }
    }

    public function hasSubAttributes($language='ALL')
    {
        if ($this->subattributes) {
            foreach ($this->subattributes as $subattribute) {
                if ($subattribute->availableInLanguage($language)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function availableInLanguage($language='ALL')
    {
        $attributeLanguage=$this->getLanguage();
        if ($attributeLanguage=='ALL') {
            return true;
        } else {
            if ($language!='ALL') {
                if ($attributeLanguage==$language) {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public function getSubAttributes($language='ALL')
    {
        if ($this->subattributes) {
            $res=[];
            foreach ($this->subattributes as $subattribute) {
                if ($subattribute->availableInLanguage($language)) {
                    $res[]=$subattribute;
                }
            }
            return $res;
        }
        return null;
    }

    public function existsSubAttribute($attributeKey): bool
    {
        if ($this->subattributes) {
            foreach ($this->subattributes as $subattribute) {
                if ($this->getFullyQualifiedKey().'.'.$subattribute->getFullyQualifiedKey()==$attributeKey) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getSubAttribute($attributeKey)
    {
        if ($this->subattributes) {
            foreach ($this->subattributes as $subattribute) {
                if ($this->getFullyQualifiedKey().'.'.$subattribute->getFullyQualifiedKey()==$attributeKey) {
                    return $subattribute;
                }
            }
        }
        return null;
    }

    public function jsonSerialize()
    {
        $res['key']=$this->getFullyQualifiedKey();
        if ($this->config) {
            $res['config']=$this->config;
        }

        if (!$this->valueType=='Omatech\Editora\Values\Value') {
            $res['valueType']=$this->valueType;
        }
        return $res;
    }

    public function getData(): array
    {
        return $this->jsonSerialize();
    }

    public function createValue($value): Value
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
