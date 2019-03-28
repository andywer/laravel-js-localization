<?php

use Mockery as m;
use JsLocalization\Facades\MessageCachingService;

class MessageCachingServiceTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        Cache::forget(JsLocalization\Caching\MessageCachingService::CACHE_KEY);
        Cache::forget(JsLocalization\Caching\MessageCachingService::CACHE_TIMESTAMP_KEY);
    }

    public function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }


    public function testGetMessagesJson()
    {
        $this->assertMessagesJsonEquals($this->testMessagesFlat);

        // Add another string, but without refreshing the cache:

        $originalTestMessages = $this->testMessagesFlat;
        $this->addTestMessage('en','test.new_message', "This is a new message.");

        $this->assertMessagesJsonEquals($originalTestMessages);

        // Now refresh the cache:

        MessageCachingService::refreshCache();

        $this->assertMessagesJsonEquals($this->testMessagesFlat);
    }

    public function testGetLastRefreshTimestamp()
    {
        $timestamp = MessageCachingService::getLastRefreshTimestamp()->getTimestamp();
        $this->assertEquals(0, $timestamp);

        MessageCachingService::refreshCache();
        $refreshTime = time();

        $timestamp = MessageCachingService::getLastRefreshTimestamp()->getTimestamp();
        $this->assertEquals($refreshTime, $timestamp);
    }

    public function testRefreshMessageCacheEvent()
    {
        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );

        Event::shouldReceive('dispatch')->once()->with('JsLocalization.registerMessages');

        MessageCachingService::refreshCache();
    }

    private function assertMessagesJsonEquals(array $expectedMessages)
    {
        $messagesJson = MessageCachingService::getMessagesJson();
        $this->assertJson($messagesJson);

        $messages = json_decode($messagesJson, true);
        $this->assertEquals($expectedMessages, $messages);
    }

}
