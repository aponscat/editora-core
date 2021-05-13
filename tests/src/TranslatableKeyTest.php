<?php
declare(strict_types=1);
namespace Omatech\EditoraTest;

use PHPUnit\Framework\TestCase;
use Omatech\Editora\Data\TranslatableKey;
use Omatech\Editora\Adapters\ArrayTranslationsStorageAdapter;

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

    public function testMultipleKeys(): void
    {
        $langData=['es'=>'Guardar', 'en'=>'Save', 'fr'=>'Sauvegarder'];
        $save=TranslatableKey::createFromJson('cms.save', json_encode($langData));

        $langData=['es'=>'Descargar', 'en'=>'Download', 'fr'=>'Telecharger'];
        $download=TranslatableKey::createFromJson('cms.download', json_encode($langData));
        
        ArrayTranslationsStorageAdapter::put($save);
        ArrayTranslationsStorageAdapter::put($download);

        //print_r(ArrayTranslationsStorageAdapter::jsonSerialize());

        $this->assertTrue(ArrayTranslationsStorageAdapter::getTranslation('cms.save', 'es')=='Guardar');
        $this->assertTrue(ArrayTranslationsStorageAdapter::getTranslation('cms.save', 'en')=='Save');
        $this->assertTrue(ArrayTranslationsStorageAdapter::getTranslation('cms.save', 'fr')=='Sauvegarder');
        $this->assertTrue(ArrayTranslationsStorageAdapter::getTranslation('cms.download', 'es')=='Descargar');
        $this->assertTrue(ArrayTranslationsStorageAdapter::getTranslation('cms.download', 'en')=='Download');
        $this->assertTrue(ArrayTranslationsStorageAdapter::getTranslation('cms.download', 'fr')=='Telecharger');
    }
}
