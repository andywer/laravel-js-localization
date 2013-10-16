<?php

use Mockery as m;

class TestCase extends Orchestra\Testbench\TestCase {

    protected $testMessagesConfig = array(
            'test_string',
            'test' => array('string')
        );

    protected $testMessages = array(
            'test_string' => 'This is: test_string',
            'test.string' => 'This is: test.string'
        );

    public function setUp ()
    {
        parent::setUp();

        $this->updateMessagesConfig($this->testMessagesConfig);
        $this->mockLang();
    }

    public function tearDown ()
    {
        m::close();

        parent::tearDown();
    }

    protected function getPackageProviders()
    {
        return array('JsLocalization\JsLocalizationServiceProvider');
    }

    protected function updateMessagesConfig (array $config)
    {
        Config::set('js-localization::config.messages', $config);
    }

    protected function mockLang ($locale = "en")
    {
        Illuminate\Support\Facades\Lang::swap($lang = m::mock('LangMock'));

        $lang->shouldReceive('setLocale');
        $lang->shouldReceive('locale')->andReturn($locale);

        foreach ($this->testMessages as $key=>$message) {
            $lang->shouldReceive('get')
                ->with($key)->andReturn($message);
        }
    }

    protected function addTestMessage ($messageKey, $message)
    {
        $this->testMessagesConfig[] = $messageKey;

        $this->testMessages[$messageKey] = $message;

        $this->updateMessagesConfig($this->testMessagesConfig);
        $this->mockLang();
    }

}
