<?php

namespace Omatech\Editora;

class ReverseValue extends BaseValue
{
    public function getValue()
    {
        return strrev(parent::getValue());
    }
}
