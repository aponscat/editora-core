<?php

namespace Omatech\Editora;

class StrangeAttribute extends BaseAttribute
{
    public function getKey(): string
    {
        return $this->key.'-is-strange';
    }
}
