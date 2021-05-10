<?php

namespace Omatech\Editora\Structure;

class BaseRelation
{
    private string $key;
    private string $name;
    private array $children;

    public function __construct($key, $name, $children)
    {
        assert(!empty($key) && !empty($name));
        assert(!empty($children) && \is_array($children));
        $this->key=$key;
        $this->name=$name;

        foreach ($children as $child) {
            assert($child instanceof BaseClass);
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
}
