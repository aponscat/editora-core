<?php

namespace Omatech\Editora;

class BaseAttribute
{
    private string $key;
    private string $language='ALL';
    private bool $mandatory=false;

    public function __construct($key, $config=null)
    {
        $this->key=$key;
        if ($config==null) {
            $language='ALL';
            $mandatory=false;
        }

        if (isset($config['language'])) {
            assert(strlen($config['language']==2));
            $this->language=$config['language'];
        }
        if (isset($config['mandatory'])) {
            assert(is_bool($config['mandatory']));
            $this->mandatory=$config['mandatory'];
        }
    }

    public function getData()
    {
        return [$this->key=>
        ['language'=>$this->language
        ,'mandatory'=>$this->mandatory
        ]];
    }

    public function isMandatory()
    {
        return $this->mandatory;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
