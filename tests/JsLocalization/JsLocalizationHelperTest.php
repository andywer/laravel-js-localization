<?php

use JsLocalization\Facades\JsLocalizationHelper;

class JsLocalizationHelperTest extends TestCase
{

    private $tmpFilePath;

    protected $additionalMessageKeys = array(
            'additional' => array(
                'message1',
                'message2'
            )
        );

    protected $additionalMessageKeysFlat = array(
            'additional.message1', 'additional.message2'
        );


    protected $testMessages = array(
            'test1' => "Text for test1",
            'prefix1' => array(
                'prefix2' => array(
                    'test2' => "Text for test2",
                    'test3' => "Text for test3"
                ),
                'test4' => "Text for test4"
            )
        );

    protected $testKeys = array(
            'test1',
            'prefix1' => array(
                'prefix2' => array(
                    'test2', 'test3'
                ),
                'test4'
            )
        );

    protected $testKeysFlat = array(
            'test1',
            'prefix1.prefix2.test2',
            'prefix1.prefix2.test3',
            'prefix1.test4'
        );

    public function setUp ()
    {
        parent::setUp();

        $this->tmpFilePath = tempnam('/tmp', '');
        unlink($this->tmpFilePath);

        $this->tmpFilePath .= '.php';
        touch($this->tmpFilePath);
    }

    public function tearDown ()
    {
        unlink($this->tmpFilePath);

        parent::tearDown();
    }

    public function testResolveMessageKeyArray ()
    {
        $this->assertEquals($this->testKeysFlat, JsLocalizationHelper::resolveMessageKeyArray($this->testKeys));
    }

    public function testResolveMessageArrayToMessageKeys ()
    {
        $this->assertEquals($this->testKeysFlat, JsLocalizationHelper::resolveMessageArrayToMessageKeys($this->testMessages));
    }
    
    public function testAddingRetrieving ()
    {
        JsLocalizationHelper::addMessagesToExport($this->additionalMessageKeys);

        $this->assertEquals(
            $this->additionalMessageKeysFlat,
            JsLocalizationHelper::getAdditionalMessages()
        );


        $this->addTestMessage('another', 'Another test text.');

        JsLocalizationHelper::addMessagesToExport(array('another'));

        $this->assertEquals(
            array_merge($this->additionalMessageKeysFlat, array('another')),
            JsLocalizationHelper::getAdditionalMessages()
        );
    }
    
    public function testEventBasedAdding ()
    {
        $additionalMessageKeys = $this->additionalMessageKeys;


        Event::listen('JsLocalization.registerMessages', function()
        use($additionalMessageKeys)
        {
            JsLocalizationHelper::addMessagesToExport($additionalMessageKeys);
        });

        $this->assertEquals(array(), JsLocalizationHelper::getAdditionalMessages());

        JsLocalizationHelper::triggerRegisterMessages();

        $this->assertEquals(
            $this->additionalMessageKeysFlat,
            JsLocalizationHelper::getAdditionalMessages()
        );


        $this->addTestMessage('another', 'Another test text.');

        Event::listen('JsLocalization.registerMessages', function()
        {
            JsLocalizationHelper::addMessagesToExport(array('another'));
        });

        JsLocalizationHelper::triggerRegisterMessages();

        $this->assertEquals(
            array_merge($this->additionalMessageKeysFlat, array('another')),
            JsLocalizationHelper::getAdditionalMessages()
        );
    }

    public function testAddMessageFileToExport ()
    {
        $fileContents = '<?php return ' . var_export($this->testMessages, true) . ';';
        file_put_contents($this->tmpFilePath, $fileContents);

        $prefix  = 'xyz::';
        $prefix .= preg_replace('/\.php$/i', '', basename($this->tmpFilePath)) . '.';
        JsLocalizationHelper::addMessageFileToExport($this->tmpFilePath, 'xyz::');

        $testKeysFlat = $this->testKeysFlat;
        array_walk($testKeysFlat, function(&$key) use($prefix)
            {
                $key = $prefix . $key;
            });

        $this->assertEquals($testKeysFlat, JsLocalizationHelper::getAdditionalMessages());
    }

}