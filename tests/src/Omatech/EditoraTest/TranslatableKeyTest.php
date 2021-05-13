<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Data\TranslatableKey;

class TranslatableKeyTest extends TestCase
{
    public function testOkAfterCreate(): void
    {
        $langData=['es'=>'Guardar', 'en'=>'Save', 'fr'=>'Sauvegarder'];
        $save=TranslatableKey::createFromJson('cms.save', json_encode($langData));
        $this->assertTrue('Guardar'==$save->get('es'));
        $this->assertTrue('Save'==$save->get('en'));
        $this->assertTrue('Sauvegarder'==$save->get('fr'));
        $this->assertTrue('cms.save'==$save->get('de'));
    }

}
