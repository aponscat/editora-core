<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\BaseValue;
use Omatech\Editora\BaseAttribute;

class ValueTest extends TestCase
{
    public function testGetValueAfterCreate(): void
    {
        $atri=new BaseAttribute('english-text-attribute', 'en');
        $val=new BaseValue($atri, 'Hello World!');
        $this->assertTrue($val->getValue()=='Hello World!');
    }

    public function testGetEnglishDataAfterCreate(): void
    {
        $key='english-text-attribute';
        $atri=new BaseAttribute($key, 'en');
        $textValue='Hello World!';
        $value=new BaseValue($atri, $textValue);
        $this->assertTrue($value->getData()==[$key=>$textValue]);
    }

    public function testGetMultiLangDataAfterCreate(): void
    {
        $key='multilang-text-attribute';
        $atri=new BaseAttribute($key);
        $textValue='hello-world-multilang';
        $value=new BaseValue($atri, $textValue);
        $this->assertTrue($value->getData()==[$key=>$textValue]);
    }
}
