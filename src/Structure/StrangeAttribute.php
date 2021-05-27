<?php

namespace Omatech\Editora\Structure;

class StrangeAttribute extends Attribute
{
    public function getKey(): string
    {
        return $this->key.'-is-strange';
    }
}
