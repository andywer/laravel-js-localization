<?php

class LocalizationScriptTest extends TestCase
{
    public function testScriptRetrieval ()
    {
        $response = $this->call('GET', '/js-localization/localization.js');

        $this->assertTrue($response->isOk());

        $content = $response->getContent();
        $this->assertRegExp('/^\(function\(.*\);/', $content);
    }
}