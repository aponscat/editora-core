<?php

namespace Omatech\Editora\Domain\CmsData;

class ReverseValue extends Value
{
    public function setValue($value)
    {
        return $this->value=strrev($value);
    }
}
