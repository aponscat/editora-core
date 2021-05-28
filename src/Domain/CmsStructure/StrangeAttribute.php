<?php

namespace Omatech\Editora\Domain\CmsStructure;

class StrangeAttribute extends Attribute
{
    public function getKey(): string
    {
        return $this->key.'-is-strange';
    }
}
