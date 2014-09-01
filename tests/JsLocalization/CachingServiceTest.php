<?php

use Mockery as m;
use JsLocalization\Facades\CachingService;

class CachingServiceTest extends TestCase
{

    private $cachingService;

    public function setUp ()
    {
        parent::setUp();

        Cache::forget(JsLocalization\CachingService::CACHE_KEY);
        Cache::forget(JsLocalization\CachingService::CACHE_TIMESTAMP_KEY);
    }

    public function tearDown ()
    {
        m::close();

        parent::tearDown();
    }


    public function testGetMessagesJson ()
    {
        $this->assertMessagesJsonEquals($this->testMessagesFlat);

        // Add another string, but without refreshing the cache:

        $originalTestMessages = $this->testMessagesFlat;
        $this->addTestMessage('en','test.new_message', "This is a new message.");

        $this->assertMessagesJsonEquals($originalTestMessages);

        // Now refresh the cache:

        CachingService::refreshMessageCache();

        $this->assertMessagesJsonEquals($this->testMessagesFlat);
    }

    public function testGetLastRefreshTimestamp ()
    {
        $timestamp = CachingService::getLastRefreshTimestamp();
        $this->assertEquals(0, $timestamp);

        CachingService::refreshMessageCache();
        $refreshTime = time();

        $timestamp = CachingService::getLastRefreshTimestamp();
        $this->assertEquals($refreshTime, $timestamp);
    }

    public function testRefreshMessageCacheEvent ()
    {
        Event::shouldReceive('fire')->once()->with('JsLocalization.registerMessages');

        CachingService::refreshMessageCache();
    }

    private function assertMessagesJsonEquals (array $expectedMessages)
    {
        $messagesJson = CachingService::getMessagesJson();
        $this->assertJson($messagesJson);

        $messages = json_decode($messagesJson, true);
        $this->assertEquals($expectedMessages, $messages);
    }

}
