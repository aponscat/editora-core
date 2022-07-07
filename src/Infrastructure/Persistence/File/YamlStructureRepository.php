<?php

namespace Omatech\Editora\Infrastructure\Persistence\File;

use Omatech\Editora\Domain\Structure\Contracts\StructureRepositoryInterface;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Relation;

use Symfony\Component\Yaml\Yaml;

class YamlStructureRepository implements StructureRepositoryInterface
{
    public static function read(string $path): Structure
    {
        $yml = Yaml::parseFile($path);
        //print_r($yml);

        $structure=Structure::createEmptyStructure();
        foreach ($yml['languages'] as $language) {
            $structure->addLanguage($language);
        }

        $classArray=[];
        foreach ($yml['classes'] as $classKey=>$class) {
            $attributeArray=[];
            if (isset($class['attributes']))
            {
                foreach ($class['attributes'] as $attributeKey=>$attributeConfig) {
                    $valueType=null;
                    $attributeType='Omatech\Editora\Domain\Structure\Attribute';
                    $multiLang=true;
                    if ($attributeConfig) {
                        if (isset($attributeConfig['type'])) {
                            $attributeType=$attributeConfig['type'];
                            unset($attributeConfig['type']);
                        }
    
                        if (isset($attributeConfig['value-type'])) {
                            $valueType=$attributeConfig['value-type'];
                            unset($attributeConfig['value-type']);
                        }
    
                        if (isset($attributeConfig['multilang']) && $attributeConfig['multilang']==false) {
                            $multiLang=false;
                            unset($attributeConfig['multilang']);
                        }
                    }
    
                    //echo "$attributeKey -> ".print_r($attributeConfig, true).$multiLang."\n";
    
    
                    if ($multiLang) {
                        foreach ($structure->getLanguages() as $language) {
                            $attributeArray[]=new $attributeType("$attributeKey:$language", $attributeConfig, $valueType);
                        }
                    } else {
                        $attributeArray[]=new $attributeType($attributeKey, $attributeConfig, $valueType);
                    }
                }    
            }

            $classInstance=Clazz::createFromAttributesArray($classKey, $attributeArray);

            
            if (isset($class['relations'])) 
            {
              assert(is_array($class['relations']));
              foreach ($class['relations'] as $relationKey=>$children) {
                if (!is_array($children))
                {
                  $children=[$children];
                }
                  $classInstance->addRelation(new Relation($relationKey, $children));
              }
          }
          $classArray[]=$classInstance;


        }

        foreach ($classArray as $class) {
            $structure->addClass($class);
        }

        return $structure;
    }

    public static function write(Structure $structure, string $path): void
    {
        $res="languages:\n";
        foreach ($structure->getLanguages() as $language)
        {
            $res.="  - $language\n";
        }

        $unique_multilang_attributes=[];
        $unique_attributes=[];
        foreach ($structure->getClasses() as $class)
        {
            foreach ($class->getAttributes() as $attribute)
            {
                if($attribute->getLanguage()=='es')
                {
                    if (!isset($unique_multilang_attributes[$attribute->getKey()]))
                    {
                        $unique_multilang_attributes[$attribute->getKey()]=[];
                    }
                }
                if($attribute->getLanguage()=='ALL')
                {
                    if (!isset($unique_attributes[$attribute->getKey()]))
                    {
                        $unique_attributes[$attribute->getKey()]=[];
                    }
                }

            }
        }

        $res.="attributes:\n";
        foreach ($unique_attributes as $key=>$val)
        {
            $res.="  $key: &$key\n";
            $res.="    multilang: false\n";
        }

        $res.="classes:\n";
        foreach ($structure->getClasses() as $class)
        {
            $keyClass=$class->getKey();
            $res.="  $keyClass:\n";
            if ($class->getAttributes())
            {
                $res.="    attributes:\n";
                foreach ($class->getAttributes() as $attribute)
                {
                    if($attribute->getLanguage()=='ALL')
                    {
                        $res.="      ".$attribute->getKey().": *".$attribute->getKey()."\n";
                    }
    
                    if($attribute->getLanguage()=='es')
                    {
                        if ($class->existsAttribute($attribute->getKey()))
                        {
                            foreach ($structure->getLanguages() as $language)
                            {
                                $res.="      ".$attribute->getKey().":$language:\n";
                            }
                        }
                        else
                        {
                            $res.="      ".$attribute->getKey().":\n";
                        }
                    }
                }
    
            }

            if ($class->hasRelations())
            {
                $res.="    relations:\n";
                foreach ($class->getRelations() as $relation)
                {
                    $res.="      ".$relation->getKey().":\n";
                    foreach ($relation->getChildrenKeys() as $child)
                    {
                        $res.="        - $child\n";
                    }
                }    
            }

        }

        file_put_contents($path, $res);
    }
}
