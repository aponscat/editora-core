<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Cms;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;
use Omatech\Editora\Infrastructure\Persistence\Memory\ArrayInstanceRepository;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsData\RelationInstances;

class InstancesRelationsTest extends TestCase
{
    public function testRelationSimpleOperations(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayInstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

        $instanceNewsItem1=Instance::create(
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

        $instanceNewsItem2=Instance::create(
            $newsItemClass,
            'second-news-item',
            ['title:en'=>'Second title of a news item'
                , 'title:es'=>'Segundo titular de la noticia'
                ,'image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
                ]
        );

        $instanceNewsItem3=Instance::create(
            $newsItemClass,
            'third-news-item',
            ['title:en'=>'Third title of a news item'
                , 'title:es'=>'Tercer titular de la noticia'
                ,'image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
                ]
        );

        $categoryClass=$cms->getClass('news-category');
        $instanceCategory1=Instance::create(
            $categoryClass,
            'tech',
            ['code'=>'tech'
                  , 'title:es'=>'Tecnología'
                  , 'title:en'=>'Technology'
                  ]
        );

        $cms->putInstance($instanceNewsItem1);
        $cms->putInstance($instanceNewsItem2);
        $cms->putInstance($instanceNewsItem3);

        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem1);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem2);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem3);

        //print_r($instanceCategory1->getRelationsArray());

        $cms->putInstance($instanceCategory1);

        $instancesInStorage=$cms->getAllInstances();

        $this->assertTrue(array_key_exists($instanceCategory1->ID(), $instancesInStorage));

        $recoveredCategoryInstance=$cms->getInstanceByID($instanceCategory1->ID());

        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue(in_array($instanceNewsItem1->ID(), $children));
        $this->assertTrue(in_array($instanceNewsItem2->ID(), $children));
        $this->assertTrue(in_array($instanceNewsItem3->ID(), $children));

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID(), false);
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue(in_array($instanceNewsItem1->ID(), $children));
        $this->assertFalse(in_array($instanceNewsItem2->ID(), $children));
        $this->assertTrue(in_array($instanceNewsItem3->ID(), $children));

        // try to remove the same element
        $this->expectException(\Exception::class);
        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID(), false);
    }

    public function testRelationOrderOperations(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayInstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $newsItemClass=$cms->getClass('news-item');

        $instanceNewsItem1=Instance::create(
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

        $instanceNewsItem2=Instance::create(
            $newsItemClass,
            'second-news-item',
            ['title:en'=>'Second title of a news item'
                , 'title:es'=>'Segundo titular de la noticia'
                ,'image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
                ]
        );

        $instanceNewsItem3=Instance::create(
            $newsItemClass,
            'third-news-item',
            ['title:en'=>'Third title of a news item'
                , 'title:es'=>'Tercer titular de la noticia'
                ,'image-with-alt-and-title'=>
                ['original-filename'=>$originalFilename
                , 'data'=>chunk_split(base64_encode(file_get_contents(dirname(__FILE__).'/../data/sample-image-640x480.jpeg')))
                ]
                ]
        );

        $categoryClass=$cms->getClass('news-category');
        $instanceCategory1=Instance::create(
            $categoryClass,
            'tech',
            ['code'=>'tech'
                  , 'title:es'=>'Tecnología'
                  , 'title:en'=>'Technology'
                  ]
        );

        $cms->putInstance($instanceNewsItem1);
        $cms->putInstance($instanceNewsItem2);
        $cms->putInstance($instanceNewsItem3);

        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem1);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem2, RelationInstances::ABOVE);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem3, RelationInstances::ABOVE);

        $children=$instanceCategory1->getChildrenIDsByRelationKey('news');

        echo "\nChildren order\n";
        foreach ($children as $key=>$childID)
        {
            echo "[$key]=>$childID:".$cms->getInstanceByID($childID)->getKey()."\n";
        }

        $cms->putInstance($instanceCategory1);
        $recoveredCategoryInstance=$cms->getInstanceByID($instanceCategory1->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        echo "\nChildren order after recovery\n";
        foreach ($children as $key=>$childID)
        {
            echo "[$key]=>$childID:".$cms->getInstanceByID($childID)->getKey()."\n";
        }

    }





}
