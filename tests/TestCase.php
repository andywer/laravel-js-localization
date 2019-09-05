sh<?php

use Mockery as m;

class TestCase extends Orchestra\Testbench\TestCase
{

    protected $testMessagesConfig = [
        'test_string',
        'test'              // this includes all messages with key 'test.*'
    ];
    
    protected $testMessages = [
        'en' => [
            'test_string' => 'This is: test_string',
            'test' => [
                'nested' => [
                    'leaf' => 'I am deeply nested!'
                ],
                'string' => 'This is: test.string'
            ],

            'test.string' => 'This is: test.string'
        ]
    ];

    protected $testMessagesFlat = [
        'en' => [
            'test_string' => 'This is: test_string',
            'test.nested.leaf' => 'I am deeply nested!',
            'test.string' => 'This is: test.string'
        ]
    ];

    protected $testConfigExportFlat = [
        'js-localization.test-config' => 'some test property value'
    ];


    public function setUp(): void
    {
        parent::setUp();

        $this->updateMessagesConfig($this->testMessagesConfig);
        $this->updateConfigExportConfig($this->testConfigExportFlat);
        $this->mockLang();
    }

    public function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return ['JsLocalization\JsLocalizationServiceProvider'];
    }

    protected function updateMessagesConfig(array $config)
    {
        Config::set('js-localization.messages', $config);
    }
    
    protected function updateConfigExportConfig(array $configData)
    {
        Config::set('js-localization.config', array_keys($configData));
        
        foreach ($configData as $propertyName => $propertyValue) {
            Config::set($propertyName, $propertyValue);
        }
    }

    protected function mockLang($locale = "en")
    {
        Illuminate\Support\Facades\Lang::swap($lang = m::mock('LangMock'));

        $lang->shouldReceive('setLocale');
        $lang->shouldReceive('locale')->andReturn($locale);

        foreach ($this->testMessages[$locale] as $key=>$message) {
            $lang->shouldReceive('getFromJson')
                ->with($key)->andReturn($message);
            $lang->shouldReceive('getFromJson')
                ->with($key, m::any(), $locale)->andReturn($message);
        }
    }

    protected function addTestMessage($locale, $messageKey, $message)
    {
        $this->testMessagesConfig[] = $messageKey;

        $this->testMessages[$locale][$messageKey] = $message;
        $this->testMessagesFlat[$locale][$messageKey] = $message;

        $keys = explode('.', $messageKey);
        if (count($keys) == 2) {
            if (!isset($this->testMessages[$locale][$keys[0]])) {
                $this->testMessages[$locale][$keys[0]] = [];
            }
            $this->testMessages[$locale][$keys[0]][$keys[1]] = $message;
        }

        $this->updateMessagesConfig($this->testMessagesConfig);
        $this->mockLang();
    }

}
