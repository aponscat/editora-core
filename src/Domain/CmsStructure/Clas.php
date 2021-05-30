<?php

namespace Omatech\Editora\Domain\CmsStructure;

use Omatech\Editora\Utils\Jsons;

class Clas
{
    private string $key;
    private ?array $attributes=null;
    private ?array $relations=null;

    private function __construct($key)
    {
        $this->setKey($key);
    }

    public static function createFromAttributesArray(string $key, array $attributesInstances): Clas
    {
        $class=new self($key);
        $class->setAttributes($attributesInstances);
        return $class;
    }

    public static function createFromJSON(string $key, string $jsonAttributes, string $jsonRelations=null): Clas
    {
        $attributes=json_decode($jsonAttributes, true);
        assert(json_last_error() == JSON_ERROR_NONE);
        $attributesInstances=[];
        
        foreach ($attributes as $id=>$attribute) {
            assert(isset($attribute['key']));

            $attributeType='Omatech\Editora\Domain\CmsStructure\Attribute';
            if (isset($attribute['type'])) {
                if (class_exists($attribute['type'])) {
                    $attributeType=$attribute['type'];
                } else {
                    throw new \Exception("Invalid attribute type ".$attribute['type']." class not found!");
                }
            }

            $valueType='Omatech\Editora\Domain\CmsData\Value';
            if (isset($attribute['valueType'])) {
                if (class_exists($attribute['valueType'])) {
                    $valueType=$attribute['valueType'];
                } else {
                    throw new \Exception("Invalid value type ".$attribute['valueType'].", class not found!");
                }
            }

            $config=null;
            if (isset($attribute['config'])) {
                $config=$attribute['config'];
            }
            $attributesInstances[$id]=new $attributeType($attribute['key'], $config, $valueType);
        }
        $returnClass=self::createFromAttributesArray($key, $attributesInstances);

        if ($jsonRelations) {
            $relations=json_decode($jsonRelations, true);
            assert(json_last_error() == JSON_ERROR_NONE);
            if ($relations) {
                assert(is_array($relations));
                foreach ($relations as $relationKey=>$children) {
                    $returnClass->addRelation(new Relation($relationKey, $children));
                }
            }
        }
        return $returnClass;
    }

    
    public function toArray()
    {
        $res[$this->getKey()]=
        [
          'attributes'=>$this->serializeAttributes()
        ];

        if ($this->relations) {
            $res[$this->getKey()]['relations']=$this->serializeRelations();
        }

        return $res;
    }

    public function getData(): array
    {
        return $this->toArray();
    }

    public function addRelation(Relation $relation): void
    {
        $this->relations[$relation->getKey()]=$relation;
    }

    public function serializeRelations()
    {
        if ($this->relations) {
            $res=[];
            foreach ($this->relations as $key=>$relation) {
                $res[$key]=$relation->getChildrenKeys();
            }
            return $res;
        }
        return null;
    }

    public function serializeAttributes()
    {
        if ($this->attributes) {
            $res=[];
            foreach ($this->attributes as $key=>$attribute) {
                $res[$key]=$attribute->toArray();
            }
            return $res;
        }
        return null;
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

    public function getRelationByKey(string $key): ?Relation
    {
        if ($this->relations) {
            foreach ($this->relations as $relationKey=>$relation) {
                if ($relationKey===$key) {
                    return $relation;
                }
            }
        }
        throw new \Exception("Relation $key not found in class ".$this->getKey());
    }

    private function setAttributes(array $attributes): void
    {
        foreach ($attributes as $id=>$attribute) {
            assert($attribute instanceof Attribute);
            $this->attributes[$id]=$attribute;
        }
    }

    private function setKey(string $key): void
    {
        assert(isset($key) && !empty($key));
        $this->key=$key;
    }

    public function getAttributeByKey($attributeKey): Attribute
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

    public function createAttributeFromKey($attributeKey): Attribute
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
