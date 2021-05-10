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
            ,"status" => "O"
            ,"startPublishingDate" => null
            ,"endPublishingDate" => null
            ,"externalID" => null
            ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en']]
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title'=>'Hello World Title!']
            ,["english-text" => "Hello World Text!"]
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
            ,"status" => "O"
            ,"startPublishingDate" => null
            ,"endPublishingDate" => null
            ,"externalID" => null
            ,"english-title" => "Hello World Title!"
            ,"english-text" => "Hello World Text!"
          ]
        );
    }

    public function testGetDataAfterCreateFromJSONWithMissingOptionalValue(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en']]
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);
        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title'=>'Hello World Title!']
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
            ,"status" => "O"
            ,"startPublishingDate" => null
            ,"endPublishingDate" => null
            ,"externalID" => null
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
            ['english-title'=>'Hello World Title!']
            ,["english-text" => "Hello World Text!"]
            ,["english-nonexistent" => "Hello World Text!"]
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
            ["english-text" => "Hello World Text!"]
      ]
        ));
        var_dump($instance);
    }


    public function testGetLanguageDataOnlyAfterCreateFromJSON(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en', 'mandatory'=>true]]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'en']]
        , ['key'=>'spanish-title', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'es']]
        , ['key'=>'spanish-text', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>['language'=>'es']]
        , ['key'=>'multilang-attribute', 'type'=>'\Omatech\Editora\BaseAttribute', 'config'=>[]]
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);

        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
            ['english-title'=>'Hello World Title!']
            ,["english-text" => "Hello World Text!"]
            ,["spanish-title" => "Hola Mundo!"]
            ,["spanish-text" => "Hola Mundo Text!"]
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
            ,"status" => "O"
            ,"startPublishingDate" => null
            ,"endPublishingDate" => null
            ,"externalID" => null
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
          ,"status" => "O"
          ,"startPublishingDate" => null
          ,"endPublishingDate" => null
          ,"externalID" => null
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
        , 'type'=>'\Omatech\Editora\ReverseAttribute'
        , 'config'=>['language'=>'en', 'mandatory'=>true, 'valueType'=>'\Omatech\Editora\ReverseValue']]
        , ['key'=>'english-text', 'type'=>'\Omatech\Editora\ReverseAttribute', 'config'=>['language'=>'en', 'valueType'=>'\Omatech\Editora\ReverseValue']]
        , ['key'=>'spanish-title', 'type'=>'\Omatech\Editora\ReverseAttribute', 'config'=>['language'=>'es', 'valueType'=>'\Omatech\Editora\ReverseValue']]
        , ['key'=>'spanish-text', 'type'=>'\Omatech\Editora\ReverseAttribute', 'config'=>['language'=>'es', 'valueType'=>'\Omatech\Editora\ReverseValue']]
        , ['key'=>'multilang-attribute', 'type'=>'\Omatech\Editora\ReverseAttribute', 'config'=>[]]
      ]);
        $class=BaseClass::createFromJSON('news-item', $jsonAttributes);

        $instance=BaseInstance::createFromJSON($class, 'news-item-instance', 'O', json_encode(
            [
              ['english-title'=>'Hello World Title!']
              ,["english-text" => "Hello World Text!"]
              ,["spanish-title" => "Hola Mundo!"]
              ,["spanish-text" => "Hola Mundo Text!"]
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
            ,"status" => "O"
            ,"startPublishingDate" => null
            ,"endPublishingDate" => null
            ,"externalID" => null
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
          ,"status" => "O"
          ,"startPublishingDate" => null
          ,"endPublishingDate" => null
          ,"externalID" => null
          ,"spanish-title" => "!odnuM aloH"
          ,"spanish-text" => "!txeT odnuM aloH"
            , "multilang-attribute" => "NOT-TRANSLATABLE-CODE"
          ]
        );
    }
}
