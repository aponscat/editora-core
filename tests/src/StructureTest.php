<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;

use Omatech\Editora\Domain\Structure\Structure;

class StructureTest extends TestCase
{

    /*
    public function testLoadStructureFromReverseEngineeredJSON(): void
    {
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/test_structure.json');
        $cms=Structure::loadStructureFromReverseEngineeredJSON($jsonStructure);

        $classes=[];
        foreach ($cms->getClasses() as $id=>$class) {
            $classes[$id]['key']=$class->getKey();
            if ($class->existRelations()) {
                foreach ($class->getRelations() as $relationId=>$relation) {
                    foreach ($relation->getChildren() as $childrenId=>$child) {
                        $classes[$id]['relations'][$relationId][]=$child;
                    }
                }
            }
        }
        $this->assertTrue($classes[71]['key']=='ActionGroup');
        $this->assertTrue($classes[71]['relations']['action_cards'][0]=='ActionCard');
    }


    public function testLoadStructureFromEditoraDatabase(): void
    {
        require_once(dirname(__FILE__).'/../data/editoradatabase.php');
        $jsonStructure=\json_encode($data);
        $cms=Structure::loadStructureFromReverseEngineeredJSON($jsonStructure);
        $classes=[];
        foreach ($cms->getClasses() as $id=>$class) {
            $classes[$id]['key']=$class->getKey();
            if ($class->existRelations()) {
                foreach ($class->getRelations() as $relationId=>$relation) {
                    foreach ($relation->getChildren() as $childrenId=>$child) {
                        $classes[$id]['relations'][$relationId][]=$child;
                    }
                }
            }
        }
        $this->assertTrue($classes[71]['key']=='ActionGroup');
        $this->assertTrue($classes[71]['relations']['action_cards'][0]=='ActionCard');

        $resultJSON=json_encode($cms, JSON_PRETTY_PRINT);
    }
    */
    public function testLoadfromOtherSources(): void
    {
        // resto de tests deshabilitados hasta decidir el formato final
        $this->assertTrue(true);
    }
}
