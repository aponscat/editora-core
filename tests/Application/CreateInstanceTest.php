<?php declare(strict_types=1);

namespace Omatech\Tests\Application;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Application\Contracts\CmsInterface;
use Omatech\Editora\Application\Cms;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class CreateInstanceTest extends TestCase
{
    private CmsInterface $cms;

    public function setUp(): void
    {
        parent::setUp();
        $structure= YamlStructureRepository::read(__DIR__ .'/../data/editora_simple.yml');
        $this->cms = new Cms($structure, new InstanceRepository($structure));
    }

    public function testCreateInstanceSuccessful(): void
    {
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

        $command=new CreateInstanceCommand($instanceSocietyArray);
        (new CreateInstanceCommandHandler($this->cms))->__invoke($command);

        $instances=$this->cms->getAllInstances();
        $this->assertTrue(!empty($instances));
    }

    public function testCreateInstanceWithNoMetadata(): void
    {
        $WronginstanceSocietyArray=
        ['values'=>[
            'code'=>'society'
            ,'title:es'=>'Sociedad'
            ,'title:en'=>'Society'
            ]
        ];

        $this->expectException(\Exception::class);
        $command=new CreateInstanceCommand($WronginstanceSocietyArray);
    }

    public function testCreateInstanceWithNoClass(): void
    {
        $WronginstanceSocietyArray=
        ['metadata'=>[
            'key'=>'society'
        ]
        ,['values'=>[
            'code'=>'society'
            ,'title:es'=>'Sociedad'
            ,'title:en'=>'Society'
            ]
        ]];

        $this->expectException(\Exception::class);
        $command=new CreateInstanceCommand($WronginstanceSocietyArray);
    }

    public function testCreateInstanceWithNoKey(): void
    {
        $WronginstanceSocietyArray=
        ['metadata'=>[
            'class'=>'news-category'
        ]
        ,['values'=>[
            'code'=>'society'
            ,'title:es'=>'Sociedad'
            ,'title:en'=>'Society'
            ]
        ]];

        $this->expectException(\Exception::class);
        $command=new CreateInstanceCommand($WronginstanceSocietyArray);
    }


}
