<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Structure\BaseClass;
use Omatech\Editora\Structure\BaseAttribute;
use Omatech\Editora\Data\BaseInstance;
use Omatech\Editora\Values\BaseValue;

class InstanceWithSubAttributesTest extends TestCase
{
    public function testGetDataAfterCreateFromJSONFromImageAttributeWithSubAttributes(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonAttributes=json_encode([
        ['key'=>'image-with-alt-and-title'
        , 'type'=>'Omatech\Editora\Structure\ImageAttribute'
        , 'valueType'=>'Omatech\Editora\Values\ImageValue'
          , 'config'=>
          ['language'=>'ALL'
          , 'mandatory'=>true
          , 'dimensions'=>'600x600'
          , 'storage-path'=>dirname(__FILE__)
          , 'public-path'=>$publicPath
          , 'adapters'=>['media'=>'Omatech\Editora\Adapters\ArrayMediaAdapter']
          , 'subattributes'=>[
            ['key'=>'alt', 'config'=>['language'=>'en']]
            , ['key'=>'alt', 'config'=>['language'=>'es']]
            , ['key'=>'title', 'config'=>['language'=>'en']]
            , ['key'=>'title', 'config'=>['language'=>'es']]
            , ['key'=>'code']
          ]
      ]]]);
        $class=BaseClass::createFromJSON('image', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'image-instance', 'O', json_encode(
            [
              ['image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/sample-image-640x480.jpeg')))
                , 'image-with-alt-and-title.alt:en'=>'English alt text'
                , 'image-with-alt-and-title.alt:es'=>'Texto alternativo en Español'
                , 'image-with-alt-and-title.title:en'=>'English title'
                , 'image-with-alt-and-title.title:es'=>'Título en Español'
                , 'image-with-alt-and-title.code'=>'CodeTextValue'
                ]
              ]
            ]
        ));

        $res=$instance->getData('en');
        $this->assertTrue($res['key']=='image-instance');
        $this->assertTrue($res['image-with-alt-and-title.alt']=='English alt text');
        $this->assertTrue($res['image-with-alt-and-title.title']=='English title');
        $this->assertTrue($res['image-with-alt-and-title.code']=='CodeTextValue');

        $res=$instance->getData('es', true);
        $this->assertTrue($res['key']=='image-instance');
        $this->assertTrue($res['image-with-alt-and-title.alt']=='Texto alternativo en Español');
        $this->assertTrue($res['image-with-alt-and-title.title']=='Título en Español');
        $this->assertTrue($res['image-with-alt-and-title.code']=='CodeTextValue');
    }
}
