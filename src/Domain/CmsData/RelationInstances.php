<?php

namespace Omatech\Editora\Domain\CmsData;

use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Utils\Jsons;

class RelationInstances
{
    private $relation;
    private $children;
    const ABOVE=0;
    const BELOW=1; 

    public function __construct(Relation $relation)
    {
        assert(!empty($relation));
        $this->relation=$relation;
    }

    public function add(Instance $child, $position=self::ABOVE, $otherID=null): void
    {
        assert(!empty($child));
        assert($child->hasID());
        $this->addID($child->ID(), $position, $otherID);
    }

    public function addID($id, $position=self::ABOVE, $otherID=null): void
    {
        if (!$this->children)
        {
            $position=self::BELOW;          
        }

        if ($position==self::ABOVE)
        {
            array_unshift($this->children, $id);
        }
        else
        {
            $this->children[]=$id;
        }
    }

    public function removeID($id, $silent=true)
    {
        $key = array_search($id, $this->children);
        if ($key !== false) {
            unset($this->children[$key]);
        }
        else
        {
            if (!$silent)
            {
                throw new \Exception("Trying to remove ID $id from the relation but not found!");
            }
        }
    }

    public function getChildren(): ?array
    {
        return $this->children;
    }

    public function toArray(): ?array
    {
        if (!$this->children) {return [];}
        return $this->children;
    }
}
