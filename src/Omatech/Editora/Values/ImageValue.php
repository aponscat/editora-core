<?php

namespace Omatech\Editora\Values;

use Omatech\Editora\Structure\BaseAttribute;

class ImageValue extends BaseValue
{
    private $internalPath;
    private $externalPath;

    public function __construct(BaseAttribute $attribute, $value=null)
    {
        parent::__construct($attribute, $value);
    }

    public function setValue($value)
    {
        $this->internalPath=$this->attribute->getStoragePath().'/'.$value['original-filename'];
        $this->externalPath=$this->attribute->getPublicPath().'/'.$value['original-filename'];
        $this->attribute->getMediaAdapter()::put($this->internalPath, base64_decode($value['data']));
        $this->value=$this->externalPath;
    }
}
