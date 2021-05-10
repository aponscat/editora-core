<?php

namespace Omatech\Editora;

class BaseInstance
{
    private $class;
    private $key;
    private $status;
    private $startPublishingDate;
    private $endPublishingDate;
    private $externalID;
    private $values;
    private $relations;

    private function __construct(BaseClass $class, string $key, string $status, $startPublishingDate=null, $endPublishingDate=null, $externalID=null)
    {
        $this->class=$class;
        $this->key=$key;
        $this->status=$status;
        $this->startPublishingDate=$startPublishingDate;
        $this->endPublishingDate=$endPublishingDate;
        $this->externalID=$externalID;

        if ($class->existRelations()) {
            $classRelations=$class->getRelations();
            if ($classRelations) {
                foreach ($classRelations as $key=>$relation) {
                    $this->relations[$key]=new BaseRelationInstances($relation);
                }
            }
        }
    }

    public static function createFromValuesArray(BaseClass $class, string $key, string $status, array $values=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null)
    {
        $inst=new self($class, $key, $status, $startPublishingDate, $endPublishingDate, $externalID);
        if ($values) {
            $inst->setValues($values);
        }
        return $inst->validate();
    }

    public static function createFromJSON(BaseClass $class, string $key, string $status, string $jsonValues=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null)
    {
        $valuesArray=[];
        if ($jsonValues) {
            $values=json_decode($jsonValues, true);
            assert(json_last_error() == JSON_ERROR_NONE);
            foreach ($values as $singleValue) {
                assert(is_array($singleValue));
                foreach ($singleValue as $attributeKey=>$value) {
                    if ($class->existsAttribute($attributeKey)) {
                        $atri=$class->createAttributeFromKey($attributeKey);
                        //$valuesArray[]=new BaseValue($atri, $value);
                        $valuesArray[]=$atri->createValue($value);
                    } else {
                        throw new \Exception("Invalid attribute $attributeKey in class ".$class->getKey()." creating Instance ".$key);
                    }
                }
            }
        }
        return self::createFromValuesArray($class, $key, $status, $valuesArray, $startPublishingDate, $endPublishingDate, $externalID);
    }


    public function addToRelation(BaseRelation $relation, BaseInstance $childInstance)
    {
        assert(!empty($relation) && !empty($childInstance));
        if ($relation->isValid($childInstance)) {
            if (isset($relations[$relation->getKey()])) {
                $relations[$relation->getKey()]->add($childInstance);
            } else {
                throw new \Exception("Trying to add a relation ".$relation->getKey()." to an instance that not have this relation!");
            }
        } else {
            throw new \Exception("Trying to add child Instance ".$childInstance->getKey()." of class ".$childInstance->getClass()->getKey()." to relation ".$relation->getKey());
        }
    }

    public function setValues(array $values)
    {
        assert(isset($values) && !empty($values) && is_array($values));
        foreach ($values as $value) {
            $attributeKey=$value->getFullyQualifiedKey();
            if ($this->class->existsAttribute($attributeKey)) {
                $this->values[]=$value;
            } else {
                throw new \Exception("Invalid attribute $attributeKey in class ".$this->class->getKey()." creating Instance ".$this->getKey());
            }
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getData($language='ALL', $withMetadata=false): array
    {
        $ret=$this->getInstanceData();
        if ($withMetadata) {
            $ret=$ret+$this->getInstanceMetadata();
        }
        foreach ($this->values as $value) {
            assert($value instanceof BaseValue);
            $data=$value->getData($language);
            if ($data) {
                $ret+=$data;
            }
        }
        return $ret;
    }

    public function getMultilanguageData($withMetadata=false): array
    {
        $ret=$this->getInstanceData();
        if ($withMetadata) {
            $ret=$ret+$this->getInstanceMetadata();
        }
        foreach ($this->values as $value) {
            assert($value instanceof BaseValue);
            $data=$value->getMultilanguageData();
            if ($data) {
                $ret+=$data;
            }
        }
        return $ret;
    }
    

    private function getInstanceMetadata(): array
    {
        return
            ['metadata'=>[
      'status'=>$this->status
      ,'startPublishingDate'=>$this->startPublishingDate
      ,'endPublishingDate'=>$this->endPublishingDate
      ,'externalID'=>$this->externalID
            ]];
    }

    private function getInstanceData(): array
    {
        return ['key'=>$this->key];
    }

    public function validate(): BaseInstance
    {
        foreach ($this->values as $value) {
            $value->validate();
        }

        foreach ($this->class->getAttributes() as $attribute) {
            assert($attribute instanceof BaseAttribute);
            if ($attribute->isMandatory() && $this->isEmptyValueForAttribute($attribute)) {
                throw new \Exception("Mandatory value missing for attribute ".$attribute->getKey());
            }
        }
        return $this;
    }

    private function isEmptyValueForAttribute(BaseAttribute $attribute): bool
    {
        foreach ($this->values as $value) {
            if ($attribute->getKey()==$value->getKey()) {
                return false;
            }
        }
        return true;
    }
}
