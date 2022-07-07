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

class CmsTest extends TestCase
{

    private Structure $structure;

    public function setUp(): void
    {
        parent::setUp();
        $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_simple.yml');
    }

    public function testLoadStructureFromSimpleModernYaml(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';

        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $countryClass=$cms->getClass('news-item');

        $instance=Instance::create(
            $countryClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
                  , 'title:es'=>'Primer titular de la noticia'
                  ,'image'=>
                  ['original-filename'=>$originalFilename
                  , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                  ]
                  ]
        );
        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');
    }

    public function testLoadStructureFromSimpleModernJSONAndRetrieveInstance(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

        $instance=Instance::create(
            $newsItemClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
                  , 'title:es'=>'Primer titular de la noticia'
                  ,'image'=>
                  ['original-filename'=>$originalFilename
                  , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                  ]
                  ]
        );
        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');

        $cms->createInstance($instance);
        $id1=$instance->ID();
        $instance2=$cms->getInstanceById($id1);
        $this->assertTrue($instance2->getData('es')['title']=='Primer titular de la noticia');

        $categoryClass=$cms->getClass('news-category');
        //var_dump($categoryClass);
        $instance=Instance::create(
            $categoryClass,
            'tech',
            ['code'=>'tech'
            , 'title:es'=>'Tecnología'
            , 'title:en'=>'Technology'
            ]
        );
        $this->assertTrue($instance->getData('es')['title']=='Tecnología');

        $cms->createInstance($instance);
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

        $instance4=$cms->createInstanceFromArray($instance4Array);
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



    public function testSaveCategoryAndRelatedNews(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

        $instanceNewsItem=Instance::create(
            $newsItemClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
                , 'title:es'=>'Primer titular de la noticia'
                ,'image'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
                ]
        );
        $this->assertTrue($instanceNewsItem->getData('es')['title']=='Primer titular de la noticia');

        $categoryClass=$cms->getClass('news-category');
        $instanceCategory1=Instance::create(
            $categoryClass,
            'tech',
            ['code'=>'tech'
                  , 'title:es'=>'Tecnología'
                  , 'title:en'=>'Technology'
                  ]
        );

        $cms->createInstance($instanceNewsItem);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem);
        $cms->createInstance($instanceCategory1);
        $idCategory1=$instanceCategory1->ID();
        
        $instanceCategoryArray2=['metadata'=>[
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


        $instanceCategory2=$cms->createInstanceFromArray($instanceCategoryArray2);
        $idCategory2=$instanceCategory2->ID();
        $instancesInStorage=$cms->getAllInstances();

        $this->assertTrue(array_key_exists($idCategory1, $instancesInStorage));
        $this->assertTrue(array_key_exists($idCategory2, $instancesInStorage));

        $onlyNewsItemInstances=$cms->filterInstances($instancesInStorage, function ($instance) {
            if ($instance->getClassKey()=='news-item') {
                return $instance;
            }
        });
        
        foreach ($onlyNewsItemInstances as $newsID=>$newsInstance) {
            $this->assertTrue($newsInstance->getData('es')['title']=='Primer titular de la noticia');
        }
    }
}
