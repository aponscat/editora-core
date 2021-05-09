<?php

namespace Omatech\Editora;

class BaseRelation
{
    private string $key;
    private string $name;
    private array $children;

    public function __construct($key, $name, $children)
    {
        assert(!empty($key) && !empty($name));
        $this->key=$key;
        $this->name=$name;

        foreach ($children as $child) {
            assert($child instanceof BaseClass);
            $this->children[]=$child;
        }
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getChildren()
    {
        return $this->children;
    }
}
