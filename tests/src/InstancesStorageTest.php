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

class InstancesStorageTest extends TestCase
{
    public function testSaveAndRetrieve(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title'
        , 'valueType'=>'Omatech\Editora\Values\ReverseValue'
        , 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'en']]
        , ['key'=>'spanish-title', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'spanish-text', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'multilang-attribute']
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);

        $instance1=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
              ['english-title:en'=>'Hello World Title!']
              ,["english-text:en" => "Hello World Text!"]
              ,["spanish-title:es" => "Hola Mundo!"]
              ,["spanish-text:es" => "Hola Mundo Text!"]
              ,["multilang-attribute" => "NOT-TRANSLATABLE-CODE"]
        ]
        ));

        $this->assertTrue(
            $instance1->getData('en')==
        [
          "key" => "news-item-instance"
          ,"english-title" => "!eltiT dlroW olleH"
          ,"english-text" => "!txeT dlroW olleH"
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
          ,"english-title" => "!eltiT dlroW olleH"
            ,"english-text" => "!txeT dlroW olleH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance1->getData('es')==
      [
        "key" => "news-item-instance"
        ,"spanish-title" => "!odnuM aloH"
        ,"spanish-text" => "!txeT odnuM aloH"
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
      ,"spanish-title" => "!odnuM aloH"
          ,"spanish-text" => "!txeT odnuM aloH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );


        $id=uniqid();
        echo "$id\n";

        $storage=new ArrayStorageAdapter;
        $instance1->put($id, $storage);

        var_dump($storage);
        
        $instance2=$storage::get($id);

        print_r($instance2->getData('es', true));
        
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
      ,"spanish-title" => "!odnuM aloH"
          ,"spanish-text" => "!txeT odnuM aloH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );

    }
}
