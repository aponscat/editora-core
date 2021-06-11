<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Domain\Structure\ImageAttribute;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Data\Value;
use Omatech\Editora\Domain\Data\ImageValue;

class InstanceWithSubAttributesTest extends TestCase
{
    public function testGetDataAfterCreateFromJSONFromImageAttributeWithSubAttributes(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $attributes=[
        ['key'=>'image-with-alt-and-title'
        , 'type'=>'Omatech\Editora\Domain\Structure\ImageAttribute'
        , 'valueType'=>'Omatech\Editora\Domain\Data\ImageValue'
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
        ]]];
        $class=Clazz::createFromSimpleAttributesArray('image', $attributes);
        $instance=Instance::create(
            $class,
            'image-instance',
            ['image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                , 'image-with-alt-and-title.alt:en'=>'English alt text'
                , 'image-with-alt-and-title.alt:es'=>'Texto alternativo en Español'
                , 'image-with-alt-and-title.title:en'=>'English title'
                , 'image-with-alt-and-title.title:es'=>'Título en Español'
                , 'image-with-alt-and-title.code'=>'CodeTextValue'
                ]
              ]
        );

        $res=$instance->getData('en');
        $this->assertTrue(!isset($res['key']));
        $this->assertTrue($res['image-with-alt-and-title.alt']=='English alt text');
        $this->assertTrue($res['image-with-alt-and-title.title']=='English title');
        $this->assertTrue($res['image-with-alt-and-title.code']=='CodeTextValue');

        $res=$instance->getData('es', true);
        $this->assertTrue(!isset($res['key']));
        $this->assertTrue($res['image-with-alt-and-title.alt']=='Texto alternativo en Español');
        $this->assertTrue($res['image-with-alt-and-title.title']=='Título en Español');
        $this->assertTrue($res['image-with-alt-and-title.code']=='CodeTextValue');
    }
}
