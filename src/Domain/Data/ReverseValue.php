<?php

namespace Omatech\Editora\Domain\Data;

class ReverseValue extends Value
{
    public function setValue($value)
    {
        return $this->value=strrev($value);
    }
}
