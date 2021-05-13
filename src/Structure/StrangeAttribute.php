<?php

namespace Omatech\Editora\Structure;

class StrangeAttribute extends BaseAttribute
{
    public function getKey(): string
    {
        return $this->key.'-is-strange';
    }
}
