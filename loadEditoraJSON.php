<?php

$autoload_location = '/vendor/autoload.php';
$tries = 0;
while (!is_file(__DIR__ . $autoload_location)) {
    $autoload_location = '/..' . $autoload_location;
    $tries++;
    if ($tries > 10) {
        die("Error trying to find autoload file try to make a composer update first\n");
    }
}
require_once __DIR__ . $autoload_location;

use Omatech\Editora\CmsStructure;

$jsonStructure=file_get_contents('/mnt/d/apons/pronokal_structure.json');
$cms=CmsStructure::loadStructureFromJSON($jsonStructure);

foreach ($cms->getClasses() as $id=>$class) {
    echo "class_id:$id ".$class->getKey()."\n";
    if ($class->existRelations()) {
        foreach ($class->getRelations() as $relationId=>$relation) {
            echo "  relation_id:$relationId\n";
            foreach ($relation->getChildren() as $childrenId=>$child) {
                echo "    children_id:$childrenId ".$child->getKey()."\n";
            }
        }
    }
}
