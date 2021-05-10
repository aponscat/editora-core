<?php

namespace Omatech\Editora\Structure;

class BaseClass
{
    private $key;
    private $attributes;
    private $relations;

    private function __construct($key)
    {
        $this->setKey($key);
    }

    public static function createFromAttributesArray(string $key, array $attributesInstances): BaseClass
    {
        $class=new self($key);
        $class->setAttributes($attributesInstances);
        return $class;
    }

    public static function createFromJSON(string $key, string $jsonAttributes): BaseClass
    {
        $attributes=json_decode($jsonAttributes, true);
        assert(json_last_error() == JSON_ERROR_NONE);
        $attributesInstances=[];
        foreach ($attributes as $id=>$attribute) {
            assert(isset($attribute['key']));

            $attributeType='Omatech\Editora\Structure\BaseAttribute';
            if (isset($attribute['type'])) {
                if (class_exists($attribute['type'])) {
                    $attributeType=$attribute['type'];
                } else {
                    throw new \Exception("Invalid attribute type ".$attribute['type']." class not found!");
                }
            }


            $ValueType='Omatech\Editora\Values\BaseValue';
            if (isset($attribute['valueType'])) {
                if (class_exists($attribute['valueType'])) {
                    $ValueType=$attribute['valueType'];
                } else {
                    throw new \Exception("Invalid value type ".$attribute['valueType'].", class not found!");
                }
            }

            $config=null;
            if (isset($attribute['config'])) {
                $config=$attribute['config'];
            }
            $attributesInstances[$id]=new $attributeType($attribute['key'], $config, $ValueType);
        }
        return self::createFromAttributesArray($key, $attributesInstances);
    }

    public function addRelation(BaseRelation $relation): void
    {
        $this->relations[$relation->getKey()]=$relation;
    }

    public function existRelations(): bool
    {
        return (!empty($this->relations));
    }

    public function getRelations(): array
    {
        assert($this->relations);
        return $this->relations;
    }

    private function setAttributes(array $attributes): void
    {
        foreach ($attributes as $id=>$attribute) {
            assert($attribute instanceof BaseAttribute);
            $this->attributes[$id]=$attribute;
        }
    }

    private function setKey(string $key): void
    {
        assert(isset($key) && !empty($key));
        $this->key=$key;
    }

    private function getAttributeByKey($attributeKey): BaseAttribute
    {
        assert($this->existsAttribute($attributeKey));
        foreach ($this->attributes as $attribute) {
            if ($attribute->getFullyQualifiedKey()==$attributeKey) {
                return $attribute;
            }
        }
    }



    public function existsAttribute($attributeKey): bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getFullyQualifiedKey()==$attributeKey) {
                return true;
            }
        }
        return false;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function createAttributeFromKey($attributeKey): BaseAttribute
    {
        if ($this->existsAttribute($attributeKey)) {
            return ($this->getAttributeByKey($attributeKey));
        } else {
            throw new \Exception("Cannot create attribute $attributeKey in class ".$this->getKey());
        }
    }

    public function getAttributesKeys(): array
    {
        $res=[];
        foreach ($this->attributes as $attribute) {
            $res[]=$attribute->getKey();
        }
        return $res;
    }


    public function getAttributes(): array
    {
        $res=[];
        foreach ($this->attributes as $attribute) {
            $res[]=$attribute;
        }
        return $res;
    }
}
