<?php

class JsLocalizationControllerTest extends TestCase
{
    public function testCreateJsMessages ()
    {
        $this->mockLang($locale = 'en');

        $response = $this->action('GET', 'JsLocalizationController@createJsMessages');
        $content = $response->getContent();

        // Test for Lang.setLocale()

        $this->assertRegExp('/Lang\.setLocale\("'.$locale.'"\);/', $content);

        // Test for Lang.addMessages()

        $this->assertLangAddMessages($content, $this->testMessages);
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