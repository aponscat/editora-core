<?php

namespace Omatech\Editora\Domain\CmsData;

use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Infrastructure\Persistence\ArrayStorageAdapter;
use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Domain\CmsData\RelationInstances;

class Instance
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

    public static function fromArray(Clas $class, array $arr): Instance 
    {
        assert(isset($arr['metadata']['key']));
        $key=$arr['metadata']['key'];
        $status=(isset($arr['metadata']['status']))?$arr['metadata']['status']:'O';
        $startPublishingDate=(isset($arr['metadata']['startPublishingDate']))?$arr['metadata']['startPublishingDate']:null;
        $endPublishingDate=(isset($arr['metadata']['endPublishingDate']))?$arr['metadata']['endPublishingDate']:null;
        $externalID=(isset($arr['metadata']['externalID']))?$arr['metadata']['externalID']:null;
        $storageID=(isset($arr['metadata']['ID']))?$arr['metadata']['ID']:null;

        return self::create($class, $key, $status, $arr['values'], $startPublishingDate, $endPublishingDate, $externalID, $storageID);
    }

    public static function create(Clas $class, string $key, string $status, array $values=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null, $storageID=null)
    {
        $valuesArray=[];
        if ($values) {
            foreach ($values as $attributeKey=>$value) {
                    if ($class->existsAttribute($attributeKey)) {
                        $atri=$class->createAttributeFromKey($attributeKey);
                        $valuesArray[]=$atri->createValue($value);
                    } else {
                        throw new \Exception("Invalid attribute $attributeKey in class ".$class->getKey()." creating Instance ".$key);
                    }
                }
        }

        $inst=new self($class, $key, $status, $startPublishingDate, $endPublishingDate, $externalID, $storageID);
        if ($valuesArray) {
            $inst->setValues($valuesArray);
        }
        return $inst->validate();
    }

    public function toArray()
    {
        return 
        $this->getInstanceMetadata()
        +['values'=>$this->getValuesArray()];
    }

    public function getValuesArray()
    {
        $ret=[];
        foreach ($this->values as $key=>$val)
        {
            $ret+=$val->toArray();
        }
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
        if ($this->values)
        {
            foreach ($this->values as $value) {
                $value->validate();
            }    
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
        if (!$this->values) {return false;}
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

        // POST Cond: Status==O
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
