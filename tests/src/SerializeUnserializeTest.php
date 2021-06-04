<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Application\Cms;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Structure\Relation;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class SerializeUnserializeTest extends TestCase
{
    public function testLoadStructureFromSimpleModernJSONSerializeAndUnserialize(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        //$jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        //$structure=Structure::loadStructureFromJSON($jsonStructure);
        $structure=YamlStructureRepository::read(dirname(__FILE__).'/../data/editora_simple.yml');
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);

        $instance1=[
            'metadata'=>[
                'class'=>'news-item'
                , 'key'=>'first-news-item'
            ]
            , 'values'=>[
                    'title:en'=>'First news item title'
                    , 'title:es'=>'Primer titular de la noticia'
                    , 'image-with-alt-and-title'=>[
                        'original-filename'=>'fff'
                        , 'data'=>'aaaa'
                    ]
            ]
        ];

        $id1=$cms->putArrayInstance($instance1);

        foreach ($storage->all() as $instance) {
            if ($instance->getKey()=='first-news-item') {
                $this->assertTrue($instance->getData('en')['title']=='First news item title');
                $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');
            }
            if ($instance->getKey()=='society') {
                $this->assertTrue($instance->getData('en')['title']=='Society');
                $this->assertTrue($instance->getData('es')['title']=='Sociedad');
            }
        }
    }
}
