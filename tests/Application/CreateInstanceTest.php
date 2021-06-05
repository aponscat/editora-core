<?php declare(strict_types=1);

namespace Omatech\Tests\Application;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class CreateInstanceTest extends TestCase
{
    private Structure $structure;

    public function setUp(): void
    {
        parent::setUp();
        $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_simple.yml');
    }

    /** @test */
    public function create_instance_successful(): void
    {
        $commandHandler = new CreateInstanceCommandHandler(new InstanceRepository($this->structure));
        $commandHandler->__invoke(new CreateInstanceCommand([]));

        self::assertTrue(true);
    }
}
