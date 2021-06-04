<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Relation;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;
use Omatech\Editora\Infrastructure\Persistence\File\ReverseEngineerStructureRepository;
use Omatech\Editora\Application\Cms;

class ReverseEngineerToYamlTest extends TestCase
{

    public function testLoadOfFile(): void
    {

        $structure=ReverseEngineerStructureRepository::read(dirname(__FILE__).'/../data/editora_reverse_enginereed_test.php');
        YamlStructureRepository::write($structure, dirname(__FILE__).'/../data/editora_reverse_enginereed_test.yml');
        $structure2=YamlStructureRepository::read(dirname(__FILE__).'/../data/editora_reverse_enginereed_test.yml');
        $storage=new InstanceRepository($structure2);
        $cms=new Cms($structure2, $storage);
        $this->assertTrue(true);

    }


        
}