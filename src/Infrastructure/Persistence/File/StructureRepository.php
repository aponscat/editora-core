<?php

namespace Omatech\Editora\Infrastructure\Persistence\File;

use Omatech\Editora\Domain\Structure\Contracts\StructureRepositoryInterface;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Relation;

use Symfony\Component\Yaml\Yaml;

class StructureRepository implements StructureRepositoryInterface
{
    public static function read($resource): Structure
    {
        $yml = Yaml::parseFile($resource);
        //print_r($yml);

        $structure=Structure::createEmptyStructure();
        foreach ($yml['languages'] as $language) {
            $structure->addLanguage($language);
        }

        $classArray=[];
        foreach ($yml['classes'] as $classKey=>$class) {
            $attributeArray=[];
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
                    
                    if (isset($attributeConfig['subattributes'])) {
                        foreach ($attributeConfig['subattributes'] as $subattributeKey=>$subattributeConfig) {
                            $subvalueType=null;
                            $subattributeType='Omatech\Editora\Domain\Structure\Attribute';
                            //echo "$subattributeKey -> ".print_r($subattributeConfig, true)."\n";
                            $subattributeMultiLang=false;

                            if (isset($subattributeConfig['type'])) {
                                $subattributeType=$subattributeConfig['type'];
                                unset($subattributeConfig['type']);
                            }

                            if (isset($subattributeConfig['value-type'])) {
                                $subvalueType=$subattributeConfig['value-type'];
                                unset($subattributeConfig['value-type']);
                            }

                            if (!isset($subattributeConfig['multilang']) || $subattributeConfig['multilang']==true) {
                                $subattributeMultiLang=true;
                                unset($subattributeConfig['multilang']);
                            }

                            if ($subattributeMultiLang) {
                                foreach ($structure->getLanguages() as $language) {
                                    $attributeConfig['subattributes'][]=
                                                      ["key"=>"$subattributeKey:$language"
                                                      , "config"=>$subattributeConfig
                                                      , "valueType"=>$subvalueType];
                                }
                            } else {
                                $attributeConfig['subattributes'][]=
                                                  ["key"=>"$subattributeKey"
                                                  , "config"=>$subattributeConfig
                                                  , "valueType"=>$subvalueType];
                            }

                            unset($attributeConfig['subattributes'][$subattributeKey]);
                        }
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

    public static function write($resource, Structure $structure): void
    {
    }
}
