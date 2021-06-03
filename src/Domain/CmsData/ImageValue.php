<?php

namespace Omatech\Editora\Domain\CmsData;

class ImageValue extends Value
{
    private string $internalPath;
    private string $externalPath;
    private string $fileName;
    private string $base64Data;

    public function setValue($value)
    {
        assert(isset($value['original-filename']));
        //$mediaAdapter=$this->attribute->getMediaAdapter();
        $storagePath=$this->attribute->getStoragePath();
        $externalPath=$this->attribute->getPublicPath();
        $fileName=$value['original-filename'];
        /*while ($mediaAdapter::exists("$storagePath/$fileName")) {
            $fileName=rand(0, 1000).$fileName;
        }*/
 
        $this->internalPath="$storagePath/$fileName";
        $this->externalPath="$externalPath/$fileName";
        $this->fileName=$fileName;
        $this->base64Data=$value['data'];
        //$mediaAdapter::put($this->internalPath, base64_decode($this->base64Data));
        $this->value=$this->externalPath;
    }
}
