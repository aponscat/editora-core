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
        ['key'=>'title:en'
        , 'valueType'=>'Omatech\Editora\Values\ReverseValue'
        , 'config'=>['mandatory'=>true]]
        , ['key'=>'text:en', 'valueType'=>'Omatech\Editora\Values\ReverseValue']
        , ['key'=>'title:es', 'valueType'=>'Omatech\Editora\Values\ReverseValue']
        , ['key'=>'text:es', 'valueType'=>'Omatech\Editora\Values\ReverseValue']
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
          "title" => "!eltiT dlroW olleH"
          ,"text" => "!txeT dlroW olleH"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance1->getData('en', true)==
          ['metadata' => [
              'status' => 'O'
              ,'class'=>'news-item'
              , "key" => "news-item-instance"
              ]
            ,"title" => "!eltiT dlroW olleH"
            ,"text" => "!txeT dlroW olleH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance1->getData('es')==
      [
        "title" => "!odnuM aloH"
        ,"text" => "!txeT odnuM aloH"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
      ]
        );

        $this->assertTrue(
            $instance1->getData('es', true)==
        ['metadata' => [
            'status' => 'O'
            ,'class'=>'news-item'
            ,"key" => "news-item-instance"
            ]
          ,"title" => "!odnuM aloH"
          ,"text" => "!txeT odnuM aloH"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );


        $id=uniqid();
        $structure=CmsStructure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');
        $structure->addClass($class);
        $storage=new ArrayStorageAdapter($structure);
        $id=$instance1->put($storage);
 
        $instance2=$storage::get($id);

        $this->assertTrue(
            $instance2->getData('es', true)==
        ['metadata' => [
            'status' => 'O'
            ,'class'=>'news-item'
            ,"key" => "news-item-instance"
            , "ID" => $id
        ]
        ,"title" => "!odnuM aloH"
        ,"text" => "!txeT odnuM aloH"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );
    }
}
