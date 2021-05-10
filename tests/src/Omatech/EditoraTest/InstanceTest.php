<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\BaseValue;
use Omatech\Editora\BaseAttribute;
use Omatech\Editora\BaseInstance;
use Omatech\Editora\BaseClass;

class InstanceTest extends TestCase
{
    public function testGetDataAfterCreate(): void
    {
        $atriTitle=new BaseAttribute('english-title', ['language'=>'en', 'mandatory'=>true]);
        $atriText=new BaseAttribute('english-text', ['language'=>'en']);
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
            ]
            ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'config'=>['language'=>'en']]
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
          ]
          ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSONWithMissingOptionalValue(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'config'=>['language'=>'en']]
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
          ]
          ,"english-title" => "Hello World Title!"
          ]
        );
    }

    public function testSetDataInNonExistingAttribute(): void
    {
        $atriTitle=new BaseAttribute('english-title', ['language'=>'en', 'mandatory'=>true]);
        $atriText=new BaseAttribute('english-text', ['language'=>'en']);
        $class=BaseClass::createFromAttributesArray('news-item', [$atriTitle, $atriText]);

        $atriInexistentText=new BaseAttribute('english-nonexistent', ['language'=>'en']);

        $valTitle=new BaseValue($atriTitle, 'Hello World Title!');
        $valText=new BaseValue($atriText, 'Hello World Text!');
        $valInexistentText=new BaseValue($atriInexistentText, 'Inexistent Text!');
        $this->expectException(\Exception::class);
        $instance=BaseInstance::createFromValuesArray($class, 'news-item-instance', 'O', [$valTitle, $valText, $valInexistentText]);
    }

    public function testSetDataInNonExistingAttributeFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en']]
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
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en']]
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
        ['key'=>'english-title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'config'=>['language'=>'en']]
        , ['key'=>'spanish-title', 'config'=>['language'=>'es']]
        , ['key'=>'spanish-text', 'config'=>['language'=>'es']]
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
        ]
      ,"spanish-title" => "Hola Mundo!"
          ,"spanish-text" => "Hola Mundo Text!"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }


    public function testGetLanguageDataDifferentAttributesFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title'
        , 'valueType'=>'\Omatech\Editora\ReverseValue'
        , 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'valueType'=>'\Omatech\Editora\ReverseValue', 'config'=>['language'=>'en']]
        , ['key'=>'spanish-title', 'valueType'=>'\Omatech\Editora\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'spanish-text', 'valueType'=>'\Omatech\Editora\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'multilang-attribute']
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
          ,"english-title" => "!eltiT dlroW olleH"
          ,"english-text" => "!txeT dlroW olleH"
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
          ]
          ,"english-title" => "!eltiT dlroW olleH"
            ,"english-text" => "!txeT dlroW olleH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance->getData('es')==
      [
        "key" => "news-item-instance"
        ,"spanish-title" => "!odnuM aloH"
        ,"spanish-text" => "!txeT odnuM aloH"
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
        ]
      ,"spanish-title" => "!odnuM aloH"
          ,"spanish-text" => "!txeT odnuM aloH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }



    public function testGetMultilanguageDataAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'text', 'config'=>['language'=>'en']]
        , ['key'=>'title', 'config'=>['language'=>'es']]
        , ['key'=>'text', 'config'=>['language'=>'es']]
        , ['key'=>'multilang-attribute', 'config'=>[]]
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
          ]
          ,"title:en" => "Hello World Title!"
            ,"text:en" => "Hello World Text!"
            ,"title:es" => "Hola Mundo!"
            ,"text:es" => "Hola Mundo Text!"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );
    }

    public function testSetNumericValueFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'times', 'valueType'=>'\Omatech\Editora\NumberValue']
      ]);
        $class=BaseClass::createFromJSON('numeric-item', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'numeric-item-instance', 'O', json_encode(
            [
            ['title:en'=>'Numeric Hello World Title!']
            ,["times" => 42]
      ]
        ));

        $this->assertTrue(
            $instance->getData()==
        [
          "key" => "numeric-item-instance"
          ,"title" => "Numeric Hello World Title!"
          ,"times" => 42
        ]
        );
    }


    public function testSetInvalidNumericValueFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'times', 'valueType'=>'\Omatech\Editora\NumberValue']
      ]);
        $class=BaseClass::createFromJSON('numeric-item', $jsonAttributes);

        $this->expectException(\Exception::class);
        $instance=BaseInstance::createFromJSON($class, 'numeric-item-instance', 'O', json_encode(
            [
            ['title:en'=>'Numeric Hello World Title!']
            ,["times" => 'aaaa']
      ]
        ));
    }


    public function testGetDataAfterCreateFromJSONFromStrangeAttribute(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\StrangeAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\StrangeAttribute', 'config'=>['language'=>'en']]
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title-is-strange:en'=>'Hello World Title!']
            ,["english-text-is-strange:en" => "Hello World Text!"]
      ]
        ));

        $this->assertTrue(
            $instance->getData()==
        [
          "key" => "news-item-instance"
          ,"english-title-is-strange" => "Hello World Title!"
          ,"english-text-is-strange" => "Hello World Text!"
        ]
        );
    }
}
