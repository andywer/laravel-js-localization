<?php

use Mockery as m;
use JsLocalization\CachingService;

class CachingServiceTest extends TestCase
{

    private $cachingService;

    public function setUp ()
    {
        parent::setUp();

        $this->cachingService = new CachingService;
        
        Cache::forget(CachingService::CACHE_KEY);
        Cache::forget(CachingService::CACHE_TIMESTAMP_KEY);
    }

    public function tearDown ()
    {
        m::close();

        parent::tearDown();
    }


    public function testGetMessagesJson ()
    {
        $this->assertMessagesJsonEquals($this->testMessages);

        // Add another string, but without refreshing the cache:

        $originalTestMessages = $this->testMessages;
        $this->addTestMessage('test.new_message', "This is a new message.");

        $this->assertMessagesJsonEquals($originalTestMessages);

        // Now refresh the cache:

        $this->cachingService->refreshMessageCache();

        $this->assertMessagesJsonEquals($this->testMessages);
    }

    public function testGetLastRefreshTimestamp ()
    {
        $timestamp = $this->cachingService->getLastRefreshTimestamp();
        $this->assertEquals(0, $timestamp);

        $this->cachingService->refreshMessageCache();
        $refreshTime = time();

        $timestamp = $this->cachingService->getLastRefreshTimestamp();
        $this->assertEquals($refreshTime, $timestamp);
    }

    public function testRefreshMessageCacheEvent ()
    {
        Event::shouldReceive('fire')->once()->with('JsLocalization.registerMessages');

        $this->cachingService->refreshMessageCache();
    }

    private function assertMessagesJsonEquals (array $expectedMessages)
    {
        $messagesJson = $this->cachingService->getMessagesJson();
        $this->assertJson($messagesJson);

        $messages = json_decode($messagesJson, true);
        $this->assertEquals($expectedMessages, $messages);
    }

}