<?php declare(strict_types=1);

namespace Omatech\Tests\Application;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Application\CreateInstance\CreateInstanceCommand;
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
        $newsItemClass=$cms->getClass('news-item');
        $originalFilename='result.jpg';

        $instance=Instance::create(
            $newsItemClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
            , 'title:es'=>'Primer titular de la noticia'
            ,'image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                , 'image-with-alt-and-title.alt:en'=>'Alternative text of the image'
                , 'image-with-alt-and-title.alt:es'=>'Texto alternativo de la imágen'
                , 'image-with-alt-and-title.title:en'=>'Image title'
                , 'image-with-alt-and-title.title:es'=>'Título de la imágen'
                , 'image-with-alt-and-title.code' => '0001'
                ]
            ]
        );

        $command=new CreateInstanceCommand(['cms'=>$cms, 'instance'=>$instance]);
        (new CreateInstanceCommandHandler())->__invoke($command);

        self::assertTrue(true);
    }
}
