<?php

namespace Omatech\Editora\Values;

use Omatech\Editora\Structure\BaseAttribute;

class ImageValue extends BaseValue
{

    public function setValue($value)
    {
        file_put_contents($this->attribute->getStoragePath().'/result.jpg', base64_decode($value));
        $this->value=$this->attribute->getPublicPath().'/result.jpg';
    }


}