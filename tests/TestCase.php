sh<?php

use Mockery as m;

class TestCase extends Orchestra\Testbench\TestCase
{

    protected $testMessagesConfig = array(
        'test_string',
        'test'              // this includes all messages with key 'test.*'
    );

    protected $testMessages = array(
        'en' => array(
            'test_string' => 'This is: test_string',
            'test' => array(
                'nested' => array(
                    'leaf' => 'I am deeply nested!'
                ),
                'string' => 'This is: test.string'
            ),

            'test.string' => 'This is: test.string'
        )
    );

    protected $testMessagesFlat = array(
        'en' => array(
            'test_string' => 'This is: test_string',
            'test.nested.leaf' => 'I am deeply nested!',
            'test.string' => 'This is: test.string'
        )
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

        foreach ($this->testMessages[$locale] as $key=>$message) {
            $lang->shouldReceive('get')
                ->with($key)->andReturn($message);
            $lang->shouldReceive('get')
                ->with($key, m::any(), $locale)->andReturn($message);
        }
    }

    protected function addTestMessage ($locale, $messageKey, $message)
    {
        $this->testMessagesConfig[] = $messageKey;

        $this->testMessages[$locale][$messageKey] = $message;
        $this->testMessagesFlat[$locale][$messageKey] = $message;

        $keys = explode('.', $messageKey);
        if (count($keys) == 2) {
            if (!isset($this->testMessages[$locale][$keys[0]])) {
                $this->testMessages[$locale][$keys[0]] = array();
            }
            $this->testMessages[$locale][$keys[0]][$keys[1]] = $message;
        }

        $this->updateMessagesConfig($this->testMessagesConfig);
        $this->mockLang();
    }

}
