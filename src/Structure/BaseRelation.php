<?php

namespace Omatech\Editora\Structure;

use Omatech\Editora\Data\BaseInstance;

class BaseRelation implements \JsonSerializable
{
    private $key;
    //private $name;
    private $children;

    public function __construct($key, $children)
    {
        assert(!empty($key));
        assert(!empty($children) && \is_array($children));
        $this->key=$key;
        //$this->name=$name;

        foreach ($children as $child) {
            //assert($child instanceof BaseClass);
            $this->children[]=$child;
        }
    }

    public function jsonSerialize()
    {
        $res=[$this->getKey()=>['children'=>$this->getChildrenKeys()]];
        return $res;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChildrenKeys(): array
    {
        foreach ($this->children as $key=>$child) {
            $res[]=$child;
        }
        return $res;
    }

    public function isValid(BaseInstance $instance)
    {
        $classKey=$instance->getClassKey();
        foreach ($this->children as $child)
        {
            if ($child==$classKey) {
                return true;
            }
        }
        return false;
    }

}
