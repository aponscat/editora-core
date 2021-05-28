<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\Cms;
use Omatech\Editora\Domain\CmsStructure\CmsStructure;
use Omatech\Editora\Adapters\ArrayStorageAdapter;
use Omatech\Editora\Data\Instance;
use Omatech\Editora\Domain\CmsStructure\Relation;
use Omatech\Editora\Domain\CmsStructure\Clas;

class SerializeUnserializeTest extends TestCase
{
    public function testLoadStructureFromSimpleModernJSONSerializeAndUnserialize(): void
    {
        $publicPath='/images';
        $originalFilename='result.jpg';
        $jsonStructure=file_get_contents(dirname(__FILE__).'/../data/simple_modern.json');
        $structure=CmsStructure::loadStructureFromJSON($jsonStructure);
        $storage=new ArrayStorageAdapter($structure);
        $cms=new Cms($structure, $storage);

        $instance1='
        {
          "metadata":{"status":"O"
            ,"class":"news-item"
            ,"key":"first-news-item"}
            ,"values":[
            {"title:en":"First news item title"}
            ,{"title:es":"Primer titular de la noticia"}
            ,{"image-with-alt-and-title":
            [{"original-filename":"fff"}
            ,{"data":"aaaa"}
            ]}
          ]
        }';

        $id1=$cms->putJSONInstance($instance1);

        $instance2='
        {
        "metadata":{"status":"O"
            ,"class":"news-category"
            ,"key":"society"}
            ,"values":[
            {"code":"society"}
            ,{"title:es":"Sociedad"}
            ,{"title:en":"Society"}
            ]
        }';

        $id2=$cms->putJSONInstance($instance2);

        foreach ($storage->all() as $instance)
        {
            if($instance->getKey()=='first-news-item')
            {
                $this->assertTrue($instance->getData('en')['title']=='First news item title');
                $this->assertTrue($instance->getData('es')['title']=='Primer titular de la noticia');
            }
            if($instance->getKey()=='society')
            {
                $this->assertTrue($instance->getData('en')['title']=='Society');
                $this->assertTrue($instance->getData('es')['title']=='Sociedad');
            }

        }

    }
}
