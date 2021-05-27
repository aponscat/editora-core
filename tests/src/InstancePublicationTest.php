<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Structure\Clas;
use Omatech\Editora\Structure\Attribute;
use Omatech\Editora\Data\Instance;
use Omatech\Editora\Values\Value;

class InstancePublicationTest extends TestCase
{
    public function testIsPublishedAfterCreate(): void
    {
        $jsonAttributes=json_encode([
            ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
          ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));
            
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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));
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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));
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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));
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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));


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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));


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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]));

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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'O', json_encode([['english-title:en'=>'Hello World Title!']]), strtotime('+1 week'), strtotime('-1 week'));
    }

    public function testSetInvalidStatusXInCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'X', json_encode([['english-title:en'=>'Hello World Title!']]));
    }

    public function testSetInvalidStatusXAfterCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'P', json_encode([['english-title:en'=>'Hello World Title!']]));
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
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'XXX', json_encode([['english-title:en'=>'Hello World Title!']]));
    }

    public function testSetInvalidStatusXXXAfterCreation(): void
    {
        $jsonAttributes=json_encode([
        ['key'=>'english-title:en', 'config'=>['mandatory'=>true]]
      ]);
        $class=Clas::createFromJSON('news-item', $jsonAttributes);
        $this->expectException(\Exception::class);
        $instance=Instance::createFromJSON($class, 'news-item-instance', 'P', json_encode([['english-title:en'=>'Hello World Title!']]));
        $this->assertFalse($instance->isPublished());
        $this->expectException(\Exception::class);
        $instance->setStatus('XXX');
    }
}
