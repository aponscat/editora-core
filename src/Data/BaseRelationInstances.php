<?php

namespace Omatech\Editora\Data;

class BaseRelationInstances
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
}
