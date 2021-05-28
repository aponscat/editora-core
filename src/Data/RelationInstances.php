<?php

namespace Omatech\Editora\Data;

use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Utils\Jsons;

class RelationInstances implements \JsonSerializable
{
    private $relation;
    private $children;

    public function __construct(Relation $relation)
    {
        assert(!empty($relation));
        $this->relation=$relation;
    }

    public function add(Instance $child)
    {
        assert(!empty($child));
        $this->children[]=$child;
    }

    public function getChildren(): ?array
    {
        return $this->children;
    }

    public function jsonSerialize()
    {
        $ret=[];
        $ret['relation']=Jsons::mapSerialize($this->relation);
        $ret['children']=Jsons::mapSerialize($this->children);

        return $ret;
    }
}
