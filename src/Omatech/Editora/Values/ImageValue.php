<?php

namespace Omatech\Editora\Values;

use Omatech\Editora\Structure\BaseAttribute;

class ImageValue extends BaseValue
{
    public function __construct(BaseAttribute $attribute, $value=null)
    {
        parent::__construct($attribute, $value);
    }

    public function setValue($value)
    {
        $this->attribute->getMediaAdapter()::put($this->attribute->getStoragePath().'/result.jpg', base64_decode($value));
        $this->value=$this->attribute->getPublicPath().'/result.jpg';
    }
}
