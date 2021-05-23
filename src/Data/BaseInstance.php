<?php

namespace Omatech\Editora\Data;

use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Structure\BaseAttribute;
use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Structure\BaseRelation;
use Omatech\Editora\Data\BaseRelationInstances;
use Omatech\Editora\Utils\Jsons;

class BaseInstance implements \JsonSerializable
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
        $this->setStatus($status);
        $this->setPublishingDates($startPublishingDate, $endPublishingDate);
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
                        //echo "Creating attribute value with value:\n";
                        $valuesArray[]=$atri->createValue($value);
                    } else {
                        throw new \Exception("Invalid attribute $attributeKey in class ".$class->getKey()." creating Instance ".$key);
                    }
                }
            }
        }
        return self::createFromValuesArray($class, $key, $status, $valuesArray, $startPublishingDate, $endPublishingDate, $externalID);
    }

    public static function createFromJSONWithMetadata(BaseClass $class, string $jsonInstance): BaseInstance
    {
        $json=json_decode($jsonInstance, true);

        //print_r($json);

        $key=$json['key'];
        $status=$json['metadata']['status'];
        $startPublishingDate=$json['metadata']['startPublishingDate'];
        $endPublishingDate=$json['metadata']['endPublishingDate'];
        $externalID=$json['metadata']['externalID'];

        $values=[];
        foreach ($json['values'] as $atrikey=>$oneValue) {
            $values[]=[$atrikey=>$oneValue['value']];
        }
        return self::createFromJSON($class, $key, $status, json_encode($values), $startPublishingDate, $endPublishingDate, $externalID);
    }

    public function jsonSerialize()
    {
        $ret=$this->getInstanceHeaderData();
        $ret=$ret+$this->getInstanceMetadata();
        $ret['values']=Jsons::mapSerialize($this->values);
        $ret['relations']=Jsons::mapSerialize($this->relations);

        return $ret;
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
        $ret=$this->getInstanceHeaderData();
        if ($withMetadata) {
            $ret=$ret+$this->getInstanceMetadata();
        }
        foreach ($this->values as $value) {
            $data=$value->getData($language);
            if ($data) {
                $ret+=$data;
            }
        }
        return $ret;
    }

    public function getMultilanguageData($withMetadata=false): array
    {
        $ret=$this->getInstanceHeaderData();
        if ($withMetadata) {
            $ret=$ret+$this->getInstanceMetadata();
        }
        foreach ($this->values as $value) {
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
            , 'startPublishingDate'=>$this->startPublishingDate
            , 'endPublishingDate'=>$this->endPublishingDate
            , 'externalID'=>$this->externalID
            , 'class'=>$this->class->getKey()
            ]];
    }

    private function getInstanceHeaderData(): array
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

    private function validateStatus($status)
    {
        if ($status!='P' && $status!='V' && $status!='O') {
            throw new \Exception("Incorrect status $status for instance. Valid values are (O)k, (V)erified, (P)ending");
        }
    }

    public function setStatus($status)
    {
        $this->validateStatus($status);
        $this->status=$status;
    }

    public function isPublished($time=null)
    {
        if ($this->status=='P'||$this->status=='V') {
            return false;
        }

        // Status==O
        if ($time==null) {
            $time=time();
        }

        return ($this->getStartPublishingDateOr0()<=$time
        && $this->getEndPublishingDateOr3000()>=$time);
    }

    public function setStartPublishingDate($time=null)
    {
        if ($time!==null && $this->getEndPublishingDateOr3000()<=$time) {
            throw new \Exception("Cannot set start publication date after end publication date");
        }
        $this->startPublishingDate=$time;
    }

    public function setEndPublishingDate($time=null)
    {
        if ($time!==null && $this->getStartPublishingDateOr0()>=$time) {
            throw new \Exception("Cannot set end publication date before start publication date");
        }
        $this->endPublishingDate=$time;
    }

    public function setPublishingDates($startDate=null, $endDate=null)
    {
        if ($startDate!==null && $endDate!==null) {
            if ($startDate>$endDate) {
                throw new \Exception("Cannot set end publication date before start publication date");
            }
        }
        $this->startPublishingDate=$startDate;
        $this->endPublishingDate=$endDate;
    }

    private function getEndPublishingDateOr3000()
    {
        $endDate=32512176000; // year 3.000
        if ($this->endPublishingDate) {
            $endDate=$this->endPublishingDate;
        }
        return $endDate;
    }

    private function getStartPublishingDateOr0()
    {
        $startDate=0;
        if ($this->startPublishingDate) {
            $startDate=$this->startPublishingDate;
        }
        return $startDate;
    }

    public function put(string $id, CmsStorageInstanceInterface $storage)
    {
        $storage::put($id, $this);
    }

    public static function get(string $id, CmsStorageInstanceInterface $storage): BaseInstance
    {
        return $storage::get($id);
    }
}
