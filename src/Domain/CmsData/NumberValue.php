<?php

namespace Omatech\Editora\Domain\CmsData;

class NumberValue extends Value
{
    public function getValue()
    {
        return (int)parent::getValue();
    }

    public function validate(): bool
    {
        $value=parent::getValue();
        if (!\is_numeric($value)) {
            throw new \Exception("Value $value is not a number in attribute ".$this->attribute->getKey());
        }
        return true;
    }
}