<?php

use JsLocalization\JsLocalizationServiceProvider;
use JsLocalization\Console\RefreshCommand;
use Symfony\Component\Console\Tester\CommandTester;

class JsLocalizationServiceProviderTest extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders()
    {
        return array('JsLocalization\JsLocalizationServiceProvider');
    }


    public function testProvidesArray () {
        $service = new JsLocalization\JsLocalizationServiceProvider($this->app);

        $this->assertEquals( $service->provides(), array('js-localization') );
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
        $refreshCommand = Artisan::find('js-localization:refresh');
        $this->assertInstanceOf('JsLocalization\Console\RefreshCommand', $refreshCommand);

        $commandTester = new CommandTester($refreshCommand);
        $commandTester->execute(array());
        $this->assertEquals("Refreshing the message cache...\n", $commandTester->getDisplay());
    }

    public function testBindings ()
    {
        $helper = App::make('JsLocalizationHelper');
        $this->assertInstanceOf('JsLocalization\Helper', $helper);

        $cachingService = App::make('JsLocalizationCachingService');
        $this->assertInstanceOf('JsLocalization\CachingService', $cachingService);
    }

}
