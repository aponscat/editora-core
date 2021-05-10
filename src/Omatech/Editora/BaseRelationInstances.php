<?php

namespace Omatech\Editora;

class BaseRelationInstances
{

    private BaseRelation $relation;
    private $children;

public __construct(BaseRelation $relation)
{
    assert(!empty($relation));
    $this->relation=$relation;
}

public function add($child)
{
    $children[]=$child;
}

}
