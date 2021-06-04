<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Data\Value;

class InstanceWithNonStandardAttributesTest extends TestCase
{
    public function testGetLanguageDataDifferentAttributesFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en'
        , 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue'
        , 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en', 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue']
        , ['key'=>'spanish-title:es', 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue']
        , ['key'=>'spanish-text:es', 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue']
        , ['key'=>'nolang-attribute']
      ]);
        $class=Clazz::createFromJSON('news-item', $jsonAttributes);

        $instance=Instance::create(
            $class,
            'news-item-instance',
            [
              'english-title:en'=>'Hello World Title!'
              ,"english-text:en" => "Hello World Text!"
              ,"spanish-title:es" => "Hola Mundo!"
              ,"spanish-text:es" => "Hola Mundo Text!"
              ,"nolang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        //print_r($instance->getData('en'));

        $this->assertTrue(
            $instance->getData('en')==
        [
          "english-title" => "!eltiT dlroW olleH"
          ,"english-text" => "!txeT dlroW olleH"
          , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance->getData('en', true)==
          ['metadata' => [
              'status' => 'O'
              ,'class'=>'news-item'
              ,"key" => "news-item-instance"
          ]
          ,"english-title" => "!eltiT dlroW olleH"
            ,"english-text" => "!txeT dlroW olleH"
            , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance->getData('es')==
      [
        "spanish-title" => "!odnuM aloH"
        ,"spanish-text" => "!txeT odnuM aloH"
        , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
      ]
        );

        $this->assertTrue(
            $instance->getData('es', true)==
        ['metadata' => [
            'status' => 'O'
            ,'class'=>'news-item'
            ,"key" => "news-item-instance"
            ]
      ,"spanish-title" => "!odnuM aloH"
          ,"spanish-text" => "!txeT odnuM aloH"
            , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }




    public function testSetNumericValueFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'times', 'valueType'=>'Omatech\Editora\Domain\Data\NumberValue']
      ]);
        $class=Clazz::createFromJSON('numeric-item', $jsonAttributes);
        $instance=Instance::create(
            $class,
            'numeric-item-instance',
            [
        'title:en'=>'Numeric Hello World Title!'
        ,"times" => 42
        ]
        );

        $this->assertTrue(
            $instance->getData('en')==
        [
          "title" => "Numeric Hello World Title!"
          ,"times" => 42
        ]
        );
    }


    public function testSetInvalidNumericValueFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title:es', 'config'=>['mandatory'=>true]]
        , ['key'=>'times', 'valueType'=>'Omatech\Editora\Domain\Data\NumberValue']
      ]);
        $class=Clazz::createFromJSON('numeric-item', $jsonAttributes);

        $this->expectException(\Exception::class);
        $instance=Instance::create(
            $class,
            'numeric-item-instance',
            ['title:en'=>'Numeric Hello World Title!'
        ,"times" => 'aaaa'
      ]
        );
    }


    public function testGetDataAfterCreateFromJSONFromStrangeAttribute(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'type'=>'\Omatech\Editora\Domain\Structure\StrangeAttribute', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en', 'type'=>'\Omatech\Editora\Domain\Structure\StrangeAttribute']
      ]);
        $class=Clazz::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create(
            $class,
            'news-item-instance',
            ['english-title-is-strange:en'=>'Hello World Title!'
          ,"english-text-is-strange:en" => "Hello World Text!"
      ]
        );

        $this->assertTrue(
            $instance->getData('en')==
        [
          "english-title-is-strange" => "Hello World Title!"
          ,"english-text-is-strange" => "Hello World Text!"
        ]
        );
    }



    public function testGetDataAfterCreateFromJSONFromImageAttribute(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonAttributes=json_encode([
        ['key'=>'image-with-height:en'
        , 'type'=>'Omatech\Editora\Domain\Structure\ImageAttribute'
        , 'valueType'=>'Omatech\Editora\Domain\Data\ImageValue'
          , 'config'=>
          ['mandatory'=>true
          , 'dimensions'=>'x300'
          , 'storage-path'=>dirname(__FILE__)
          , 'public-path'=>$publicPath
          , 'adapters'=>['media'=>'Omatech\Editora\Adapters\ArrayMediaAdapter']
          ]]
        , ['key'=>'image-with-width:en', 'type'=>'Omatech\Editora\Domain\Structure\ImageAttribute', 'valueType'=>'Omatech\Editora\Domain\Data\ImageValue', 'config'=>['dimensions'=>'300x']]
        , ['key'=>'image-with-width-and-height', 'type'=>'Omatech\Editora\Domain\Structure\ImageAttribute', 'valueType'=>'Omatech\Editora\Domain\Data\ImageValue', 'config'=>['dimensions'=>'100x200']]
      ]);
        $class=Clazz::createFromJSON('image', $jsonAttributes);


        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getDimensions(), 'x300');
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getDimensions(), '300x');
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getDimensions(), '100x200');

        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getWidth(), null);
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getWidth(), 300);
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getWidth(), 100);

        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getHeight(), 300);
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getHeight(), null);
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getHeight(), 200);

        $instance=Instance::create(
            $class,
            'image-instance',
            ['image-with-height:en'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
              ]
        );


        $this->assertTrue($instance->getData()==
        ['image-with-height:en' => "$publicPath".'/'.date_format(date_create(), 'Ymd')."/$originalFilename"]);


        $secondInstance=Instance::create(
            $class,
            'second-image-instance',
            ['image-with-height:en'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
              ]
        );

        $this->assertTrue($secondInstance->getMultilanguageData(true)['metadata']['key']=='second-image-instance');
        $this->assertStringStartsWith("$publicPath".'/'.date_format(date_create(), 'Ymd')."/", $secondInstance->getData('en')['image-with-height']);
        $this->assertStringEndsWith($originalFilename, $secondInstance->getData('en')['image-with-height']);
    }
}
