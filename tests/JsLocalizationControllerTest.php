<?php

use Mockery as m;

class JsLocalizationControllerTest extends TestCase
{
    public function testCreateJsMessages ()
    {
        // Prepare & Request

        $this->mockLang($locale = 'en');

        Artisan::call('js-localization:refresh');

        $response = $this->action('GET', 'JsLocalizationController@createJsMessages');
        $content = $response->getContent();

        $this->assertTrue($response->isOk());

        // Test for Lang.setLocale()

        $this->assertRegExp('/Lang\.setLocale\("'.$locale.'"\);/', $content);

        // Test for Lang.addMessages()

        $this->assertLangAddMessages($content, $this->testMessagesFlat);
    }

    public function testBackwardsCompatibility ()
    {
        // Prepare & Request

        $this->mockLang($locale = 'en');
        $this->mockCachingService($this->testMessagesFlat['en']);

        $response = $this->action('GET', 'JsLocalizationController@createJsMessages');
        $content = $response->getContent();

        $this->assertTrue($response->isOk());

        // Test for Lang.addMessages()

        $this->assertLangAddMessages($content, $this->testMessagesFlat);
    }

    private function mockCachingService (array $messages)
    {
        $service = m::mock('CachingServiceMock');
        JsLocalization\Facades\CachingService::swap($service);

        $service->shouldReceive('getMessagesJson')
            ->andReturn(json_encode($messages));

        $service->shouldReceive('getLastRefreshTimestamp')
            ->andReturn(time());
    }

    private function assertLangAddMessages ($jsContent, array $expectedMessages)
    {
        $addMessagesRegex = '/Lang\.addMessages\( (\{.*?\}) \);/x';
        $this->assertRegExp($addMessagesRegex, $jsContent);

        preg_match($addMessagesRegex, $jsContent, $matches);
        $messagesJson = $matches[1];

        $this->assertJson($messagesJson);
        $messages = json_decode($messagesJson, true);

        $this->assertEquals($expectedMessages, $messages);
    }

}
