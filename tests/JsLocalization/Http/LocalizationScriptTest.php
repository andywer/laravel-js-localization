<?php

class LocalizationScriptTest extends TestCase
{
    public function testScriptRetrieval()
    {
        $response = $this->call('GET', '/js-localization/localization.js');

        $this->assertTrue($response->isOk());
        $content = $response->getContent();
        
        // Test for JS content
        
        $this->assertRegExp('/^!?\(?function\(.*\);/', $content);
    }
    
    public function testScriptAndTranslationCombinedRetrieval()
    {
        $response = $this->call('GET', '/js-localization/all.js');

        $this->assertTrue($response->isOk());
        $content = $response->getContent();

        // Test for JS content

        $this->assertRegExp('/^!?\(?function\(.*\);/', $content);

        // Test for Lang.setLocale()

        $locale = \Illuminate\Support\Facades\Lang::locale();
        $this->assertRegExp('/Lang\.setLocale\("'.$locale.'"\);/', $content);
        
        // Test for Lang.addMessages()

        $addMessagesRegex = '/Lang\.addMessages\( (\{.*?\}) \);/x';
        $this->assertRegExp($addMessagesRegex, $content);
        
        // Test for Config.addConfig()

        $addConfigRegex = '/Config\.addConfig\( (\{.*?\}) \);/x';
        $this->assertRegExp($addConfigRegex, $content);
    }
}