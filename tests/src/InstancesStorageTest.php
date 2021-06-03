<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Data\Value;
use Omatech\Editora\Domain\Data\Contracts\InstanceRepositoryInterface;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Domain\Structure\Structure;

class InstancesStorageTest extends TestCase
{
    public function testSaveAndRetrieve(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title:en'
        , 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue'
        , 'config'=>['mandatory'=>true]]
        , ['key'=>'text:en', 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue']
        , ['key'=>'title:es', 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue']
        , ['key'=>'text:es', 'valueType'=>'Omatech\Editora\Domain\Data\ReverseValue']
        , ['key'=>'nolang-attribute']
      ]);
        $class=Clazz::createFromJSON('news-item', $jsonAttributes);

        $instance1=Instance::create(
            $class,
            'news-item-instance',
            [
              'title:en'=>'Hello World Title!'
              ,"text:en" => "Hello World Text!"
              ,"title:es" => "Hola Mundo!"
              ,"text:es" => "Hola Mundo Text!"
              ,"nolang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );

        $this->assertTrue(
            $instance1->getData('en')==
        [
          "title" => "!eltiT dlroW olleH"
          ,"text" => "!txeT dlroW olleH"
          , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
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
            , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance1->getData('es')==
      [
        "title" => "!odnuM aloH"
        ,"text" => "!txeT odnuM aloH"
        , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
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
          , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );


        $id=uniqid();
        $structure=Structure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');
        $structure->addClass($class);
        $storage=new InstanceRepository($structure);
        $storage->create($instance1);
        $id=$instance1->ID();
 
        $instance2=$storage::read($id);
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
        , "nolang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );
    }
}
