<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Symfony\Component\Yaml\Yaml;
use Omatech\Editora\Infrastructure\Persistence\Memory\ArrayInstanceRepository;
use Omatech\Editora\Domain\Cms;
use Omatech\Editora\Domain\CmsData\Instance;


class YamlStructureTest extends TestCase
{
    public function testLoadStructureFromYaml(): void
    {
        $yml = Yaml::parseFile(dirname(__FILE__).'/../data/editora_simple.yml');
        //print_r($yml);

        $structure=CmsStructure::createEmptyStructure();
        foreach ($yml['languages'] as $language)
        {
            $structure->addLanguage($language);
        }

        $classArray=[];
        foreach ($yml['classes'] as $classKey=>$class)
        {
            $attributeArray=[];
            foreach ($class['attributes'] as $attributeKey=>$attributeConfig)
            {
                $valueType=null;
                $attributeType='Omatech\Editora\Domain\CmsStructure\Attribute';
                //echo "$attributeKey -> ".print_r($attributeConfig, true)."\n";
                $multiLang=false;
                if ($attributeConfig)
                {
                    if(isset($attributeConfig['type']))
                    {
                        $attributeType=$attributeConfig['type'];
                        unset($attributeConfig['type']);
                    }

                    if(isset($attributeConfig['value-type']))
                    {
                        $valueType=$attributeConfig['value-type'];
                        unset($attributeConfig['value-type']);
                    }

                    if(!isset($attributeConfig['multilang']) || $attributeConfig['multilang']==true)
                    {
                        $multiLang=true;
                        unset($attributeConfig['multilang']);
                    }

                    if (isset($attributeConfig['subattributes']))
                    {
                        foreach ($attributeConfig['subattributes'] as $subattributeKey=>$subattributeConfig)
                        {
                            $subvalueType=null;
                            $subattributeType='Omatech\Editora\Domain\CmsStructure\Attribute';
                            //echo "$subattributeKey -> ".print_r($subattributeConfig, true)."\n";
                            $subattributeMultiLang=false;
            
                            if(isset($subattributeConfig['type']))
                            {
                                $subattributeType=$subattributeConfig['type'];
                                unset($subattributeConfig['type']);
                            }
        
                            if(isset($subattributeConfig['value-type']))
                            {
                                $subvalueType=$subattributeConfig['value-type'];
                                unset($subattributeConfig['value-type']);
                            }
        
                            if(!isset($subattributeConfig['multilang']) || $subattributeConfig['multilang']==true)
                            {
                                $subattributeMultiLang=true;
                                unset($subattributeConfig['multilang']);
                            }

                            if ($subattributeMultiLang)
                            {
                                foreach ($structure->getLanguages() as $language)
                                {
                                    $attributeConfig['subattributes'][]=
                                    ["key"=>"$subattributeKey:$language"
                                    , "config"=>$subattributeConfig
                                    , "valueType"=>$subvalueType];
                                }
                            }
                            else
                            {
                                $attributeConfig['subattributes'][]=
                                ["key"=>"$subattributeKey"
                                , "config"=>$subattributeConfig
                                , "valueType"=>$subvalueType];
                            }

                            unset($attributeConfig['subattributes'][$subattributeKey]);

                        }
                    }
                }

                if ($multiLang)
                {
                    foreach ($structure->getLanguages() as $language)
                    {
                        $attributeArray[]=new $attributeType("$attributeKey:$language", $attributeConfig, $valueType);
                    }
                }
                else
                {
                    $attributeArray[]=new $attributeType($attributeKey, $attributeConfig, $valueType);
                }

            }
            $classArray[]=Clas::createFromAttributesArray($classKey, $attributeArray);
        }

        foreach ($classArray as $class)
        {
            $structure->addClass($class);
        }
        
        $storage=new ArrayInstanceRepository($structure);

        $publicPath='/images';
        $originalFilename='result.jpg';

        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

        $instance=Instance::create(
            $newsItemClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
            , 'title:es'=>'Primer titular de la noticia'
            ,'image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
            ]
        );

        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');

        $cms->putInstance($instance);
        $id1=$instance->ID();
        $instance2=$cms->getInstanceById($id1);
        $this->assertTrue($instance2->getData('es')['title']=='Primer titular de la noticia');
/*
        $categoryClass=$cms->getClass('news-category');
        $instance=Instance::create(
            $categoryClass,
            'tech',
            ['code'=>'tech'
                  , 'title:es'=>'Tecnología'
                  , 'title:en'=>'Technology'
                  ]
        );
        $this->assertTrue($instance->getData('es')['title']=='Tecnología');

        $cms->putInstance($instance);
        $id2=$instance->ID();
        $instance3=$cms->getInstanceById($id2);
        $this->assertTrue($instance3->getData('es')['title']=='Tecnología');

        $instance4Array=['metadata'=>[
            'status'=>'O'
            ,'class'=>'news-category'
            ,'key'=>'society'
        ]
            ,'values'=>[
                'code'=>'society'
                ,'title:es'=>'Sociedad'
                ,'title:en'=>'Society'
            ]
        ];

        $instance4=$cms->putArrayInstance($instance4Array);
        $id3=$instance4->ID();
        $instance5=$cms->getInstanceById($id3);
        $this->assertTrue($instance5->getData('es')['title']=='Sociedad');

        $instancesInStorage=$cms->getAllInstances();

        $this->assertTrue(array_key_exists($id1, $instancesInStorage));
        $this->assertTrue(array_key_exists($id2, $instancesInStorage));
        $this->assertTrue(array_key_exists($id3, $instancesInStorage));


        $this->assertTrue(
            $instancesInStorage[$id2]->getData('es')==
        ['code' => 'tech'
        ,'title' => 'Tecnología'
        ]
        );

        $this->assertTrue(
            $instancesInStorage[$id3]->getMultilanguageData()==
      ['code' => 'society'
      ,'title:es' => 'Sociedad'
      ,'title:en' => 'Society']
        );


        $this->assertTrue(
            $instancesInStorage[$id2]->getMultilanguageData()==
      ['code' => 'tech'
      ,'title:es' => 'Tecnología'
      ,'title:en' => 'Technology']
        );

        $this->assertTrue(
            $instancesInStorage[$id3]->getMultilanguageData()==
        ['code' => 'society'
        ,'title:es' => 'Sociedad'
        ,'title:en' => 'Society']
        );


        $onlyCategoryInstances=$cms->filterInstances($instancesInStorage, function ($instance) {
            if ($instance->getClassKey()=='news-category') {
                return $instance;
            }
        });

        $this->assertFalse(array_key_exists($id1, $onlyCategoryInstances));
        $this->assertTrue(array_key_exists($id2, $onlyCategoryInstances));
        $this->assertTrue(array_key_exists($id3, $onlyCategoryInstances));

*/
    }




}
