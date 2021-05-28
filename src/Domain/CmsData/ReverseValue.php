<?php

namespace Omatech\Editora\Domain\CmsData;

class ReverseValue extends Value
{
    public function getValue()
    {
        return strrev(parent::getValue());
    }
}
