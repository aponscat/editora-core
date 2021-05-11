<?php

namespace Omatech\Editora\Structure;

class CmsStructure implements \JsonSerializable
{
    private $attributes;
    private $classes;
    private $languages;

    private function __construct($languages, $attributes, $classes)
    {
        $this->languages=$languages;
        $this->attributes=$attributes;
        $this->classes=$classes;
    }

    public function jsonSerialize()
    {
        $res=['classes'=>$this->classes
        , 'attributes'=>$this->attributes
        , 'languages'=>$this->languages];
        return $res;
    }

    public static function loadStructureFromJSON($jsonStructure)
    {
        $structure=json_decode($jsonStructure, true);

        $languages=[];
        foreach ($structure['languages'] as $id=>$language) {
            $languages[(int)$id]=$language;
        }

        $classes_list=[];
        foreach ($structure['classes'] as $group=>$classes_array) {
            foreach ($classes_array as $id=>$labels) {
                $classes_list[$id]=$labels[0];
            }
        }

        $attributes=self::getAllAttributesFromStructureArray($structure, $languages);

        $relationsClasses=[];
        foreach ($structure['relations'] as $id=>$relation_parent_and_children) {
            $ids_array=explode(',', $relation_parent_and_children);
            $relationsClasses[(int)$id]=['parent'=>array_shift($ids_array), 'children'=>$ids_array];
        }

        $relationsList=[];
        foreach ($structure['relation_names'] as $id=>$relation_array) {
            $relation_name=$relation_array[0];
            $relation_key=$relation_array[1];
            $relationsList[(int)$id]=['name'=>$relation_name, 'key'=>$relation_key]+$relationsClasses[$id];
        }


        // TBD
        // groups
        // mandatory class_attributes

        $classes=[];
        foreach ($classes_list as $id=>$className) {
            $classAttris=[];
            $attributesInClass=self::getAttributesInClass($structure, $id);
            foreach ($attributesInClass as $attributeId) {
                $classAttris+=self::getAttributesFromId($attributes, $languages, $attributeId);
            }
            
            $classInstance=BaseClass::createFromAttributesArray($className, $classAttris);
            $classes[$id]=$classInstance;
        }

        foreach ($relationsList as $id=>$relation) {
            $children=[];
            foreach ($relation['children'] as $childrenClassId) {
                $children[]=$classes[$childrenClassId];
            }

            $classes[$relation['parent']]->addRelation(new BaseRelation($relation['key'], $relation['name'], $children));
        }
        
        return new self($languages, $attributes, $classes);
    }

    public function getClasses()
    {
        return $this->classes;
    }

    private function getAllAttributesFromStructureArray($structure, $languages)
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
        $attributes=$structure['attributes_classes'][(string)$id];
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


    private static function loadAttributes($structure, $attributeType, $languages=[])
    {
        $atris=[];
        if (isset($structure[$attributeType])) {
            foreach ($structure[$attributeType] as $id=>$stringAttribute) {
                if ($languages) {
                    foreach ($languages as $languageId=>$language) {
                        $atri=new BaseAttribute($stringAttribute[0]."_$language", ['language'=>$language]);
                        $atris[$languageId+$id]=$atri;
                    }
                } else {
                    $atri=new BaseAttribute($stringAttribute[0]);
                    $atris[$id]=$atri;
                }
            }
        }
        return $atris;
    }
}
