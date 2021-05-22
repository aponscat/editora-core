<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Facades\Cms;
use Omatech\Editora\Structure\CmsStructure;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Data\BaseInstance;
use Omatech\Editora\Structure\BaseRelation;
use Omatech\Editora\Structure\BaseClass;

class CmsTest extends TestCase
{
    public function testLoadStructureFromReverseEngeeneredJSON(): void
    {
        $jsonStructure=file_get_contents(dirname(__FILE__).'/test_structure.json');
        $structure=CmsStructure::loadStructureFromReverseEngineeredJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        //echo json_encode($cms, JSON_PRETTY_PRINT);
        $countryClass=$cms->getClass('Countries');
        //var_dump($country);
        $instance=BaseInstance::createFromJSON($countryClass, 'country-es', 'O', json_encode(
            [
              ['country_code'=>'es'
              , 'title_es:es'=>'España'
              , 'title_en:en'=>'Spain'
              ]
            ]
        ));
        $this->assertTrue($instance->getData('es')==
        ['key' => 'country-es'
        ,'country_code' => 'es'
        ,'title_es' => 'España']);

        $id=uniqid();
        $cms->putInstanceWithID($id, $instance);
        $instance2=$cms->getInstanceByID($id);
        $this->assertTrue($instance2->getData('es')==$instance->getData('es'));
        $this->assertTrue($instance2->getData('en')==$instance->getData('en'));
    }

    public function testSaveStructureToSimpleModernJSON(): void
    {
        $jsonAttributes=json_encode([
            ['key'=>'title', 'config'=>['language'=>'en', 'mandatory'=>true]]
            , ['key'=>'text', 'config'=>['language'=>'en']]
            , ['key'=>'title', 'config'=>['language'=>'es']]
            , ['key'=>'text', 'config'=>['language'=>'es']]
            , ['key'=>'multilang-attribute']
          ]);
        $newsItem=BaseClass::createFromJSON('news-item', $jsonAttributes);
    

        $jsonAttributes=json_encode([
            ['key'=>'title', 'config'=>['language'=>'en', 'mandatory'=>true]]
            , ['key'=>'title', 'config'=>['language'=>'es']]
            , ['key'=>'code']
          ]);
        $category=BaseClass::createFromJSON('news-category', $jsonAttributes);



        $structure=CmsStructure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');
        $structure->addClass($newsItem);
        $structure->addClass($category);


        file_put_contents(dirname(__FILE__).'/simple_modern.json', json_encode($structure->jsonSerialize(), JSON_PRETTY_PRINT));

        $this->assertTrue(true);
    }


    public function testLoadStructureFromSimpleModernJSON(): void
    {
        $this->assertTrue(true);
    }
}
