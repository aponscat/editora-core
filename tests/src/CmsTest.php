<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Facades\Cms;
use Omatech\Editora\Structure\CmsStructure;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Data\BaseInstance;
use Omatech\Editora\Structure\BaseRelation;
use Omatech\Editora\Structure\BaseClass;

class CmsTest extends TestCase
{
    public function testSaveStructureToSimpleModernJSON(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonAttributes=json_encode([
            ['key'=>'title:en', 'config'=>['mandatory'=>true]]
            , ['key'=>'text:en']
            , ['key'=>'title:es']
            , ['key'=>'text:es']
            , ['key'=>'multilang-attribute']
            , ['key'=>'image-with-alt-and-title'
            , 'type'=>'Omatech\Editora\Structure\ImageAttribute'
            , 'valueType'=>'Omatech\Editora\Values\ImageValue'
              , 'config'=>
              ['mandatory'=>true
              , 'dimensions'=>'600x600'
              , 'storage-path'=>dirname(__FILE__)
              , 'public-path'=>$publicPath
              , 'adapters'=>['media'=>'Omatech\Editora\Adapters\ArrayMediaAdapter']
              , 'subattributes'=>[
                ['key'=>'alt:en']
                , ['key'=>'alt:es']
                , ['key'=>'title:en']
                , ['key'=>'title:es']
                , ['key'=>'code']
              ]
          ]]
          ]);

        $newsItem=BaseClass::createFromJSON('news-item', $jsonAttributes);
    
        $jsonAttributes=json_encode([
            ['key'=>'title:en', 'config'=>['mandatory'=>true]]
            , ['key'=>'title:es']
            , ['key'=>'code']
          ]);
        $category=BaseClass::createFromJSON('news-category', $jsonAttributes);

        $structure=CmsStructure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');
        $structure->addClass($newsItem);
        $structure->addClass($category);

        file_put_contents(dirname(__FILE__).'/../data/simple_modern.json', json_encode($structure->jsonSerialize(), JSON_PRETTY_PRINT));

        $this->assertTrue(true);
    }


    public function testLoadStructureFromSimpleModernJSON(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        $countryClass=$cms->getClass('news-item');
        //var_dump($countryClass);

        $instance=BaseInstance::createFromJSON($countryClass, 'first-news-item', 'O', json_encode(
            [
                  ['title:en'=>'First title of a news item'
                  , 'title:es'=>'Primer titular de la noticia'
                  ,'image-with-alt-and-title'=>
                  ['original-filename'=>$originalFilename
                  , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                  ]
                  ]
                ]
        ));
        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');
    }

    public function testLoadStructureFromReverseEngeeneredJSON(): void
    {
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/test_structure.json');
        $structure=CmsStructure::loadStructureFromReverseEngineeredJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        $countryClass=$cms->getClass('Countries');

        $instance=BaseInstance::createFromJSON($countryClass, 'country-es', 'O', json_encode(
            [
                  ['country_code'=>'es'
                  , 'title:es'=>'España'
                  , 'title:en'=>'Spain'
                  ]
                ]
        ));
        $this->assertTrue($instance->getData('es')==
            ['country_code' => 'es'
            ,'title' => 'España']);

        $id=uniqid();
        $cms->putInstanceWithID($id, $instance);
        $instance2=$cms->getInstanceByID($id);
        $this->assertTrue($instance2->getData('es')==$instance->getData('es'));
        $this->assertTrue($instance2->getData('en')==$instance->getData('en'));
    }


    public function testLoadStructureFromSimpleModernJSONAndRetrieveInstance(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');
        //var_dump($countryClass);

        $instance=BaseInstance::createFromJSON($newsItemClass, 'first-news-item', 'O', json_encode(
            [
                  ['title:en'=>'First title of a news item'
                  , 'title:es'=>'Primer titular de la noticia'
                  ,'image-with-alt-and-title'=>
                  ['original-filename'=>$originalFilename
                  , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                  ]
                  ]
                ]
        ));
        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');

        $id1=$cms->putInstance($instance);
        $instance2=$cms->getInstanceById($id1);
        $this->assertTrue($instance2->getData('es')['title']=='Primer titular de la noticia');

        $categoryClass=$cms->getClass('news-category');
        $instance=BaseInstance::createFromJSON($categoryClass, 'tech', 'O', json_encode(
            [
                  ['code'=>'tech'
                  , 'title:es'=>'Tecnología'
                  , 'title:en'=>'Technology'
                  ]
                ]
        ));
        $this->assertTrue($instance->getData('es')['title']=='Tecnología');

        $id2=$cms->putInstance($instance);
        $instance3=$cms->getInstanceById($id2);
        $this->assertTrue($instance3->getData('es')['title']=='Tecnología');

        $instance4='
        {
          "metadata":{"status":"O"
            ,"startPublishingDate":null
            ,"endPublishingDate":null
            ,"externalID":null
            ,"class":"news-category"
            ,"key":"society"}
            ,"values":
            {"code":{"attribute":{"key":"code"},"value":"society"}
            ,"title:es":{"attribute":{"key":"title:es"},"value":"Sociedad"}
            ,"title:en":{"attribute":{"key":"title:en","config":{"mandatory":true}},"value":"Society"}}}
        ';

        $id3=$cms->putJSONInstance($instance4);
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
    }
}
