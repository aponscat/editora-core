<?php

namespace Omatech\Editora\Domain\CmsData;

use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Utils\Jsons;

class RelationInstances
{
    private Relation $relation;
    private ?array $children=null;
    
    const ABOVE=0;
    const BELOW=1; 

    public function __construct(Relation $relation)
    {
        assert(!empty($relation));
        $this->relation=$relation;
    }

    public function add(Instance $child, $position=self::ABOVE, $otherID=null, bool $strict=false): void
    {
        assert(!empty($child));
        assert($child->hasID());
        $this->addID($child->ID(), $position, $otherID, $strict);
    }

    private function addIDAtBeggining(string $id)
    {
        if (!$this->children)
        {
            $this->addIDAtEnd($id);
        }
        else
        {
            array_unshift($this->children, $id);
        }
    }

    private function addIDAtEnd(string $id)
    {
        $this->children[]=$id;
    }

    private function addIDInPosition(string $id, int $position)
    {
        array_splice($this->children, $position, 0, $id);
    }


    public function addID(string $id, $position=self::ABOVE, string $otherID=null, bool $strict=false): void
    {
        if (!$otherID)
        {
            if ($position==self::ABOVE)
            {
                $this->addIDAtBeggining($id);
            }
            else
            {
                $this->addIDAtEnd($id);
            }
        }
        else
        {
            $otherIDPosition=array_search($otherID, $this->children);
            if ($otherIDPosition===false)
            {
                if ($strict)
                {
                    throw new \Exception("Trying position $id relative to an ID that is not in array $otherID!");
                }
                else
                {
                    $this->addID($id, $position);
                }
            }
            else
            {
                if ($otherIDPosition==0 && $position==self::ABOVE)
                {
                    $this->addIDAtBeggining($id);
                }
                else
                {// $otherIDPosition > 0 
                    if ($position==self::ABOVE)
                    {
                        $this->addIDInPosition($id, $otherIDPosition);
                    }
                    else
                    {
                        $this->addIDInPosition($id, $otherIDPosition+1);
                    }
                }    
            }
        }
    }

    public function removeID($id, $strict=false)
    {
        $key = array_search($id, $this->children);
        if ($key !== false) {
            array_splice($this->children, $key, 1);
        }
        else
        {
            if ($strict)
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
