<?php

namespace Omatech\Editora\Data;

use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Data\RelationInstances;
use Omatech\Editora\Utils\Jsons;

class Instance implements \JsonSerializable
{
    private $class;
    private $key;
    private $status;
    private $startPublishingDate;
    private $endPublishingDate;
    private $externalID;
    private $values;
    private $relations;
    private $storageID=null;

    private function __construct(Clas $class, string $key, string $status, $startPublishingDate=null, $endPublishingDate=null, $externalID=null, $storageID=null)
    {
        $this->class=$class;
        $this->key=$key;
        $this->setStatus($status);
        $this->setPublishingDates($startPublishingDate, $endPublishingDate);
        $this->externalID=$externalID;
        $this->storageID=$storageID;

        if ($class->existRelations()) {
            $classRelations=$class->getRelations();
            if ($classRelations) {
                foreach ($classRelations as $relationKey=>$relation) {
                    $this->relations[$relationKey]=new RelationInstances($relation);
                }
            }
        }
    }

    public static function createFromValuesArray(Clas $class, string $key, string $status, array $values=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null, $storageID=null)
    {
        $inst=new self($class, $key, $status, $startPublishingDate, $endPublishingDate, $externalID, $storageID);
        if ($values) {
            $inst->setValues($values);
        }
        return $inst->validate();
    }

    public static function createFromJSON(Clas $class, string $key, string $status, string $jsonValues=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null, $storageID=null)
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
                        $valuesArray[]=$atri->createValue($value);
                    } else {
                        throw new \Exception("Invalid attribute $attributeKey in class ".$class->getKey()." creating Instance ".$key);
                    }
                }
            }
        }
        return self::createFromValuesArray($class, $key, $status, $valuesArray, $startPublishingDate, $endPublishingDate, $externalID, $storageID);
    }

    public static function createFromJSONWithMetadata(Clas $class, string $jsonInstance): Instance
    {
        $json=json_decode($jsonInstance, true);
        $key=$json['metadata']['key'];
        $status=$json['metadata']['status'];
        $startPublishingDate=(isset($json['metadata']['startPublishingDate']))?$json['metadata']['startPublishingDate']:null;
        $endPublishingDate=(isset($json['metadata']['endPublishingDate']))?$json['metadata']['endPublishingDate']:null;
        $externalID=(isset($json['metadata']['externalID']))?$json['metadata']['externalID']:null;
        $storageID=(isset($json['metadata']['ID']))?$json['metadata']['ID']:null;

        $values=json_encode($json['values']);
        return self::createFromJSON($class, $key, $status, $values, $startPublishingDate, $endPublishingDate, $externalID, $storageID);
    }

    private function serializeValues()
    {
        if (!$this->values) {
            return null;
        }
        $res=[];
        foreach ($this->values as $value) {
            $res[]=$value->jsonSerialize();
        }
        return $res;
    }

    public function jsonSerialize()
    {
        $ret=$this->getInstanceHeaderData();
        $ret=$ret+$this->getInstanceMetadata();
        $ret['values']=$this->serializeValues();
        $ret['relations']=Jsons::mapSerialize($this->relations);

        return $ret;
    }

    public function addToRelation(Relation $relation, Instance $childInstance)
    {
        assert(!empty($relation) && !empty($childInstance));
        if ($relation->isValid($childInstance)) {
            if (isset($this->relations[$relation->getKey()])) {
                $this->relations[$relation->getKey()]->add($childInstance);
            } else {
                throw new \Exception("Trying to add a relation ".$relation->getKey()." to an instance that not have this relation!");
            }
        } else {
            throw new \Exception("Trying to add child Instance ".$childInstance->getKey()." of class ".$childInstance->getClass()->getKey()." to relation ".$relation->getKey());
        }
    }

    public function addToRelationByKey(string $key, Instance $child)
    {
        $relation=$this->getClass()->getRelationByKey($key);
        return $this->addToRelation($relation, $child);
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

    public function getClassKey(): string
    {
        return $this->class->getKey();
    }

    public function getClass(): Clas
    {
        return $this->class;
    }

    public function getData($language='ALL', $withMetadata=false): array
    {
        //$ret=$this->getInstanceHeaderData();
        $ret=[];
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
        $ret=[];
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
        $ret=
        ['metadata'=>
          ['status'=>$this->status
          , 'class'=>$this->class->getKey()
          , 'key'=>$this->key
        ]];

        if (!empty($this->startPublishingDate)) {
            $ret['metadata']['startPublishingDate']=$this->startPublishingDate;
        }

        if (!empty($this->endPublishingDate)) {
            $ret['metadata']['endPublishingDate']=$this->endPublishingDate;
        }

        if (!empty($this->externalID)) {
            $ret['metadata']['externalID']=$this->externalID;
        }

        if (!empty($this->storageID)) {
            $ret['metadata']['ID']=$this->storageID;
        }

        return $ret;
    }

    private function getInstanceHeaderData(): array
    {
        return ['key'=>$this->key];
    }

    public function validate(): Instance
    {
        foreach ($this->values as $value) {
            $value->validate();
        }

        foreach ($this->class->getAttributes() as $attribute) {
            assert($attribute instanceof Attribute);
            if ($attribute->isMandatory() && $this->isEmptyValueForAttribute($attribute)) {
                throw new \Exception("Mandatory value missing for attribute ".$attribute->getKey());
            }
        }
        return $this;
    }

    private function isEmptyValueForAttribute(Attribute $attribute): bool
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

    public function hasID()
    {
        return ($this->storageID!==null);
    }

    public function ID()
    {
        return $this->storageID;
    }

    public function putIfNotExists(CmsStorageInstanceInterface $storage)
    {
        if (!$this->hasID()) {
            return $this->put($storage);
        }

        if ($storage->exists($this->ID())) {
            return $this->ID();
        }
        
        return $this->put($storage);
    }

    public function put(CmsStorageInstanceInterface $storage): string
    {
        $id=uniqid();
        if ($this->hasID()) {
            $id=$this->ID();
        }
        $this->storageID=$id;
        $storage::put($id, $this);
        if ($this->relations) {
            foreach ($this->relations as $relationInstances) {
                if ($relationInstances->getChildren()) {
                    foreach ($relationInstances->getChildren() as $child) {
                        $child->putIfNotExists($storage);
                    }
                }
            }
        }
        return $id;
    }

    public static function get(string $id, CmsStorageInstanceInterface $storage): Instance
    {
        return $storage::get($id);
    }
}
