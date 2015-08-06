<?php

use Mockery as m;

class JsLocalizationControllerTest extends TestCase
{
    
    public function testCreateJsMessages()
    {
        // Prepare & Request

        $this->mockLang($locale = 'en');
        Artisan::call('js-localization:refresh');

        $response = $this->call('GET', '/js-localization/messages');
        $content = $response->getContent();

        $this->assertTrue($response->isOk());

        // Test for Lang.setLocale()

        $this->assertRegExp('/Lang\.setLocale\("'.$locale.'"\);/', $content);

        // Test for Lang.addMessages()

        $this->assertLangAddMessages($content, $this->testMessagesFlat);
    }
    
    public function testCreateJsConfig()
    {
        // Prepare & Request

        $this->mockLang($locale = 'en');
        Artisan::call('js-localization:refresh');

        $response = $this->call('GET', '/js-localization/config');
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        
        $this->assertNotNull($response->getLastModified());
        $this->assertNull($response->getEtag());

        // Test for Config.addConfig()
        
        $this->assertConfigAddConfig($content, $this->testConfigExportFlat);
    }
    
    public function testCreateJsConfigWithCacheDisabled()
    {
        // Prepare & Request

        $this->mockLang($locale = 'en');
        Config::set('js-localization.disable_config_cache', true);

        $response = $this->call('GET', '/js-localization/config');
        $content = $response->getContent();

        $this->assertTrue($response->isOk());

        $this->assertNull($response->getLastModified());
        $this->assertEquals('"'.md5($content).'"', $response->getEtag());

        // Test for Config.addConfig()

        $this->assertConfigAddConfig($content, $this->testConfigExportFlat);
    }

    public function testBackwardsCompatibility()
    {
        // Prepare & Request

        $this->mockLang($locale = 'en');
        $this->mockMessageCachingService($this->testMessagesFlat['en']);

        $response = $this->call('GET', '/js-localization/messages');
        $content = $response->getContent();

        $this->assertTrue($response->isOk());

        // Test for Lang.addMessages()

        $this->assertLangAddMessages($content, $this->testMessagesFlat);
    }

    private function mockMessageCachingService(array $messages)
    {
        $service = m::mock('CachingServiceMock');
        JsLocalization\Facades\MessageCachingService::swap($service);
        
        $service->shouldReceive('getMessagesJson')
            ->andReturn(json_encode($messages));

        $service->shouldReceive('getLastRefreshTimestamp')
            ->andReturn(new DateTime);
    }

    private function assertLangAddMessages($jsContent, array $expectedMessages)
    {
        $this->assertJsCall($jsContent, 'Lang.addMessages', $expectedMessages);
    }
    
    protected function assertConfigAddConfig($jsContent, array $expectedConfig)
    {
        $this->assertJsCall($jsContent, 'Config.addConfig', $expectedConfig);
    }
    
    protected function assertJsCall($jsContent, $functionName, $functionParam)
    {
        $functionName = str_replace('.', '\\.', $functionName);
        $functionNameRegex = '/' . $functionName . '\( (\{.*?\}) \);/x';

        preg_match($functionNameRegex, $jsContent, $matches);
        $paramJson = $matches[1];

        $this->assertJson($paramJson);
        $parsedParam = json_decode($paramJson, true);

        $this->assertEquals($functionParam, $parsedParam);
    }

}
