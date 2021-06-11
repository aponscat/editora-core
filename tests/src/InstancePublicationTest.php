<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Clazz;
use Omatech\Editora\Domain\Structure\Attribute;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Domain\Data\Instance;
use Omatech\Editora\Domain\Data\Value;
use Omatech\Editora\Domain\Data\Publication;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;

class InstancePublicationTest extends TestCase
{

  private Structure $structure;

  public function setUp(): void
  {
      parent::setUp();
      $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_ultrasimple.yml');
  }

    public function testIsPublishedAfterCreate(): void
    {
        $class=$this->structure->getClass('news-item');
        $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);
            
        $this->assertTrue(
            $instance->getData('en')==
            [
              "english-title" => "Hello World Title!"
            ]
        );
        $this->assertTrue($instance->isPublished());
    
        $this->assertTrue(
            $instance->getData('ALL', true)==
              ['metadata' => [
                  'status' => 'O'
                  ,'class'=>'news-item'
                  , "key" => "news-item-instance"
              ]
              ,"english-title:en" => "Hello World Title!"
              ]
        );
        $this->assertTrue($instance->isPublished());
    }

    public function testIsPublishedDifferentStatus(): void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);
        $this->assertTrue($instance->isPublished());
        $instance->setStatus('P');
        $this->assertFalse($instance->isPublished());

        $instance->setStatus('V');
        $this->assertFalse($instance->isPublished());

        $instance->setStatus('O');
        $this->assertTrue($instance->isPublished());
    }


    public function testIsPublishedNow(): void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);
        $this->assertTrue($instance->isPublished());

        $instance->setStartPublishingDate(strtotime('-1 week'));
        $instance->setEndPublishingDate();
        $this->assertTrue($instance->isPublished());

        $instance->setStartPublishingDate();
        $instance->setEndPublishingDate(strtotime('+1 week'));
        $this->assertTrue($instance->isPublished());

        $instance->setStartPublishingDate(strtotime('-1 week'));
        $instance->setEndPublishingDate(strtotime('+1 week'));
        $this->assertTrue($instance->isPublished());

        $instance->setStartPublishingDate();
        $instance->setEndPublishingDate();
        $instance->setStartPublishingDate(strtotime('+1 week'));
        $this->assertFalse($instance->isPublished());

        $instance->setStartPublishingDate();
        $instance->setEndPublishingDate(strtotime('-1 week'));
        $this->assertFalse($instance->isPublished());
    }


    public function testIsPublishedNowAtOnce(): void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);
        $this->assertTrue($instance->isPublished());

        $instance->setPublishingDates(strtotime('-1 week'));
        $this->assertTrue($instance->isPublished());
 
        $instance->setPublishingDates(null, strtotime('+1 week'));
        $this->assertTrue($instance->isPublished());

        $instance->setPublishingDates(strtotime('-1 week'), strtotime('+1 week'));
        $this->assertTrue($instance->isPublished());

        $instance->setPublishingDates(strtotime('+1 week'));
        $this->assertFalse($instance->isPublished());

        $instance->setPublishingDates(null, strtotime('-1 week'));
        $this->assertFalse($instance->isPublished());
    }



    public function testInvalidPublishingDates1():void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);


        $instance->setStartPublishingDate(strtotime('+1 week'));
        $this->expectException(\Exception::class);
        $instance->setEndPublishingDate(strtotime('-1 week'));
    }

    public function testInvalidPublishingDates2():void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);


        $instance->setEndPublishingDate(strtotime('-1 week'));
        $this->expectException(\Exception::class);
        $instance->setStartPublishingDate(strtotime('+1 week'));
    }

    public function testInvalidPublishingDates3():void
    {
      $class=$this->structure->getClass('news-item');
      $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!']);

        $this->expectException(\Exception::class);
        $instance->setPublishingDates(strtotime('+1 week'), strtotime('-1 week'));
    }


    public function testInvalidPublishingDates4():void
    {
      $class=$this->structure->getClass('news-item');
      $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!'], null, new Publication('O', strtotime('+1 week'), strtotime('-1 week')));
    }

    public function testSetInvalidStatusXInCreation(): void
    {
      $class=$this->structure->getClass('news-item');
      $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!'], null, new Publication('X'));
    }

    public function testSetInvalidStatusXAfterCreation(): void
    {
      $class=$this->structure->getClass('news-item');
      $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!'], null, new Publication('P'));
        $this->assertFalse($instance->isPublished());
        $this->expectException(\Exception::class);
        $instance->setStatus('X');
    }

    public function testSetInvalidStatusXXXInCreation(): void
    {
      $class=$this->structure->getClass('news-item');
      $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!'], null, new Publication('XXX'));
    }

    public function testSetInvalidStatusXXXAfterCreation(): void
    {
      $class=$this->structure->getClass('news-item');
      $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance'
        , ['english-title:en'=>'Hello World Title!'], null, new Publication('P'));
        $this->assertFalse($instance->isPublished());
        $this->expectException(\Exception::class);
        $instance->setStatus('XXX');
    }
}
