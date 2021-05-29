<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Cms;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;
use Omatech\Editora\Infrastructure\Persistence\Memory\ArrayStorageAdapter;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Domain\CmsStructure\Clas;

class CmsTest extends TestCase
{
    public function testLoadStructureFromReverseEngeeneredJSON(): void
    {
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/test_structure.json');
        $structure=CmsStructure::loadStructureFromReverseEngineeredJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        $countryClass=$cms->getClass('Countries');

        $instance=Instance::create(
            $countryClass,
            'country-es',
            ['country_code'=>'es'
                  , 'title:es'=>'España'
                  , 'title:en'=>'Spain'
                  ]
        );
        $this->assertTrue($instance->getData('es')==
            ['country_code' => 'es'
            ,'title' => 'España']);

        $cms->putInstance($instance);
        $instance2=$cms->getInstanceByID($instance->ID());
        $this->assertTrue($instance2->getData('es')==$instance->getData('es'));
        $this->assertTrue($instance2->getData('en')==$instance->getData('en'));
    }


    public function testSaveStructureToSimpleModernJSON(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonAttributes=json_encode([
            ['key'=>'title:en', 'config'=>['mandatory'=>true]]
            , ['key'=>'text:en']
            , ['key'=>'title:es']
            , ['key'=>'text:es']
            , ['key'=>'multilang-attribute']
            , ['key'=>'image-with-alt-and-title'
            , 'type'=>'Omatech\Editora\Domain\CmsStructure\ImageAttribute'
            , 'valueType'=>'Omatech\Editora\Domain\CmsData\ImageValue'
              , 'config'=>
              ['mandatory'=>true
              , 'dimensions'=>'600x600'
              , 'storage-path'=>dirname(__FILE__)
              , 'public-path'=>$publicPath
              , 'adapters'=>['media'=>'Omatech\Editora\Adapters\ArrayMediaAdapter']
              , 'subattributes'=>[
                ['key'=>'alt:en']
                , ['key'=>'alt:es']
                , ['key'=>'title:en']
                , ['key'=>'title:es']
                , ['key'=>'code']
              ]
          ]]
          ]);

        $newsItem=Clas::createFromJSON('news-item', $jsonAttributes);
    
        $jsonAttributes=json_encode([
            ['key'=>'title:en', 'config'=>['mandatory'=>true]]
            , ['key'=>'title:es']
            , ['key'=>'code']
          ]);
        $category=Clas::createFromJSON('news-category', $jsonAttributes);

        $structure=CmsStructure::createEmptyStructure();
        $structure->addLanguage('es');
        $structure->addLanguage('en');

        $category->addRelation(new Relation('news', ['news-item']));
        $structure->addClass($category);
        $structure->addClass($newsItem);

        file_put_contents(dirname(__FILE__).'/../data/simple_modern.json', json_encode($structure->toArray(), JSON_PRETTY_PRINT));

        $this->assertTrue(true);
    }


    public function testLoadStructureFromSimpleModernJSON(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        $countryClass=$cms->getClass('news-item');

        $instance=Instance::create(
            $countryClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
                  , 'title:es'=>'Primer titular de la noticia'
                  ,'image-with-alt-and-title'=>
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
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
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
                  ]
                  ]
        );
        $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');

        $cms->putInstance($instance);
        $id1=$instance->ID();
        $instance2=$cms->getInstanceById($id1);
        $this->assertTrue($instance2->getData('es')['title']=='Primer titular de la noticia');

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



    public function testSaveCategoryAndRelatedNews(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

        $instanceNewsItem=Instance::create(
            $newsItemClass,
            'first-news-item',
            ['title:en'=>'First title of a news item'
                , 'title:es'=>'Primer titular de la noticia'
                ,'image-with-alt-and-title'=>
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

        $cms->putInstance($instanceNewsItem);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem);
        $cms->putInstance($instanceCategory1);
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


        $instanceCategory2=$cms->putArrayInstance($instanceCategoryArray2);
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
