<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Structure\BaseAttribute;
use Omatech\Editora\Data\BaseInstance;
use Omatech\Editora\Values\BaseValue;

class InstanceTest extends TestCase
{
    public function testGetDataAfterCreate(): void
    {
        $atriTitle=new BaseAttribute('english-title:en', ['mandatory'=>true]);
        $atriText=new BaseAttribute('english-text:en');
        $class=BaseClass::createFromAttributesArray('news-item', [$atriTitle, $atriText]);
        $valTitle=new BaseValue($atriTitle, 'Hello World Title!');
        $valText=new BaseValue($atriText, 'Hello World Text!');
        $instance=BaseInstance::createFromValuesArray($class, 'news-item-instance', 'O', [$valTitle, $valText]);

        $this->assertTrue(
            $instance->getData()==
        [
          "key" => "news-item-instance"
          ,"english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
        ]
        );


        $this->assertTrue(
            $instance->getData('ALL', true)==
          [
            "key" => "news-item-instance"
            , 'metadata' => [
                'status' => 'O'
                ,'startPublishingDate' => null
                ,'endPublishingDate' => null
                ,'externalID' => null
                ,'class'=>'news-item'
            ]
            ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'type'=>'Omatech\Editora\Structure\BaseAttribute', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title:en'=>'Hello World Title!']
            ,["english-text:en" => "Hello World Text!"]
      ]
        ));
        $this->assertTrue(
            $instance->getData()==
        [
          "key" => "news-item-instance"
          ,"english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
        ]
        );

        $this->assertTrue(
            $instance->getData('ALL', true)==
          [
            "key" => "news-item-instance"
            , 'metadata' => [
              'status' => 'O'
              ,'startPublishingDate' => null
              ,'endPublishingDate' => null
              ,'externalID' => null
              ,'class'=>'news-item'
              ]
          ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSONWithMissingOptionalValue(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title:en'=>'Hello World Title!']
      ]
        ));
        $this->assertTrue(
            $instance->getData()==
        [
          "key" => "news-item-instance"
          ,"english-title" => "Hello World Title!"
        ]
        );

        $this->assertTrue(
            $instance->getData('ALL', true)==
          [
            "key" => "news-item-instance"
            , 'metadata' => [
              'status' => 'O'
              ,'startPublishingDate' => null
              ,'endPublishingDate' => null
              ,'externalID' => null
              ,'class'=>'news-item'
              ]
          ,"english-title" => "Hello World Title!"
          ]
        );
    }

    public function testSetDataInNonExistingAttribute(): void
    {
        $atriTitle=new BaseAttribute('english-title:en', ['mandatory'=>true]);
        $atriText=new BaseAttribute('english-text:en');
        $class=BaseClass::createFromAttributesArray('news-item', [$atriTitle, $atriText]);

        $atriInexistentText=new BaseAttribute('english-nonexistent:en');

        $valTitle=new BaseValue($atriTitle, 'Hello World Title!');
        $valText=new BaseValue($atriText, 'Hello World Text!');
        $valInexistentText=new BaseValue($atriInexistentText, 'Inexistent Text!');
        $this->expectException(\Exception::class);
        $instance=BaseInstance::createFromValuesArray($class, 'news-item-instance', 'O', [$valTitle, $valText, $valInexistentText]);
    }

    public function testSetDataInNonExistingAttributeFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);

        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title:en'=>'Hello World Title!']
            ,["english-text:en" => "Hello World Text!"]
            ,["english-nonexistent:en" => "Hello World Text!"]
      ]
        ));
    }

    public function testMissingMandatoryAttributeFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ["english-text:en" => "Hello World Text!"]
      ]
        ));
    }


    public function testGetLanguageDataOnlyAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
        , ['key'=>'spanish-title:es']
        , ['key'=>'spanish-text:es']
        , ['key'=>'multilang-attribute', 'config'=>[]]
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);

        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title:en'=>'Hello World Title!']
            ,["english-text:en" => "Hello World Text!"]
            ,["spanish-title:es" => "Hola Mundo!"]
            ,["spanish-text:es" => "Hola Mundo Text!"]
            ,["multilang-attribute" => "NOT-TRANSLATABLE-CODE"]
      ]
        ));
        $this->assertTrue(
            $instance->getData('en')==
        [
          "key" => "news-item-instance"
          ,"english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance->getData('en', true)==
          [
            "key" => "news-item-instance"
            , 'metadata' => [
              'status' => 'O'
              ,'startPublishingDate' => null
              ,'endPublishingDate' => null
              ,'externalID' => null
              ,'class'=>'news-item'
            ]
          ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance->getData('es')==
      [
        "key" => "news-item-instance"
        ,"spanish-title" => "Hola Mundo!"
        ,"spanish-text" => "Hola Mundo Text!"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
      ]
        );

        $this->assertTrue(
            $instance->getData('es', true)==
        [
          "key" => "news-item-instance"
          , 'metadata' => [
            'status' => 'O'
            ,'startPublishingDate' => null
            ,'endPublishingDate' => null
            ,'externalID' => null
            ,'class'=>'news-item'
            ]
      ,"spanish-title" => "Hola Mundo!"
          ,"spanish-text" => "Hola Mundo Text!"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }

    public function testGetMultilanguageDataAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'text:en']
        , ['key'=>'title:es']
        , ['key'=>'text:es']
        , ['key'=>'multilang-attribute']
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);

        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['title:en'=>'Hello World Title!']
            ,["text:en" => "Hello World Text!"]
            ,["title:es" => "Hola Mundo!"]
            ,["text:es" => "Hola Mundo Text!"]
            ,["multilang-attribute" => "NOT-TRANSLATABLE-CODE"]
      ]
        ));
        $this->assertTrue(
            $instance->getMultilanguageData()==
        [
          "key" => "news-item-instance"
          ,"title:en" => "Hello World Title!"
          ,"text:en" => "Hello World Text!"
          ,"title:es" => "Hola Mundo!"
          ,"text:es" => "Hola Mundo Text!"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance->getMultilanguageData(true)==
          [
            "key" => "news-item-instance"
            , 'metadata' => [
              'status' => 'O'
              ,'startPublishingDate' => null
              ,'endPublishingDate' => null
              ,'externalID' => null
              ,'class'=>'news-item'
          ]
          ,"title:en" => "Hello World Title!"
            ,"text:en" => "Hello World Text!"
            ,"title:es" => "Hola Mundo!"
            ,"text:es" => "Hola Mundo Text!"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );
    }
}
