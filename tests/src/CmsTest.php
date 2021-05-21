<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Facades\Cms;
use Omatech\Editora\Structure\CmsStructure;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Data\BaseInstance;
use Omatech\Editora\Structure\BaseRelation;

class CmsTest extends TestCase
{
    public function testLoadStructureFromEditoraDatabase(): void
    {
        $jsonStructure=file_get_contents(dirname(__FILE__).'/test_structure.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
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

    }
}
