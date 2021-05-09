<?php

namespace Omatech\Editora;

class ECMS
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

    public static function loadFromJSON($jsonStructure)
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

        $classes=[];
        foreach ($classes_list as $id=>$className) {
            $classAttris=[];
            $attributesInClass=self::getAttributesInClass($structure, $id);
            foreach ($attributesInClass as $attributeId) {
                $classAttris+=self::getAttributesFromId($attributes, $languages, $attributeId);
            }
            //echo "Creating $className with class_id=$id\n";
            $classInstance=BaseClass::createFromAttributesArray($className, $classAttris);
            //var_dump($classInstance);
            $classes[$className]=$classInstance;
        }
        
        return new self($languages, $attributes, $classes);
    }

    
    private static function getAttributesFromId($atris, $languages, $attributeId)
    {
        $res=[];
        if (isset($atris[$attributeId])) {
            $res[$attributeId]=$atris[$attributeId];
        }
        foreach ($languages as $languageId=>$language) {
            if (isset($atris[$languageId+$attributeId])) {
                $res[$languageId+$attributeId]=$atris[$languageId+$attributeId];
            }
        }
        return $res;
    }

    private static function getAttributesInClass($structure, $id)
    {
        $res=[];
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
