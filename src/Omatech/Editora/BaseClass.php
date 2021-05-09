<?php

namespace Omatech\Editora;

use Omatech\Editora\BaseAttribute;

class BaseClass
{
    private $key;
    private $attributes;

    private function __construct($key)
    {
        $this->setKey($key);
    }

    public static function createFromAttributesArray(string $key, array $attributesInstances)
    {
        $class=new self($key);
        $class->setAttributes($attributesInstances);
        return $class;
    }

    public static function createFromJSON(string $key, string $jsonAttributes)
    {
        $attributes=json_decode($jsonAttributes, true);
        assert(json_last_error() == JSON_ERROR_NONE);
        $attributesInstances=[];
        foreach ($attributes as $id=>$attribute) {
            assert(isset($attribute['key']));
            assert(isset($attribute['type']));
            $config=null;
            if (isset($attribute['config'])) {
                $config=$attribute['config'];
            }
            $attributesInstances[$id]=new $attribute['type']($attribute['key'], $config);
        }
        return self::createFromAttributesArray($key, $attributesInstances);
    }

    private function setAttributes(array $attributes)
    {
        foreach ($attributes as $id=>$attribute) {
            assert($attribute instanceof BaseAttribute);
            $this->attributes[$id]=$attribute;
        }
    }

    private function setKey(string $key)
    {
        assert(isset($key) && !empty($key));
        $this->key=$key;
    }

    private function getAttributeByKey($attributeKey)
    {
        assert($this->existsAttribute($attributeKey));
        foreach ($this->attributes as $attribute) {
            if ($attribute->getKey()==$attributeKey) {
                return $attribute;
            }
        }
    }

    public function existsAttribute($attributeKey)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getKey()==$attributeKey) {
                return true;
            }
        }
        return false;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function createAttributeFromKey($attributeKey)
    {
        if ($this->existsAttribute($attributeKey)) {
            return ($this->getAttributeByKey($attributeKey));
        } else {
            throw new \Exception("Cannot create attribute $attributeKey in class ".$this->getKey());
        }
    }

    public function getAttributesKeys()
    {
        $res=[];
        foreach ($this->attributes as $attribute) {
            $res[]=$attribute->getKey();
        }
        return $res;
    }


    public function getAttributes()
    {
        $res=[];
        foreach ($this->attributes as $attribute) {
            $res[]=$attribute;
        }
        return $res;
    }
}
