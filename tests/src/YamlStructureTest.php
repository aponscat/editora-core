<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;
use Omatech\Editora\Infrastructure\Persistence\Memory\ArrayInstanceRepository;
use Omatech\Editora\Infrastructure\Persistence\File\StructureRepository;
use Omatech\Editora\Domain\Cms;
use Omatech\Editora\Domain\CmsData\Instance;

class YamlStructureTest extends TestCase
{
    public function testLoadStructureFromYaml(): void
    {
        $structure=StructureRepository::read(dirname(__FILE__).'/../data/editora_simple.yml');
        $storage=new ArrayInstanceRepository($structure);

        $publicPath='/images';
        $originalFilename='result.jpg';

        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

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

        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');
        
        $cms->putInstance($instance);
        $id1=$instance->ID();
        $instance2=$cms->getInstanceById($id1);

        $this->assertTrue($instance2->getData('es')==[
            'title' => 'Primer titular de la noticia'
            ,'image-with-alt-and-title' => '/images/20210603/result.jpg'
            ,'image-with-alt-and-title.alt' => 'Texto alternativo de la imágen'
            ,'image-with-alt-and-title.title' => 'Título de la imágen'
            ,'image-with-alt-and-title.code' => '0001'
        ]);

        $this->assertTrue($instance2->getData('en')==[
            'title' => 'First title of a news item'
            ,'image-with-alt-and-title' => '/images/20210603/result.jpg'
            ,'image-with-alt-and-title.alt' => 'Alternative text of the image'
            ,'image-with-alt-and-title.title' => 'Image title'
            ,'image-with-alt-and-title.code' => '0001'
        ]);
        
        $categoryClass=$cms->getClass('news-category');
        $instance=Instance::create(
            $categoryClass,
            'tech',
            ['code'=>'tech'
            , 'title:es'=>'Tecnología'
            , 'title:en'=>'Technology'
            ]
        );
        $this->assertTrue($instance->getData('es')['title']=='Tecnología');

        $cms->putInstance($instance);
        $id2=$instance->ID();
        $instance3=$cms->getInstanceById($id2);
        $this->assertTrue($instance3->getData('es')['title']=='Tecnología');

        $instance4Array=['metadata'=>[
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

        $instance4=$cms->putArrayInstance($instance4Array);
        $id3=$instance4->ID();
        $instance5=$cms->getInstanceById($id3);
        $this->assertTrue($instance5->getData('es')['title']=='Sociedad');

        $instancesInStorage=$cms->getAllInstances();

        $this->assertTrue(array_key_exists($id1, $instancesInStorage));
        $this->assertTrue(array_key_exists($id2, $instancesInStorage));
        $this->assertTrue(array_key_exists($id3, $instancesInStorage));


        $this->assertTrue(
        $instancesInStorage[$id2]->getData('es')==
        ['code' => 'tech'
        ,'title' => 'Tecnología'
        ]
        );

        $this->assertTrue(
        $instancesInStorage[$id3]->getMultilanguageData()==
        ['code' => 'society'
        ,'title:es' => 'Sociedad'
        ,'title:en' => 'Society']
        );


        $this->assertTrue(
        $instancesInStorage[$id2]->getMultilanguageData()==
        ['code' => 'tech'
        ,'title:es' => 'Tecnología'
        ,'title:en' => 'Technology']
        );

        $this->assertTrue(
        $instancesInStorage[$id3]->getMultilanguageData()==
        ['code' => 'society'
        ,'title:es' => 'Sociedad'
        ,'title:en' => 'Society']
        );


        $onlyCategoryInstances=$cms->filterInstances($instancesInStorage, function ($instance) {
            if ($instance->getClassKey()=='news-category') {
                return $instance;
            }
        });

        $this->assertFalse(array_key_exists($id1, $onlyCategoryInstances));
        $this->assertTrue(array_key_exists($id2, $onlyCategoryInstances));
        $this->assertTrue(array_key_exists($id3, $onlyCategoryInstances));

        
    }
}
