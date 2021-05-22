<?php

namespace Omatech\Editora\Data;

use Omatech\Editora\Structure\BaseRelation;
use Omatech\Editora\Utils\Jsons;

class BaseRelationInstances implements \JsonSerializable
{
    private $relation;
    private $children;

    public function __construct(BaseRelation $relation)
    {
        assert(!empty($relation));
        $this->relation=$relation;
    }

    public function add(BaseInstance $child)
    {
        assert(!empty($child));
        $this->children[]=$child;
    }

    public function jsonSerialize()
    {
        $ret=[];
        $ret['relation']=Jsons::mapSerialize($this->relation);
        $ret['children']=Jsons::mapSerialize($this->children);

        return $ret;
    }
}
