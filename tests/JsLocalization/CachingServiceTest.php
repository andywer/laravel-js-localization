<?php

use Mockery as m;
use JsLocalization\CachingService;

class CachingServiceTest extends TestCase
{

    private $cachingService;
    private $config;

    private $testMessagesConfig = array(
            'test_string',
            'test' => array('string')
        );

    private $testMessages = array(
            'test_string' => 'This is: test_string',
            'test.string' => 'This is: test.string'
        );

    public function setUp ()
    {
        parent::setUp();

        $this->cachingService = new CachingService;
        
        Cache::forget(CachingService::CACHE_KEY);

        $this->updateConfig($this->testMessagesConfig);
        $this->mockLang();
    }

    private function updateConfig (array $config)
    {
        Config::set('js-localization::config.messages', $config);
    }

    private function mockLang ()
    {
        Illuminate\Support\Facades\Lang::swap($lang = m::mock('LangMock'));

        foreach ($this->testMessages as $key=>$message) {
            $lang->shouldReceive('get')
                ->with($key)->andReturn($message);
        }
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

    private function addTestMessage ($messageKey, $message)
    {
        $this->testMessagesConfig[] = $messageKey;

        $this->testMessages[$messageKey] = $message;

        $this->updateConfig($this->testMessagesConfig);
        $this->mockLang();
    }

    private function assertMessagesJsonEquals (array $expectedMessages)
    {
        $messagesJson = $this->cachingService->getMessagesJson();
        $this->assertJson($messagesJson);

        $messages = json_decode($messagesJson, true);
        $this->assertEquals($expectedMessages, $messages);
    }

}