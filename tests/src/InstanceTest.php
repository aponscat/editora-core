<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Data\Value;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class InstanceTest extends TestCase
{
  private Structure $structure;

  public function setUp(): void
  {
      parent::setUp();
      $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_ultrasimple.yml');
  }


    public function testGetDataAfterCreate(): void
    {
        $atriTitle=new Attribute('english-title:en', ['mandatory'=>true]);
        $atriText=new Attribute('english-text:en');
        $class=$this->structure->getClass('news-item');
        $valTitle=new Value($atriTitle, 'Hello World Title!');
        $valText=new Value($atriText, 'Hello World Text!');
        $instance=Instance::create($class, 'news-item-instance', $valTitle->toArray()+$valText->toArray());

        $this->assertTrue(
            $instance->getData('en')==
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
            ,"english-title:en" => "Hello World Title!"
            ,"english-text:en" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSON(): void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create(
            $class,
            'news-item-instance',
            [
            'english-title:en'=>'Hello World Title!'
            ,"english-text:en" => "Hello World Text!"
      ]
        );
        $this->assertTrue(
            $instance->getData('en')==
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
          ,"english-title:en" => "Hello World Title!"
            ,"english-text:en" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSONWithMissingOptionalValue(): void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create(
            $class,
            'news-item-instance',
            ['english-title:en'=>'Hello World Title!']
        );
        $this->assertTrue(
            $instance->getData('en')==
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
          ,"english-title:en" => "Hello World Title!"
          ]
        );
    }

    public function testSetDataInNonExistingAttribute(): void
    {
      $class=$this->structure->getClass('news-item');
      $atriInexistentText=new Attribute('english-nonexistent:en');
      $atriTitle=new Attribute('english-title:en', ['mandatory'=>true]);
      $atriText=new Attribute('english-text:en');

        $valTitle=new Value($atriTitle, 'Hello World Title!');
        $valText=new Value($atriText, 'Hello World Text!');
        $valInexistentText=new Value($atriInexistentText, 'Inexistent Text!');
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', [$valTitle, $valText, $valInexistentText]);
    }

    public function testSetDataInNonExistingAttributeFromJSON(): void
    {
      $class=$this->structure->getClass('news-item');
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
      $class=$this->structure->getClass('news-item');
      $this->expectException(\Exception::class);
        $instance=Instance::create(
            $class,
            'news-item-instance',
            ["english-text:en" => "Hello World Text!"]
        );
    }


    public function testGetLanguageDataOnlyAfterCreateFromJSON(): void
    {
      $class=$this->structure->getClass('news-item');

        $instance=Instance::create(
            $class,
            'news-item-instance',
            ['english-title:en'=>'Hello World Title!'
            ,"english-text:en" => "Hello World Text!"
            ,"spanish-title:es" => "Hola Mundo!"
            ,"spanish-text:es" => "Hola Mundo Text!"
            ,"nolang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );
        $this->assertTrue(
            $instance->getData('en')==
        [
          "english-title" => "Hello World Title!"
          ,"english-text" => "Hello World Text!"
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
          ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
            , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance->getData('es')==
      [
        "spanish-title" => "Hola Mundo!"
        ,"spanish-text" => "Hola Mundo Text!"
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
      ,"spanish-title" => "Hola Mundo!"
          ,"spanish-text" => "Hola Mundo Text!"
            , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }

    public function testGetMultilanguageDataAfterCreateFromJSON(): void
    {
      $class=$this->structure->getClass('news-item');

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
        $this->assertTrue(
            $instance->getMultilanguageData()==
        [
          "english-title:en" => "Hello World Title!"
          ,"english-text:en" => "Hello World Text!"
          ,"spanish-title:es" => "Hola Mundo!"
          ,"spanish-text:es" => "Hola Mundo Text!"
          , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance->getMultilanguageData(true)==
          ['metadata' => [
              'status' => 'O'
              ,'class'=>'news-item'
              ,"key" => "news-item-instance"
              ]
          ,"english-title:en" => "Hello World Title!"
            ,"english-text:en" => "Hello World Text!"
            ,"spanish-title:es" => "Hola Mundo!"
            ,"spanish-text:es" => "Hola Mundo Text!"
            , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );
    }
}
