<?php

namespace Omatech\Editora\Values;

class ReverseValue extends Value
{
    public function getValue()
    {
        return strrev(parent::getValue());
    }
}
