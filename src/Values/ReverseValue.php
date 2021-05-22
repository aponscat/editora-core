<?php

namespace Omatech\Editora\Values;

class ReverseValue extends BaseValue
{
    public function getValue()
    {
        //if (is_array(parent::getValue())) {echo"AAAA\n";print_r(parent::getValue());}
        return strrev(parent::getValue());
    }
}
