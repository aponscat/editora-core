<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Domain\CmsStructure\Attribute;
use Omatech\Editora\Domain\CmsData\Value;

class ValueTest extends TestCase
{
    public function testGetValueAfterCreate(): void
    {
        $key='english-text-attribute';
        $fullyQualifyedKey="$key:en";
        $atri=new Attribute($fullyQualifyedKey);
        $val=new Value($atri, 'Hello World!');
        $this->assertTrue($val->getValue()=='Hello World!');
    }

    public function testGetEnglishDataAfterCreate(): void
    {
        $key='english-text-attribute';
        $fullyQualifyedKey="$key:en";
        $atri=new Attribute($fullyQualifyedKey);
        $textValue='Hello World!';
        $value=new Value($atri, $textValue);

        $this->assertTrue($value->getData('en')==[$key=>$textValue]);
    }

    public function testGetMultiLangDataAfterCreate(): void
    {
        $key='multilang-text-attribute';
        $atri=new Attribute($key);
        $textValue='hello-world-multilang';
        $value=new Value($atri, $textValue);
        $this->assertTrue($value->getData()==[$key=>$textValue]);
    }
}
