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

use Omatech\Editora\ECMS;

$jsonStructure=file_get_contents('/mnt/d/apons/pronokal_structure.json');
$ecms=ECMS::loadFromJSON($jsonStructure);
var_dump($ecms);
