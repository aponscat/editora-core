<?php

namespace Omatech\Editora\Domain\CmsStructure;

use Omatech\Editora\Domain\CmsData\Value;
use Omatech\Editora\Adapters\MediaAdapterInterface;

class ImageAttribute extends Attribute
{
    protected ?int $width=null;
    protected ?int $height=null;
    protected string $storagePath='/tmp';
    protected string $publicPath='/uploads/images';
    protected string $folderPattern='Ymd';

    public function __construct($key, $config, $valueType)
    {
        assert(!empty($config) && !empty($valueType));

        parent::__construct($key, $config, $valueType);
        if (isset($config['storage-path'])) {
            $this->storagePath=$config['storage-path'];
        }

        if (isset($config['public-path'])) {
            $this->publicPath=$config['public-path'];
        }

        if (isset($config['folder-pattern'])) {
            $this->folderPattern=$config['folder-pattern'];
        }

        if (isset($config['dimensions'])) {
            assert(stripos($config['dimensions'], 'x')!==false);
            $dimensionsArray=explode('x', $config['dimensions']);
            if (!empty($dimensionsArray[0])) {
                assert(is_numeric($dimensionsArray[0]));
                $this->width=$dimensionsArray[0];
            }
            if (!empty($dimensionsArray[1])) {
                assert(is_numeric($dimensionsArray[1]));
                $this->height=$dimensionsArray[1];
            }
        }
    }
    
    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getStoragePath()
    {
        return $this->storagePath.'/'.date_format(date_create(), $this->folderPattern);
    }

    public function getPublicPath()
    {
        return $this->publicPath.'/'.date_format(date_create(), $this->folderPattern);
    }

    public function getDimensions()
    {
        return $this->width.'x'.$this->height;
    }
}
