<?php

namespace Omatech\Editora\Domain\CmsData;

use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Utils\Jsons;

class RelationInstances
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
        assert($child->hasID());
        $this->children[]=$child->ID();
    }

    public function getChildren(): ?array
    {
        return $this->children;
    }
}
