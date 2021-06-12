<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Application\Cms;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Structure\Relation;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class CmsOrderableTest extends TestCase
{

    private Structure $structure;

    public function setUp(): void
    {
        parent::setUp();
        $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_orderable.yml');
    }

    public function testLoadStructureFromYamlAndTestOrder(): void
    {
        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $countryClass=$cms->getClass('news-item');

        $instance=Instance::create(
            $countryClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
            , 'title:es'=>'Primer titular de la noticia'
            , 'orderable-attribute'=>'1'
            ]
        );
        $storage->create($instance);

        $instance=Instance::create(
            $countryClass,
            'second-news-item',
            ['title:en'=>'Second title of a news item'
            , 'title:es'=>'Segundo titular de la noticia'
            , 'orderable-attribute'=>'0'
            ]
        );
        $storage->create($instance);

        $instance=Instance::create(
            $countryClass,
            'third-news-item',
            ['title:en'=>'Third title of a news item'
            , 'title:es'=>'Tercer titular de la noticia'
            , 'orderable-attribute'=>'9'
            ]
        );
        $storage->create($instance);

        $storedInstances=$cms->getAllInstances();
        $orderedInstances=$cms->orderInstances($storedInstances);
        $this->assertTrue($orderedInstances[0]->getData('en')['orderable-attribute']=='0');
        $this->assertTrue($orderedInstances[1]->getData('en')['orderable-attribute']=='1');
        $this->assertTrue($orderedInstances[2]->getData('en')['orderable-attribute']=='9');
    }
}
