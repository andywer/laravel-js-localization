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


    protected $testMessagesFlat = array(
            'en' => array(
                'test1' => "Text for test1",
                'prefix1' => array(
                    'prefix2' => array(
                        'test2' => "Text for test2",
                        'test3' => "Text for test3"
                    ),
                    'test4' => "Text for test4"
                )
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

    protected function setUpTestMessagesFile ($filePath)
    {
        $fileContents = '<?php return ' . var_export($this->testMessagesFlat['en'], true) . ';';
        file_put_contents($filePath, $fileContents);

        $prefix = preg_replace('/\.php$/i', '', basename($filePath));

        return $prefix;
    }

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
        $this->assertEquals($this->testKeysFlat, JsLocalizationHelper::resolveMessageArrayToMessageKeys($this->testMessagesFlat['en']));
    }

    public function testAddingRetrieving ()
    {
        JsLocalizationHelper::addMessagesToExport($this->additionalMessageKeys);

        $this->assertEquals(
            $this->additionalMessageKeysFlat,
            JsLocalizationHelper::getAdditionalMessages()
        );


        $this->addTestMessage('en', 'another', 'Another test text.');

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


        $this->addTestMessage('en', 'another', 'Another test text.');

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
        $prefix = 'xyz::' . $this->setUpTestMessagesFile($this->tmpFilePath);
        JsLocalizationHelper::addMessageFileToExport($this->tmpFilePath, 'xyz::');

        // since we just tested the method using a prefix without the trailing '.'
        $prefix .= '.';

        $testKeysFlat = $this->testKeysFlat;
        array_walk($testKeysFlat, function(&$key) use($prefix)
            {
                $key = $prefix . $key;
            });

        $this->assertEquals($testKeysFlat, JsLocalizationHelper::getAdditionalMessages());
    }

    public function testAddMessageFileToExportExceptionHandling ()
    {
        $filePath = "/tmp/x/y/z/does-not-exist";

        $this->setExpectedException(
            'Illuminate\Filesystem\FileNotFoundException',
            "File not found: $filePath"
        );

        JsLocalizationHelper::addMessageFileToExport($filePath, 'xyz::');
    }

}
