<?php

use JsLocalization\JsLocalizationServiceProvider;
use Symfony\Component\Console\Tester\CommandTester;

class JsLocalizationServiceProviderTest extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return ['JsLocalization\JsLocalizationServiceProvider'];
    }


    public function testProvidesArray () {
        $service = new JsLocalizationServiceProvider($this->app);

        $this->assertEquals( $service->provides(), ['js-localization'] );
    }

    public function testRegisteredNamespaces ()
    {
        $this->assertEquals(['en'], Config::get('js-localization.locales'));
        $this->assertEquals([], Config::get('js-localization.messages'));
        
        $this->assertArrayHasKey(
            'js-localization', View::getFinder()->getHints(),
            'View namespace not registered: js-localization'
        );
    }

    public function testRegisteredCommands ()
    {
        $allCommands = Artisan::all();
        
        /** @var \Symfony\Component\Console\Command\Command $command */
        foreach ($allCommands as $command) {
            if ($command->getName() === 'js-localization:refresh') { break; }
        }
        
        $refreshCommand = $command;
        $this->assertInstanceOf('JsLocalization\Console\RefreshCommand', $refreshCommand);

        $commandTester = new CommandTester($refreshCommand);
        $commandTester->execute([]);
        $this->assertEquals("Refreshing the message cache...\n", $commandTester->getDisplay());
    }

    public function testBindings ()
    {
        $helper = App::make('JsLocalizationHelper');
        $this->assertInstanceOf('JsLocalization\Utils\Helper', $helper);

        $cachingService = App::make('JsLocalizationCachingService');
        $this->assertInstanceOf('JsLocalization\Caching\CachingService', $cachingService);
    }

}
