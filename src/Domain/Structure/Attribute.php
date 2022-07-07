<?php

namespace Omatech\Editora\Domain\Structure;

use Omatech\Editora\Domain\Data\Value;
use Omatech\Editora\Utils\Strings;
use Omatech\Editora\Utils\Jsons;

class Attribute
{
    protected string $key;
    protected string $language='ALL';
    protected bool $mandatory=false;
    protected bool $indexable=true;
    protected bool $orderable=false;
    protected string $valueType='Omatech\Editora\Domain\Data\Value';
    protected string $langSeparator=':';
    private ?array $config=null;

    public function __construct($key, $config=null, $valueType=null)
    {
        $language='ALL';
        if (stripos($key, $this->langSeparator)!==false) {// one lang
            $language=Strings::substringAfter($key, $this->langSeparator);
            $key=Strings::substringBefore($key, $this->langSeparator);
        }
        assert(strlen($language)==2||$language=='ALL');
        $this->language=$language;

        $this->key=$key;
        $this->config=$config;

        if ($valueType!==null) {
            if (class_exists($valueType)) {
                $this->valueType=$valueType;
            } else {
                throw new \Exception("Invalid value type $valueType class not found for attribute $key");
            }
        }

        if (isset($config['mandatory'])) {
            assert(is_bool($config['mandatory']));
            $this->mandatory=$config['mandatory'];
        }

        if (isset($config['indexable'])) {
            assert(is_bool($config['indexable']));
            $this->indexable=$config['indexable'];
        }

        if (isset($config['orderable'])) {
            assert(is_bool($config['orderable']));
            $this->orderable=$config['orderable'];
        }
    }

    public function isIndexable()
    {
        return $this->indexable;
    }

    public function isOrderable()
    {
        return $this->indexable;
    }

    public function availableInLanguage($language='ALL')
    {
        if ($language=='ALL')
        {
            return true;
        }
        $attributeLanguage=$this->getLanguage();
        if ($attributeLanguage=='ALL') {
            return true;
        } else {
            if ($language!='ALL') {
                if ($attributeLanguage==$language) {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }
    
    public function toArray()
    {
        $res['key']=$this->getFullyQualifiedKey();
        if ($this->config) {
            $res['config']=$this->config;
        }

        if (!$this->valueType=='Omatech\Editora\Domain\Data\Value') {
            $res['valueType']=$this->valueType;
        }
        return $res;
    }

    public function getData(): array
    {
        return $this->toArray();
    }

    public function createValue($value): Value
    {
        $class=$this->valueType;
        return new $class($this, $value);
    }

    public function hydrateValue($value)
    {
        $class=$this->valueType;
        return $class::hydrate($this, $value);
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getFullyQualifiedKey(): string
    {
        if ($this->language!='ALL') {
            return $this->getKey().":".$this->language;
        }
        return $this->getKey();
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
