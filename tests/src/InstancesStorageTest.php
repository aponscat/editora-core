<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsData\Value;
use Omatech\Editora\Domain\CmsData\Contracts\InstanceRepositoryInterface;
use Omatech\Editora\Infrastructure\Persistence\Memory\ArrayInstanceRepository;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;

class InstancesStorageTest extends TestCase
{
    public function testSaveAndRetrieve(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'title:en'
        , 'valueType'=>'Omatech\Editora\Domain\CmsData\ReverseValue'
        , 'config'=>['mandatory'=>true]]
        , ['key'=>'text:en', 'valueType'=>'Omatech\Editora\Domain\CmsData\ReverseValue']
        , ['key'=>'title:es', 'valueType'=>'Omatech\Editora\Domain\CmsData\ReverseValue']
        , ['key'=>'text:es', 'valueType'=>'Omatech\Editora\Domain\CmsData\ReverseValue']
        , ['key'=>'multilang-attribute']
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);

        $instance1=Instance::create(
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
            $instance1->getData('en')==
        [
          "title" => "!eltiT dlroW olleH"
          ,"text" => "!txeT dlroW olleH"
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
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
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
            ]
        );

        $this->assertTrue(
            $instance1->getData('es')==
      [
        "title" => "!odnuM aloH"
        ,"text" => "!txeT odnuM aloH"
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
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
          , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );


        $id=uniqid();
        $structure=CmsStructure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');
        $structure->addClass($class);
        $storage=new ArrayInstanceRepository($structure);
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
        , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
        ]
        );
    }
}
