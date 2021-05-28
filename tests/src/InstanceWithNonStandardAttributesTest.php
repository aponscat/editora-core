<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Data\Instance;
use Omatech\Editora\Values\Value;

class InstanceWithNonStandardAttributesTest extends TestCase
{
    public function testGetLanguageDataDifferentAttributesFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en'
        , 'valueType'=>'Omatech\Editora\Values\ReverseValue'
        , 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en', 'valueType'=>'Omatech\Editora\Values\ReverseValue']
        , ['key'=>'spanish-title:es', 'valueType'=>'Omatech\Editora\Values\ReverseValue']
        , ['key'=>'spanish-text:es', 'valueType'=>'Omatech\Editora\Values\ReverseValue']
        , ['key'=>'multilang-attribute']
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);

        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
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
          "english-title" => "!eltiT dlroW olleH"
          ,"english-text" => "!txeT dlroW olleH"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
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
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance->getData('es')==
      [
        "spanish-title" => "!odnuM aloH"
        ,"spanish-text" => "!txeT odnuM aloH"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
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
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }




    public function testSetNumericValueFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'times', 'valueType'=>'Omatech\Editora\Values\NumberValue']
      ]);
        $class=Clas::createFromJSON('numeric-item', $jsonAttributes);
        $instance=Instance::createFromJSON($class, 'numeric-item-instance', 'O', json_encode(
            [
            ['title:en'=>'Numeric Hello World Title!']
            ,["times" => 42]
      ]
        ));

        $this->assertTrue(
            $instance->getData()==
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
        , ['key'=>'times', 'valueType'=>'Omatech\Editora\Values\NumberValue']
      ]);
        $class=Clas::createFromJSON('numeric-item', $jsonAttributes);

        $this->expectException(\Exception::class);
        $instance=Instance::createFromJSON($class, 'numeric-item-instance', 'O', json_encode(
            [
            ['title:en'=>'Numeric Hello World Title!']
            ,["times" => 'aaaa']
      ]
        ));
    }


    public function testGetDataAfterCreateFromJSONFromStrangeAttribute(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'type'=>'\Omatech\Editora\Domain\CmsStructure\StrangeAttribute', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en', 'type'=>'\Omatech\Editora\Domain\CmsStructure\StrangeAttribute']
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title-is-strange:en'=>'Hello World Title!']
            ,["english-text-is-strange:en" => "Hello World Text!"]
      ]
        ));

        $this->assertTrue(
            $instance->getData()==
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
        , 'type'=>'Omatech\Editora\Domain\CmsStructure\ImageAttribute'
        , 'valueType'=>'Omatech\Editora\Values\ImageValue'
          , 'config'=>
          ['mandatory'=>true
          , 'dimensions'=>'x300'
          , 'storage-path'=>dirname(__FILE__)
          , 'public-path'=>$publicPath
          , 'adapters'=>['media'=>'Omatech\Editora\Adapters\ArrayMediaAdapter']
          ]]
        , ['key'=>'image-with-width:en', 'type'=>'Omatech\Editora\Domain\CmsStructure\ImageAttribute', 'valueType'=>'Omatech\Editora\Values\ImageValue', 'config'=>['dimensions'=>'300x']]
        , ['key'=>'image-with-width-and-height', 'type'=>'Omatech\Editora\Domain\CmsStructure\ImageAttribute', 'valueType'=>'Omatech\Editora\Values\ImageValue', 'config'=>['dimensions'=>'100x200']]
      ]);
        $class=Clas::createFromJSON('image', $jsonAttributes);


        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getDimensions(), 'x300');
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getDimensions(), '300x');
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getDimensions(), '100x200');

        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getWidth(), null);
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getWidth(), 300);
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getWidth(), 100);

        $this->assertEquals($class->getAttributeByKey('image-with-height:en')->getHeight(), 300);
        $this->assertEquals($class->getAttributeByKey('image-with-width:en')->getHeight(), null);
        $this->assertEquals($class->getAttributeByKey('image-with-width-and-height')->getHeight(), 200);

        $instance=Instance::createFromJSON($class, 'image-instance', 'O', json_encode(
            [
              ['image-with-height:en'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
              ]
            ]
        ));


        $this->assertTrue($instance->getData()==
        ['image-with-height' => "$publicPath".'/'.date_format(date_create(), 'Ymd')."/$originalFilename"]);


        $secondInstance=Instance::createFromJSON($class, 'second-image-instance', 'O', json_encode(
            [
              ['image-with-height:en'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
              ]
            ]
        ));

        /*
        $this->assertFalse($secondInstance->getData()==
        ['key' => 'second-image-instance'
        ,'image-with-height' => "$publicPath".'/'.date_format(date_create(), 'Ymd')."/$originalFilename"]);
        */
        
        $this->assertTrue($secondInstance->getMultilanguageData(true)['metadata']['key']=='second-image-instance');
        $this->assertStringStartsWith("$publicPath".'/'.date_format(date_create(), 'Ymd')."/", $secondInstance->getData()['image-with-height']);
        $this->assertStringEndsWith($originalFilename, $secondInstance->getData()['image-with-height']);
    }
}
