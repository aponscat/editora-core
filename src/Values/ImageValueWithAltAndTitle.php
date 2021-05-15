<?php

namespace Omatech\Editora\Values;

use Omatech\Editora\Structure\BaseAttribute;
use Omatech\Editora\Utils\Strings;

class ImageValueWithAltAndTitle extends ImageValue
{
    private $internalPath;
    private $externalPath;
    private $subValues;

    public function __construct(BaseAttribute $attribute, $value=null)
    {
        parent::__construct($attribute, $value);
    }

    public function setValue($value)
    {
        parent::setValue($value);
        foreach ($value as $key=>$val) {
            //echo "comparando $key con ".$this->attribute->getKey()."\n";
            if (Strings::startsWith($key.'.', $this->attribute->getKey())) {
                //echo "Subvalue: $key\n";
                $this->subValues[Strings::substringAfter($key, '.')]=$val;
            }
        }
    }

    public function getData($language='ALL'): ?array
    {
        $res=parent::getData($language);
        if ($this->getSubValuesData($language)) {
            foreach ($this->getSubValuesData($language) as $key=>$subvalue) {
                $res+=[$this->attribute->getKey().'.'.$key=>$subvalue];
            }
        }
        return $res;
    }

    public function getSubValuesData($language='ALL'): ?array
    {
        if ($this->subValues) {
            $res=[];
            foreach ($this->subValues as $key=>$val) {
                if ($language!='ALL') {
                    if (stripos($key, ':')!==false) {// per idioma
                        $valueLanguage=Strings::substringAfter($key, ':');
                        if ($valueLanguage==$language) {
                            $res+=[Strings::substringBefore($key, ':')=>$val];
                        }
                    } else {
                        $res+=[$key=>$val];
                    }
                }
            }
            return $res;
        }
    }
}
