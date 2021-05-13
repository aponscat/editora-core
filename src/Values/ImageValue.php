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
        $mediaAdapter=$this->attribute->getMediaAdapter();
        $storagePath=$this->attribute->getStoragePath();
        $externalPath=$this->attribute->getPublicPath();
        $fileName=$value['original-filename'];
        while ($mediaAdapter::exists("$storagePath/$fileName")) {
            $fileName=rand(0, 1000).$fileName;
        }

        $this->internalPath="$storagePath/$fileName";
        $this->externalPath="$externalPath/$fileName";
        $mediaAdapter::put($this->internalPath, base64_decode($value['data']));
        $this->value=$this->externalPath;
    }
}
