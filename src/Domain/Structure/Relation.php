<?php

namespace Omatech\Editora\Domain\Structure;

use Omatech\Editora\Domain\Data\Instance;

class Relation
{
    private string $key;
    private array $children;

    public function __construct($key, $children)
    {
        assert(!empty($key));
        assert(!empty($children) && \is_array($children));
        $this->key=$key;

        foreach ($children as $child) {
            $this->children[]=$child;
        }
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

    public function toArray(): array
    {
        return [$this->getKey()=>$this->getChildrenKeys()];
    }

    public function isValid(Instance $instance)
    {
        $classKey=$instance->getClassKey();
        foreach ($this->children as $child) {
            if ($child==$classKey) {
                return true;
            }
        }
        return false;
    }
}
