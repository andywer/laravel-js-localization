<?php

use JsLocalization\JsLocalizationServiceProvider;

class JsLocalizationServiceProviderTest extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders()
    {
        return array('JsLocalization\JsLocalizationServiceProvider');
    }

    public function testRegisteredNamespaces ()
    {
        $this->assertArrayHasKey(
            'js-localization', Config::getNamespaces(),
            'Configuration namespace not registered: js-localization'
        );

        $this->assertArrayHasKey(
            'js-localization', View::getFinder()->getHints(),
            'View namespace not registered: js-localization'
        );
    }

    public function testRegisteredCommands ()
    {
        $artisan = Artisan::getArtisan();

        $this->assertTrue(
            $artisan->has('js-localization:refresh'),
            'js-localization:refresh command is not registered.'
        );

        $refreshCommand = $artisan->get('js-localization:refresh');
        $this->assertInstanceOf('JsLocalization\Console\RefreshCommand', $refreshCommand);
    }

    public function testBindings ()
    {
        $helper = App::make('JsLocalizationHelper');
        $this->assertInstanceOf('JsLocalization\Helper', $helper);

        $cachingService = App::make('JsLocalizationCachingService');
        $this->assertInstanceOf('JsLocalization\CachingService', $cachingService);
    }

}