<?php

namespace Omatech\Editora\Domain\Structure;

class StrangeAttribute extends Attribute
{
    public function getKey(): string
    {
        return $this->key.'-is-strange';
    }
}
