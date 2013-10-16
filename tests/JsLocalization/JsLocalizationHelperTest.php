<?php

class JsLocalizationHelperTest extends TestCase
{

    private $additionalMessageKeys;
    private $additionalMessageKeysFlat;

    public function setUp ()
    {
        parent::setUp();

        $this->additionalMessageKeys = array(
            'additional' => array(
                'message1',
                'message2'
            )
        );

        $this->additionalMessageKeysFlat = array(
            'additional.message1', 'additional.message2'
        );
    }

    public function testResolveMessageKey ()
    {
        $helper = App::make('JsLocalizationHelper');

        $testKeys = array(
            'test1',
            'prefix1' => array(
                'prefix2' => array(
                    'test2', 'test3'
                ),
                'test4'
            )
        );

        $testKeysFlat = array(
            'test1',
            'prefix1.prefix2.test2',
            'prefix1.prefix2.test3',
            'prefix1.test4'
        );

        $_this = $this;
        $callbackRun = 0;

        foreach ($testKeys as $index=>$key) {
            $helper->resolveMessageKey($key, $index, function($qualifiedKey)
                use ($_this, &$callbackRun, $testKeysFlat)
                {
                    $_this->assertEquals($testKeysFlat[$callbackRun], $qualifiedKey);
                    $callbackRun++;
                });
        }
    }
    
    public function testAddingRetrieving ()
    {
        $helper = App::make('JsLocalizationHelper');

        $helper->addMessagesToExport($this->additionalMessageKeys);

        $additionalMessageKeys = $helper->getAdditionalMessages();
        $this->assertEquals($this->additionalMessageKeysFlat, $additionalMessageKeys);


        $this->addTestMessage('another', 'Another test text.');

        $helper->addMessagesToExport(array('another'));

        $additionalMessageKeys = $helper->getAdditionalMessages();
        $this->assertEquals(
            array_merge($this->additionalMessageKeysFlat, array('another')),
            $additionalMessageKeys
        );
    }

}