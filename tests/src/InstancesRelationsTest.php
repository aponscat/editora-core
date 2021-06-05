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
use Omatech\Editora\Domain\Data\Link;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class InstancesRelationsTest extends TestCase
{

    private Structure $structure;

    public function setUp(): void
    {
        parent::setUp();
        $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_simple.yml');
    }
    
    public function testRelationSimpleOperations(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
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

        $cms->createInstance($instanceNewsItem1);
        $cms->createInstance($instanceNewsItem2);
        $cms->createInstance($instanceNewsItem3);

        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem1);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem2);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem3);

        //print_r($instanceCategory1->getRelationsArray());

        $cms->createInstance($instanceCategory1);

        $instancesInStorage=$cms->getAllInstances();

        $this->assertTrue(array_key_exists($instanceCategory1->ID(), $instancesInStorage));

        $recoveredCategoryInstance=$cms->getInstanceByID($instanceCategory1->ID());

        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue(in_array($instanceNewsItem1->ID(), $children));
        $this->assertTrue(in_array($instanceNewsItem2->ID(), $children));
        $this->assertTrue(in_array($instanceNewsItem3->ID(), $children));

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID(), true);
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue(in_array($instanceNewsItem1->ID(), $children));
        $this->assertFalse(in_array($instanceNewsItem2->ID(), $children));
        $this->assertTrue(in_array($instanceNewsItem3->ID(), $children));

        // try to remove the same element
        $this->expectException(\Exception::class);
        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID(), true);
    }

    public function testRelationOrderOperations(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
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

        $cms->createInstance($instanceNewsItem1);
        $cms->createInstance($instanceNewsItem2);
        $cms->createInstance($instanceNewsItem3);

        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem1);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem2, Link::ABOVE);
        $instanceCategory1->addToRelationByKey('news', $instanceNewsItem3, Link::ABOVE);

        $children=$instanceCategory1->getChildrenIDsByRelationKey('news');

        $this->assertTrue($children[0]==$instanceNewsItem3->ID());
        $this->assertTrue($children[1]==$instanceNewsItem2->ID());
        $this->assertTrue($children[2]==$instanceNewsItem1->ID());

        $cms->createInstance($instanceCategory1);
        $recoveredCategoryInstance=$cms->getInstanceByID($instanceCategory1->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');

        $this->assertTrue($children[0]==$instanceNewsItem3->ID());
        $this->assertTrue($children[1]==$instanceNewsItem2->ID());
        $this->assertTrue($children[2]==$instanceNewsItem1->ID());

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem1->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children[0]==$instanceNewsItem3->ID());
        $this->assertTrue($children[1]==$instanceNewsItem2->ID());

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID());
        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem3->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children==[]);

        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem1);
        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem2, Link::BELOW);
        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem3, Link::BELOW);
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children[0]==$instanceNewsItem1->ID());
        $this->assertTrue($children[1]==$instanceNewsItem2->ID());
        $this->assertTrue($children[2]==$instanceNewsItem3->ID());

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children[0]==$instanceNewsItem1->ID());
        $this->assertTrue($children[1]==$instanceNewsItem3->ID());

        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem2, Link::BELOW, $instanceNewsItem1->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');

        $this->assertTrue($children[0]==$instanceNewsItem1->ID());
        $this->assertTrue($children[1]==$instanceNewsItem2->ID());
        $this->assertTrue($children[2]==$instanceNewsItem3->ID());

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem1->ID());
        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem2->ID());
        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem3->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children==[]);

        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem1, Link::BELOW);
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children[0]==$instanceNewsItem1->ID());

        $recoveredCategoryInstance->removeFromRelationByKeyAndID('news', $instanceNewsItem1->ID());
        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem1, Link::ABOVE);
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children[0]==$instanceNewsItem1->ID());

        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem2, Link::ABOVE, $instanceNewsItem1->ID());
        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem3, Link::BELOW, $instanceNewsItem2->ID());
        $children=$recoveredCategoryInstance->getChildrenIDsByRelationKey('news');
        $this->assertTrue($children[0]==$instanceNewsItem2->ID());
        $this->assertTrue($children[1]==$instanceNewsItem3->ID());
        $this->assertTrue($children[2]==$instanceNewsItem1->ID());

        $this->expectException(\Exception::class);
        $recoveredCategoryInstance->addToRelationByKey('news', $instanceNewsItem1, Link::BELOW, 'xxxx', true);
    }
}
