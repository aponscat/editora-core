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
        $atri=new Attribute('english-text-attribute', 'en');
        $val=new Value($atri, 'Hello World!');
        $this->assertTrue($val->getValue()=='Hello World!');
    }

    public function testGetEnglishDataAfterCreate(): void
    {
        $key='english-text-attribute';
        $atri=new Attribute($key, 'en');
        $textValue='Hello World!';
        $value=new Value($atri, $textValue);
        $this->assertTrue($value->getData()==[$key=>$textValue]);
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
