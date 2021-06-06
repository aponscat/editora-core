<?php declare(strict_types=1);

namespace Omatech\Tests\Application;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Application\CmsCommand;
use Omatech\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Application\Cms;
use Omatech\Editora\Domain\Data\Instance;
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
        $cms= new Cms($this->structure, new InstanceRepository($this->structure));
        $instanceSocietyArray=
        ['metadata'=>[
            'status'=>'O'
            ,'class'=>'news-category'
            ,'key'=>'society'
        ]
        ,'values'=>[
            'code'=>'society'
            ,'title:es'=>'Sociedad'
            ,'title:en'=>'Society'
            ]
        ];

        $command=new CmsCommand($instanceSocietyArray);
        (new CreateInstanceCommandHandler($cms))->__invoke($command);

        self::assertTrue(true);
    }
}
