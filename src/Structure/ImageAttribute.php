<?php

namespace Omatech\Editora\Structure;

use Omatech\Editora\Values\BaseValue;
use Omatech\Editora\Adapters\MediaAdapterInterface;

class ImageAttribute extends BaseAttribute implements \JsonSerializable
{
    protected $width=null;
    protected $height=null;
    protected $storagePath='/tmp';
    protected $publicPath='/uploads/images';
    protected $folderPattern='Ymd';

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

    public function jsonSerialize()
    {
        $res=parent::jsonSerialize();
        $res[$this->getKey()]['width']=$this->width;
        $res[$this->getKey()]['height']=$this->height;
        $res[$this->getKey()]['storagePath']=$this->storagePath;
        $res[$this->getKey()]['publicPath']=$this->publicPath;
        return $res;
    }

    /*
    public function getMediaAdapter(): MediaAdapterInterface
    {
        return new $this->adapters['media'];
    }
    */
    
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
