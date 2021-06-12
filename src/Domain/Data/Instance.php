<?php

namespace Omatech\Editora\Domain\Data;

use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Domain\Structure\Relation;
use Omatech\Editora\Domain\Data\Link;

class Instance
{
    private Clazz $class;
    private string $key;
    private Publication $Publication;
    private array $values;
    private ?array $relations=null;
    private ?string $externalID=null;
    private ?string $storageID=null;
    private ?string $order='';

    private function __construct(Clazz $class, string $key, array $values=null, array $relations=null, Publication $Publication=null, $externalID=null, $storageID=null)
    {
        $this->class=$class;
        $this->key=$key;
        $this->externalID=$externalID;
        $this->storageID=$storageID;

        if (!$Publication) {
            $this->Publication=new Publication();
        } else {
            $this->Publication=$Publication;
        }

        if ($class->existRelations()) {
            $classRelations=$class->getRelations();
            if ($classRelations) {
                foreach ($classRelations as $relationKey=>$relation) {
                    $this->relations[$relationKey]=new Link($relation);
                }
            }
        }
    }

    public static function hydrateFromArray(Clazz $class, array $arr): Instance
    {
        return self::fromArray($class, $arr, true);
    }

    public static function fromArray(Clazz $class, array $arr, $hydrateOnly=false): Instance
    {
        assert(isset($arr['metadata']['key']));
        $key=$arr['metadata']['key'];
        $status=(isset($arr['metadata']['status']))?$arr['metadata']['status']:'O';
        $startPublishingDate=(isset($arr['metadata']['startPublishingDate']))?$arr['metadata']['startPublishingDate']:null;
        $endPublishingDate=(isset($arr['metadata']['endPublishingDate']))?$arr['metadata']['endPublishingDate']:null;
        $externalID=(isset($arr['metadata']['externalID']))?$arr['metadata']['externalID']:null;
        $storageID=(isset($arr['metadata']['ID']))?$arr['metadata']['ID']:null;

        $Publication=Publication::fromArray($arr);

        $values=(isset($arr['values']))?$arr['values']:null;
        $relations=(isset($arr['relations']))?$arr['relations']:null;

        $method='create';
        if ($hydrateOnly) {
            $method='hydrate';
        }

        return self::$method($class, $key, $values, $relations, $Publication, $externalID, $storageID);
    }

    public static function hydrate(Clazz $class, string $key, array $values=null, array $relations=null, Publication $Publication=null, $externalID=null, $storageID=null)
    {
        return self::create($class, $key, $values, $relations, $Publication, $externalID, $storageID, true);
    }

    public static function create(Clazz $class, string $key, array $values=null, array $relations=null, Publication $Publication=null, $externalID=null, $storageID=null, $hydrateOnly=false)
    {
        $method='createValue';
        if ($hydrateOnly) {
            $method='hydrateValue';
        }
        $valuesArray=[];
        $order='';
        if ($values) {
            foreach ($values as $attributeKey=>$value) {
                if ($class->existsAttribute($attributeKey)) {
                    $atri=$class->createAttributeFromKey($attributeKey);
                    $valuesArray[]=$atri->$method($value);
                } else {
                    throw new \Exception("Invalid attribute $attributeKey in class ".$class->getKey()." creating Instance ".$key);
                }
            }
        }

        $inst=new self($class, $key, null, null, $Publication, $externalID, $storageID);

        if ($valuesArray) {
            $inst->setValues($valuesArray);
        }

        if ($relations) {
            foreach ($relations as $relationKey=>$children) {
                foreach ($children as $id) {
                    $inst->addToRelationByKeyAndID($relationKey, $id, Link::BELOW);
                }
            }
        }
        return $inst->validate();
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function toArray()
    {
        return
        $this->getInstanceMetadata()
        +['values'=>$this->getValuesArray()]
        +['relations'=>$this->getRelationsArray()]
        ;
    }

    public function getValuesArray()
    {
        $ret=[];
        foreach ($this->values as $key=>$val) {
            $ret+=$val->toArray();
        }
        return $ret;
    }

    public function getRelationsArray()
    {
        $ret=[];
        if ($this->relations) {
            foreach ($this->relations as $key=>$rel) {
                $ret[$key]=$rel->toArray();
            }
        }
        return $ret;
    }

    public function getChildrenIDsByRelationKey($key): ?array
    {
        if ($this->relations) {
            if (isset($this->relations[$key])) {
                return $this->relations[$key]->toArray();
            }
        }
        return null;
    }

    public function addToRelation(Relation $relation, Instance $childInstance, $position=Link::ABOVE, string $otherID=null, bool $strict=false)
    {
        assert(!empty($relation) && !empty($childInstance));
        if ($relation->isValid($childInstance)) {
            if (isset($this->relations[$relation->getKey()])) {
                $this->relations[$relation->getKey()]->add($childInstance, $position, $otherID, $strict);
            } else {
                throw new \Exception("Trying to add a relation ".$relation->getKey()." to an instance that not have this relation!");
            }
        } else {
            throw new \Exception("Trying to add child Instance ".$childInstance->getKey()." of class ".$childInstance->getClass()->getKey()." to relation ".$relation->getKey());
        }
    }

    public function addToRelationByKey(string $key, Instance $child, $position=Link::ABOVE, string $otherID=null, bool $strict=false)
    {
        $relation=$this->getClass()->getRelationByKey($key);
        return $this->addToRelation($relation, $child, $position, $otherID, $strict);
    }

    private function addToRelationByKeyAndID(string $key, string $id, $position=Link::ABOVE, string $otherID=null, bool $strict=false)
    {
        $this->relations[$key]->addID($id, $position, $otherID, $strict);
    }

    public function removeFromRelationByKeyAndID(string $key, string $id, $strict=false)
    {
        $this->relations[$key]->removeID($id, $strict);
    }

    public function setValues(array $values)
    {
        assert(isset($values) && !empty($values) && is_array($values));
        foreach ($values as $value) {
            $attributeKey=$value->getFullyQualifiedKey();
            if ($this->class->existsAttribute($attributeKey)) {
                $this->values[]=$value;
                $atri=$this->class->getAttributeByKey($attributeKey);
                if ($atri->isOrderable())
                {
                    $this->order=$value->getValue();
                }
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

    public function getClass(): Clazz
    {
        return $this->class;
    }

    public function getIndexableData($language='ALL')
    {
        $ret=[];
        foreach ($this->values as $value) {
            $data=$value->getIndexableData($language);
            if ($data) {
                $ret+=$data;
            }
        }
        return $ret;        
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
          ['class'=>$this->class->getKey()
          , 'key'=>$this->key
        ]];

        $ret['metadata']+=$this->Publication->toArray();

        if (!empty($this->externalID)) {
            $ret['metadata']['externalID']=$this->externalID;
        }

        if (!empty($this->storageID)) {
            $ret['metadata']['ID']=$this->storageID;
        }

        return $ret;
    }

    public function validate(): Instance
    {
        if ($this->values) {
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
        if (!$this->values) {
            return false;
        }
        foreach ($this->values as $value) {
            if ($attribute->getKey()==$value->getKey()) {
                return false;
            }
        }
        return true;
    }

    public function setStatus($status)
    {
        return $this->Publication->setStatus($status);
    }

    public function isPublished($time=null)
    {
        return $this->Publication->isPublished($time);
    }

    public function setStartPublishingDate($time=null)
    {
        return $this->Publication->setStartPublishingDate($time);
    }

    public function setEndPublishingDate($time=null)
    {
        return $this->Publication->setEndPublishingDate($time);
    }

    public function setPublishingDates($startDate=null, $endDate=null)
    {
        return $this->Publication->setPublishingDates($startDate, $endDate);
    }

    public function hasID()
    {
        return ($this->storageID!==null);
    }

    public function ID()
    {
        return $this->storageID;
    }

    public function setID(string $id)
    {
        $this->storageID=$id;
    }
}
