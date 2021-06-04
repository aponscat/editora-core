<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Relation;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Infrastructure\Persistence\File\StructureRepository;
use Omatech\Editora\Application\Cms;

class ReverseEngineerToYamlTest extends TestCase
{

    public function testLoadOfFile(): void
    {
        include_once(dirname(__FILE__).'/../data/editora_reverse_enginereed_test.php');
        //print_r($data);

        $languages=self::parseLanguages($data);

        $classes_list=[];
        if ($data['classes']) {
            foreach ($data['classes'] as $group=>$classes_array) {
                foreach ($classes_array as $id=>$labels) {
                    $classes_list[$id]=$labels[0];
                }
            }
        }

        $attributes=self::getAllAttributesFromStructureArray($data, $languages);

        $relationsClasses=[];
        if ($data['relations']) {
            foreach ($data['relations'] as $id=>$relation_parent_and_children) {
                $ids_array=explode(',', $relation_parent_and_children);
                $relationsClasses[(int)$id]=['parent'=>array_shift($ids_array), 'children'=>$ids_array];
            }
        }

        $relationsList=[];
        if ($data['relation_names']) {
            foreach ($data['relation_names'] as $id=>$relation_array) {
                $relation_name=$relation_array[0];
                $relation_key=$relation_array[1];
                $relationsList[(int)$id]=['name'=>$relation_name, 'key'=>$relation_key]+$relationsClasses[$id];
            }
        }
        // TBD
        // groups
        // mandatory class_attributes

        $classes=[];
        foreach ($classes_list as $id=>$className) {
            $classAttris=[];
            $attributesInClass=self::getAttributesInClass($data, $id);

            foreach ($attributesInClass as $attributeId) {
                $classAttris+=self::getAttributesFromId($attributes, $languages, $attributeId);
            }

            $classInstance=Clazz::createFromAttributesArray($className, $classAttris);
            $classes[$id]=$classInstance;
        }

        foreach ($relationsList as $id=>$relation) {
            $children=[];
            foreach ($relation['children'] as $childrenClassId) {
                $children[]=$classes[$childrenClassId]->getKey();
            }

            $classes[$relation['parent']]->addRelation(new Relation($relation['key'], $children));
        }

        $this->assertTrue(true);
        $structure=Structure::createEmptyStructure();
        foreach ($languages as $language)
        {
            $structure->addLanguage($language);
        }

        foreach ($classes as $class)
        {
            $structure->addClass($class);
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

        $res="languages:\n";
        foreach ($structure->getLanguages() as $language)
        {
            $res.="  - $language\n";
        }

        $res.="attributes:\n";
        foreach ($unique_attributes as $key=>$val)
        {
            $res.="  $key: &$key\n";
            $res.="    multilang: false\n";
        }
        /*
        foreach ($unique_multilang_attributes as $key=>$val)
        {
            $res.="  $key: &$key\n";
        }
        */
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

        file_put_contents(dirname(__FILE__).'/../data/editora_reverse_enginereed_test.yml', $res);
        //echo $res;
        $structure=StructureRepository::read(dirname(__FILE__).'/../data/editora_reverse_enginereed_test.yml');
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);


    }

    private static function parseLanguages($structure)
    {
        $languages=[];
        if ($structure['languages']) {
            foreach ($structure['languages'] as $id=>$language) {
                $languages[(int)$id]=$language;
            }
        }
        return $languages;
    }


    public static function loadStructureFromJSON($jsonStructure)
    {
        $structure=json_decode($jsonStructure, true);
        $languages=self::parseLanguages($structure);

        $classes=[];
        foreach ($structure['classes'] as $key=>$class) {
            $classInstance=Clazz::createFromJSON(
                $key,
                json_encode($class['attributes']),
                (isset($class['relations']))?json_encode($class['relations']):null
            );
            $classes[]=$classInstance;
        }

        return new self($languages, $classes);
    }



    public function serializeClasses()
    {
        $res=[];
        foreach ($this->classes as $class) {
            $res[$class->getKey()]=$class->toArray()[$class->getKey()];
        }
        return $res;
    }

    private static function getAllAttributesFromStructureArray($structure, $languages)
    {

        // TBD
        // lookups
        // images and multilang_images
        $attributes=[];
        $attributes+=self::loadAttributes($structure, 'attributes_string');
        $attributes+=self::loadAttributes($structure, 'attributes_textarea');
        $attributes+=self::loadAttributes($structure, 'attributes_text');
        $attributes+=self::loadAttributes($structure, 'attributes_date');
        $attributes+=self::loadAttributes($structure, 'attributes_num');
        $attributes+=self::loadAttributes($structure, 'attributes_geolocation');
        $attributes+=self::loadAttributes($structure, 'attributes_url');
        $attributes+=self::loadAttributes($structure, 'attributes_file');
        $attributes+=self::loadAttributes($structure, 'attributes_video');
        $attributes+=self::loadAttributes($structure, 'attributes_image');
        $attributes+=self::loadAttributes($structure, 'attributes_multi_lang_string', $languages);
        $attributes+=self::loadAttributes($structure, 'attributes_multi_lang_textarea', $languages);
        $attributes+=self::loadAttributes($structure, 'attributes_multi_lang_file', $languages);
        return $attributes;
    }


    private static function getAttributesFromId($atris, $languages, $attributeId)
    {
        $res=[];
        if (isset($atris[$attributeId])) {
            $res[$attributeId]=$atris[$attributeId];
        }
        foreach ($languages as $languageId=>$language) {
            if (isset($atris[(int)$languageId+(int)$attributeId])) {
                $res[(int)$languageId+(int)$attributeId]=$atris[(int)$languageId+(int)$attributeId];
            }
        }
        return $res;
    }

    private static function getAttributesInClass($structure, $id)
    {
        $res=[];
        if (!isset($structure['attributes_classes'][(string)$id])) {
            return $res;
        }
        $attributes=(string)$structure['attributes_classes'][(string)$id];
        $attributesArray=explode(',', $attributes);
        foreach ($attributesArray as $oneAttributeExpression) {
            if (stripos($oneAttributeExpression, '-')) {
                $multiAttributeArray=explode('-', $oneAttributeExpression);
                foreach ($multiAttributeArray as $oneAttribute) {
                    $res[$oneAttribute]=1;
                }
            } else {
                $res[$oneAttributeExpression]=1;
            }
        }
        return array_keys($res);
    }


    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[]=$attribute;
    }

    private static function loadAttributes($structure, $attributeType, $languages=[])
    {
        $atris=[];
        if (isset($structure[$attributeType])) {
            foreach ($structure[$attributeType] as $id=>$stringAttribute) {
                if ($languages) {
                    foreach ($languages as $languageId=>$language) {
                        $atri=new Attribute($stringAttribute[0].":$language");
                        $atris[$languageId+$id]=$atri;
                    }
                } else {
                    $atri=new Attribute($stringAttribute[0]);
                    $atris[$id]=$atri;
                }
            }
        }
        return $atris;
    }



        
}