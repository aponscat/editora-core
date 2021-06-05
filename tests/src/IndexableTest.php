<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Structure\Structure;
use Omatech\Editora\Infrastructure\Persistence\Memory\InstanceRepository;
use Omatech\Editora\Infrastructure\Persistence\File\YamlStructureRepository;
use Omatech\Editora\Application\Cms;
use Omatech\Editora\Domain\Data\Instance;

class IndexableTest extends TestCase
{
    private Structure $structure;

    public function setUp(): void
    {
        parent::setUp();
        $this->structure = YamlStructureRepository::read(__DIR__ .'/../data/editora_simple.yml');
    }
    
    public function testIndexableValues(): void
    {
        $structure=$this->structure;
        $storage=new InstanceRepository($structure);
        $cms=new Cms($structure, $storage);
        $class=$cms->getClass('confidential-class');

        $instance=Instance::create(
            $class,
            'secret1',
            ['title:en'=>'First title of secret item'
            , 'title:es'=>'Primer titular del elemento secreto'
            , 'text:en'=>'EN Lorem fistrum amatomaa se calle ustée ese pedazo de a gramenawer fistro caballo blanco caballo negroorl. Apetecan no puedor hasta luego Lucas ahorarr'
            , 'text:es'=>'ES Lorem fistrum amatomaa se calle ustée ese pedazo de a gramenawer fistro caballo blanco caballo negroorl. Apetecan no puedor hasta luego Lucas ahorarr'
            , 'secret:en'=>'secret wordnottobefound'
            , 'secret:es'=>'secreto palabranoencontrable'
            ]
        );

        $this->assertTrue($instance->getIndexableData('es')==
        ['title' => 'Primer titular del elemento secreto'
        ,'text' => 'ES Lorem fistrum amatomaa se calle ustée ese pedazo de a gramenawer fistro caballo blanco caballo negroorl. Apetecan no puedor hasta luego Lucas ahorarr'
        ]);

        $this->assertTrue($instance->getIndexableData('en')==
        ['title' => 'First title of secret item'
        ,'text' => 'EN Lorem fistrum amatomaa se calle ustée ese pedazo de a gramenawer fistro caballo blanco caballo negroorl. Apetecan no puedor hasta luego Lucas ahorarr'
        ]);

        $this->assertTrue($instance->getIndexableData()==
        ['title:es' => 'Primer titular del elemento secreto'
        ,'text:es' => 'ES Lorem fistrum amatomaa se calle ustée ese pedazo de a gramenawer fistro caballo blanco caballo negroorl. Apetecan no puedor hasta luego Lucas ahorarr'
        ,'title:en' => 'First title of secret item'
        ,'text:en' => 'EN Lorem fistrum amatomaa se calle ustée ese pedazo de a gramenawer fistro caballo blanco caballo negroorl. Apetecan no puedor hasta luego Lucas ahorarr'
        ]);

        $this->assertTrue($instance->getData('en')['secret']=='secret wordnottobefound');
        $this->assertTrue($instance->getData('es')['secret']=='secreto palabranoencontrable');
        $this->assertTrue($instance->getData()['secret:en']=='secret wordnottobefound');

        $cms->putInstance($instance);

        $onlyInstancesWithText=$cms->filterInstances($cms->getAllInstances()
        , function ($instance) {
            //print_r($instance->getIndexableData());
            foreach ($instance->getIndexableData() as $value)
            {
                if (stripos($value, 'secret'))
                {
                    return $instance;
                }
            }
        });

        $onlyInstancesWithText=$cms->filterInstances($cms->getAllInstances()
        , function ($instance) {
            //print_r($instance->getIndexableData());
            foreach ($instance->getIndexableData() as $value)
            {
                if (stripos($value, 'wordnottobefound'))
                {
                    return $instance;
                }
            }
        });
        $this->assertTrue(empty($onlyInstancesWithText));

        $onlyInstancesWithText=$cms->filterInstances($cms->getAllInstances()
        , function ($instance) {
            //print_r($instance->getIndexableData());
            foreach ($instance->getIndexableData('en') as $value)
            {
                if (stripos($value, 'wordnottobefound'))
                {
                    return $instance;
                }
            }
        });
        $this->assertTrue(empty($onlyInstancesWithText));

        $onlyInstancesWithText=$cms->filterInstances($cms->getAllInstances()
        , function ($instance) {
            //print_r($instance->getIndexableData());
            foreach ($instance->getIndexableData('es') as $value)
            {
                if (stripos($value, 'palabranoencontrable'))
                {
                    return $instance;
                }
            }
        });
        $this->assertTrue(empty($onlyInstancesWithText));


    }
}
