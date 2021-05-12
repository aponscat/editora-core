<?php

namespace Omatech\Editora\Values;

use Omatech\Editora\Structure\BaseAttribute;

class ImageValue extends BaseValue
{
    private $mediaManager;

    public function __construct(BaseAttribute $attribute, $value=null, MediaAdapterInterface $mediaAdapter)
    {
        parent::__construct($attribute, $value);
        $this->mediaAdapter=$mediaAdapter;
    }

    public function setValue($value)
    {
        $this->mediaManager->put($this->attribute->getStoragePath().'/result.jpg', base64_decode($value));
        $this->value=$this->attribute->getPublicPath().'/result.jpg';
    }
}
