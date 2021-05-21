<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Structure\BaseAttribute;
use Omatech\Editora\Data\BaseInstance;
use Omatech\Editora\Values\BaseValue;
use Omatech\Editora\Ports\CmsStorageInstanceInterface;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Structure\CmsStructure;

class InstancesStorageTest extends TestCase
{
    public function testSaveAndRetrieve(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title'
        , 'valueType'=>'Omatech\Editora\Values\ReverseValue'
        , 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'text', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'en']]
        , ['key'=>'title', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'text', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'multilang-attribute']
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);

        $instance1=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
              ['title:en'=>'Hello World Title!']
              ,["text:en" => "Hello World Text!"]
              ,["title:es" => "Hola Mundo!"]
              ,["text:es" => "Hola Mundo Text!"]
              ,["multilang-attribute" => "NOT-TRANSLATABLE-CODE"]
        ]
        ));

        $this->assertTrue(
            $instance1->getData('en')==
        [
          "key" => "news-item-instance"
          ,"title" => "!eltiT dlroW olleH"
          ,"text" => "!txeT dlroW olleH"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance1->getData('en', true)==
          [
            "key" => "news-item-instance"
            , 'metadata' => [
              'status' => 'O'
              ,'startPublishingDate' => null
              ,'endPublishingDate' => null
              ,'externalID' => null
              ,'class'=>'news-item'
          ]
          ,"title" => "!eltiT dlroW olleH"
            ,"text" => "!txeT dlroW olleH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance1->getData('es')==
      [
        "key" => "news-item-instance"
        ,"title" => "!odnuM aloH"
        ,"text" => "!txeT odnuM aloH"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
      ]
        );

        $this->assertTrue(
            $instance1->getData('es', true)==
        [
          "key" => "news-item-instance"
          , 'metadata' => [
            'status' => 'O'
            ,'startPublishingDate' => null
            ,'endPublishingDate' => null
            ,'externalID' => null
            ,'class'=>'news-item'
        ]
      ,"title" => "!odnuM aloH"
          ,"text" => "!txeT odnuM aloH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );


        $id=uniqid();
        //echo "$id\n";

       
        $structure=CmsStructure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');
        $structure->addClass($class);
        $storage=new ArrayStorageAdapter($structure);
        $instance1->put($id, $storage);
 
        $instance2=$storage::get($id);

        //print_r($instance2->getData('es', true));die;

        $this->assertTrue(
            $instance2->getData('es', true)==
        [
          "key" => "news-item-instance"
          , 'metadata' => [
            'status' => 'O'
            ,'startPublishingDate' => null
            ,'endPublishingDate' => null
            ,'externalID' => null
            ,'class'=>'news-item'
        ]
        ,"title" => "!odnuM aloH"
        ,"text" => "!txeT odnuM aloH"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

    }
}
