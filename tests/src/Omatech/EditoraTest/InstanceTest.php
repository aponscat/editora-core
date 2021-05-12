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
        ['key'=>'english-title', 'type'=>'Omatech\Editora\Structure\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
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
        ['key'=>'english-title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'config'=>['language'=>'en']]
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
        ['key'=>'english-title', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'config'=>['language'=>'en']]
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
        , 'valueType'=>'Omatech\Editora\Values\ReverseValue'
        , 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'en']]
        , ['key'=>'spanish-title', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'es']]
        , ['key'=>'spanish-text', 'valueType'=>'Omatech\Editora\Values\ReverseValue', 'config'=>['language'=>'es']]
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
        , ['key'=>'times', 'valueType'=>'Omatech\Editora\Values\NumberValue']
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
        , ['key'=>'times', 'valueType'=>'Omatech\Editora\Values\NumberValue']
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
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\Structure\StrangeAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\Structure\StrangeAttribute', 'config'=>['language'=>'en']]
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



    public function testGetDataAfterCreateFromJSONFromImageAttribute(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'image-with-height'
        , 'type'=>'Omatech\Editora\Structure\ImageAttribute'
        , 'valueType'=>'Omatech\Editora\Values\ImageValue'
          , 'config'=>
          ['language'=>'en'
          , 'mandatory'=>true
          , 'dimensions'=>'x300'
          , 'storage-path'=>dirname(__FILE__)
          , 'public-path'=>'/images'
          , 'adapters'=>['media'=>'Omatech\Editora\Adapters\TestMediaAdapter']
          ]]
        , ['key'=>'image-with-width', 'type'=>'Omatech\Editora\Structure\ImageAttribute', 'valueType'=>'Omatech\Editora\Values\ImageValue', 'config'=>['language'=>'en', 'dimensions'=>'300x']]
        , ['key'=>'image-with-width-and-height', 'type'=>'Omatech\Editora\Structure\ImageAttribute', 'valueType'=>'Omatech\Editora\Values\ImageValue', 'config'=>['dimensions'=>'100x200']]
      ]);
        $class=BaseClass::createFromJSON('image', $jsonAttributes);


        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getDimensions(), 'x300');
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getDimensions(), '300x');
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getDimensions(), '100x200');

        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getWidth(), null);
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getWidth(), 300);
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getWidth(), 100);

        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getHeight(), 300);
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getHeight(), null);
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getHeight(), 200);

        $instance=BaseInstance::createFromJSON($class, 'image-instance', 'O', json_encode(
            [
          ['image-with-height:en'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/sample-image-640x480.jpeg')))]
    ]
        ));

        print_r($instance->getData());
    }
}
