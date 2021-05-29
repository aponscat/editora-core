<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\Clas;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Domain\CmsData\Instance;
use Omatech\Editora\Domain\CmsData\Value;
use Omatech\Editora\Domain\CmsData\PublishingInfo;

class InstancePublicationTest extends TestCase
{
    public function testIsPublishedAfterCreate(): void
    {
        $jsonAttributes=json_encode([
            ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
          ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!']);
            
        $this->assertTrue(
            $instance->getData()==
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
              ,"english-title" => "Hello World Title!"
              ]
        );
        $this->assertTrue($instance->isPublished());
    }

    public function testIsPublishedDifferentStatus(): void
    {
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo());
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
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('O'));
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
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('O'));
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
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('O'));


        $instance->setStartPublishingDate(strtotime('+1 week'));
        $this->expectException(\Exception::class);
        $instance->setEndPublishingDate(strtotime('-1 week'));
    }

    public function testInvalidPublishingDates2():void
    {
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('O'));


        $instance->setEndPublishingDate(strtotime('-1 week'));
        $this->expectException(\Exception::class);
        $instance->setStartPublishingDate(strtotime('+1 week'));
    }

    public function testInvalidPublishingDates3():void
    {
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('O'));

        $this->expectException(\Exception::class);
        $instance->setPublishingDates(strtotime('+1 week'), strtotime('-1 week'));
    }


    public function testInvalidPublishingDates4():void
    {
        $jsonAttributes=json_encode([
                ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
              ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('O', strtotime('+1 week'), strtotime('-1 week')));
    }

    public function testSetInvalidStatusXInCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('X'));
    }

    public function testSetInvalidStatusXAfterCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('P'));
        $this->assertFalse($instance->isPublished());
        $this->expectException(\Exception::class);
        $instance->setStatus('X');
    }

    public function testSetInvalidStatusXXXInCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('XXX'));
    }

    public function testSetInvalidStatusXXXAfterCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::create($class, 'news-item-instance', ['english-title:en'=>'Hello World Title!'], new PublishingInfo('P'));
        $this->assertFalse($instance->isPublished());
        $this->expectException(\Exception::class);
        $instance->setStatus('XXX');
    }
}
