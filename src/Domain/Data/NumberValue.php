<?php

namespace Omatech\Editora\Domain\Data;

class NumberValue extends Value
{
    public function setValue($value)
    {
        return parent::setValue((int)$value);
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
