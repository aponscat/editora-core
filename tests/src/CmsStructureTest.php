<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;

use Omatech\Editora\Structure\CmsStructure;

class CmsStructureTest extends TestCase
{
    public function testLoadStructureFromReverseEngineeredJSON(): void
    {
        $jsonStructure=file_get_contents(dirname(__FILE__).'/test_structure.json');
        $cms=CmsStructure::loadStructureFromJSON($jsonStructure);
        $classes=[];
        foreach ($cms->getClasses() as $id=>$class) {
            $classes[$id]['key']=$class->getKey();
            if ($class->existRelations()) {
                foreach ($class->getRelations() as $relationId=>$relation) {
                    foreach ($relation->getChildren() as $childrenId=>$child) {
                        $classes[$id]['relations'][$relationId][]=$child->getKey();
                    }
                }
            }
        }
        //print_r($classes);
        $this->assertTrue($classes[71]['key']=='ActionGroup');
        $this->assertTrue($classes[71]['relations']['action_cards'][0]=='ActionCard');
    }


    public function testLoadStructureFromEditoraDatabase(): void
    {
        require_once(dirname(__FILE__).'/editoradatabase.php');
        $jsonStructure=\json_encode($data);
        $cms=CmsStructure::loadStructureFromJSON($jsonStructure);
        $classes=[];
        foreach ($cms->getClasses() as $id=>$class) {
            $classes[$id]['key']=$class->getKey();
            if ($class->existRelations()) {
                foreach ($class->getRelations() as $relationId=>$relation) {
                    foreach ($relation->getChildren() as $childrenId=>$child) {
                        $classes[$id]['relations'][$relationId][]=$child->getKey();
                    }
                }
            }
        }
        $this->assertTrue($classes[71]['key']=='ActionGroup');
        $this->assertTrue($classes[71]['relations']['action_cards'][0]=='ActionCard');

        $resultJSON=json_encode($cms, JSON_PRETTY_PRINT);
    }
}
