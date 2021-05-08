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

    private function __construct(BaseClass $class, $key, $status, $startPublishingDate=null, $endPublishingDate=null, $externalID=null)
    {
        $this->class=$class;
        $this->key=$key;
        $this->status=$status;
        $this->startPublishingDate=$startPublishingDate;
        $this->endPublishingDate=$endPublishingDate;
        $this->externalID=$externalID;
    }

    public static function createFromValuesArray(BaseClass $class, $key, $status, array $values=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null)
    {
        $inst=new self($class, $key, $status, $startPublishingDate, $endPublishingDate, $externalID);
        if ($values) {
            $inst->setValues($values);
        }
        return $inst->validate();
    }

    public static function createFromJSON(BaseClass $class, $key, $status, string $jsonValues=null, $startPublishingDate=null, $endPublishingDate=null, $externalID=null)
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

    public function setValues($values)
    {
        assert(isset($values) && !empty($values) && is_array($values));
        foreach ($values as $value) {
            $attributeKey=$value->getKey();
            if ($this->class->existsAttribute($attributeKey)) {
                $this->values[]=$value;
            } else {
                throw new \Exception("Invalid attribute $attributeKey in class ".$this->class->getKey()." creating Instance ".$this->getKey());
            }
        }
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getData($language='ALL', $withMetadata=false)
    {
        $ret=$this->getInstanceData();
        if ($withMetadata) {
            $ret=array_merge($ret, $this->getInstanceMetadata());
        }
        foreach ($this->values as $value) {
            assert($value instanceof BaseValue);
            $data=$value->getData($language);
            if ($data) {
                $ret=array_merge($ret, $data);
            }
        }
        return $ret;
    }

    private function getInstanceMetadata()
    {
        return
            [
      'status'=>$this->status
      ,'startPublishingDate'=>$this->startPublishingDate
      ,'endPublishingDate'=>$this->endPublishingDate
      ,'externalID'=>$this->externalID
      ];
    }

    private function getInstanceData()
    {
        return ['key'=>$this->key];
    }

    public function validate()
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

    private function isEmptyValueForAttribute(BaseAttribute $attribute)
    {
        foreach ($this->values as $value) {
            if ($attribute->getKey()==$value->getKey()) {
                return false;
            }
        }
        return true;
    }
}
