<?php

class LocalizationScriptTest extends TestCase
{
    public function testScriptRetrieval ()
    {
        $this->client->request('GET', '/js-localization/localization.js');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isOk());

        $content = $response->getContent();
        $this->assertRegExp('/^\(function\(.*\);/', $content);
    }
}