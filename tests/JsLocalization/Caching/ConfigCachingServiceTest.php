<?php

use Mockery as m;
use JsLocalization\Facades\ConfigCachingService;

class ConfigCachingServiceTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        Cache::forget(JsLocalization\Caching\ConfigCachingService::CACHE_KEY);
        Cache::forget(JsLocalization\Caching\ConfigCachingService::CACHE_TIMESTAMP_KEY);
    }

    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }
    
    public function testIsDisabled()
    {
        Config::set('js-localization.disable_config_cache', false);
        $this->assertFalse(ConfigCachingService::isDisabled());

        Config::set('js-localization.disable_config_cache', true);
        $this->assertTrue(ConfigCachingService::isDisabled());
    }
    
    public function testGetConfigJson()
    {
        $expectedJson = json_encode($this->testConfigExportFlat);
        $this->assertEquals($expectedJson, ConfigCachingService::getConfigJson());
        
        // Test that getConfigJson() returns the old value after Config::set()
        Config::set('js-localization.test-config', 'new value');
        $this->assertEquals($expectedJson, ConfigCachingService::getConfigJson());
    }

    public function testGetConfigJsonWithCacheDisabled()
    {
        Config::set('js-localization.disable_config_cache', true);
        
        $expectedJson = json_encode($this->testConfigExportFlat);
        $this->assertEquals($expectedJson, ConfigCachingService::getConfigJson());

        // Test that getConfigJson() returns the new value after Config::set()
        $this->testConfigExportFlat['js-localization.test-config'] = 'new value';
        $expectedJson = json_encode($this->testConfigExportFlat);
        
        Config::set('js-localization.test-config', 'new value');
        $this->assertEquals($expectedJson, ConfigCachingService::getConfigJson());
    }
    
}