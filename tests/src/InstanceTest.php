<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsData\Value;

class InstanceTest extends TestCase
{
    public function testGetDataAfterCreate(): void
    {
        $atriTitle=new Attribute('english-title:en', ['mandatory'=>true]);
        $atriText=new Attribute('english-text:en');
        $class=Clas::createFromAttributesArray('news-item', [$atriTitle, $atriText]);
        $valTitle=new Value($atriTitle, 'Hello World Title!');
        $valText=new Value($atriText, 'Hello World Text!');
        $instance=Instance::create($class, 'news-item-instance', $valTitle->toArray()+$valText->toArray());

        $this->assertTrue(
            $instance->getData()==
        [
          "english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
        ]
        );


        $this->assertTrue(
            $instance->getData('ALL', true)==
          ['metadata' => [
                'status' => 'O'
                ,'class'=>'news-item'
                ,"key" => "news-item-instance"
                ]
            ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'type'=>'Omatech\Editora\Domain\CmsStructure\Attribute', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create(
            $class,
            'news-item-instance',
            [
            'english-title:en'=>'Hello World Title!'
            ,"english-text:en" => "Hello World Text!"
      ]
        );
        $this->assertTrue(
            $instance->getData()==
        [
          "english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
        ]
        );

        $this->assertTrue(
            $instance->getData('ALL', true)==
          ['metadata' => [
              'status' => 'O'
              ,'class'=>'news-item'
              ,"key" => "news-item-instance"
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
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create(
            $class,
            'news-item-instance',
            ['english-title:en'=>'Hello World Title!']
        );
        $this->assertTrue(
            $instance->getData()==
        [
          "english-title" => "Hello World Title!"
        ]
        );

        $this->assertTrue(
            $instance->getData('ALL', true)==
          ['metadata' => [
              'status' => 'O'
              ,'class'=>'news-item'
              ,"key" => "news-item-instance"
              ]
          ,"english-title" => "Hello World Title!"
          ]
        );
    }

    public function testSetDataInNonExistingAttribute(): void
    {
        $atriTitle=new Attribute('english-title:en', ['mandatory'=>true]);
        $atriText=new Attribute('english-text:en');
        $class=Clas::createFromAttributesArray('news-item', [$atriTitle, $atriText]);

        $atriInexistentText=new Attribute('english-nonexistent:en');

        $valTitle=new Value($atriTitle, 'Hello World Title!');
        $valText=new Value($atriText, 'Hello World Text!');
        $valInexistentText=new Value($atriInexistentText, 'Inexistent Text!');
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', [$valTitle, $valText, $valInexistentText]);
    }

    public function testSetDataInNonExistingAttributeFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);

        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create(
            $class,
            'news-item-instance',
            [
            ['english-title:en'=>'Hello World Title!']
            ,["english-text:en" => "Hello World Text!"]
            ,["english-nonexistent:en" => "Hello World Text!"]
      ]
        );
    }

    public function testMissingMandatoryAttributeFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
        , ['key'=>'english-text:en']
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create(
            $class,
            'news-item-instance',
            ["english-text:en" => "Hello World Text!"]
        );
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
        $class=Clas::createFromJSON('news-item', $jsonAttributes);

        $instance=Instance::create(
            $class,
            'news-item-instance',
            ['english-title:en'=>'Hello World Title!'
            ,"english-text:en" => "Hello World Text!"
            ,"spanish-title:es" => "Hola Mundo!"
            ,"spanish-text:es" => "Hola Mundo Text!"
            ,"multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );
        $this->assertTrue(
            $instance->getData('en')==
        [
          "english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
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
          ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance->getData('es')==
      [
        "spanish-title" => "Hola Mundo!"
        ,"spanish-text" => "Hola Mundo Text!"
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
        $class=Clas::createFromJSON('news-item', $jsonAttributes);

        $instance=Instance::create(
            $class,
            'news-item-instance',
            [
            'title:en'=>'Hello World Title!'
            ,"text:en" => "Hello World Text!"
            ,"title:es" => "Hola Mundo!"
            ,"text:es" => "Hola Mundo Text!"
            ,"multilang-attribute" => "NOT-TRANSLATABLE-CODE"
      ]
        );
        $this->assertTrue(
            $instance->getMultilanguageData()==
        [
          "title:en" => "Hello World Title!"
          ,"text:en" => "Hello World Text!"
          ,"title:es" => "Hola Mundo!"
          ,"text:es" => "Hola Mundo Text!"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance->getMultilanguageData(true)==
          ['metadata' => [
              'status' => 'O'
              ,'class'=>'news-item'
              ,"key" => "news-item-instance"
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
