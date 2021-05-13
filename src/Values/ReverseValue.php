<?php

namespace Omatech\Editora\Values;

class ReverseValue extends BaseValue
{
    public function getValue()
    {
        return strrev(parent::getValue());
    }
}
