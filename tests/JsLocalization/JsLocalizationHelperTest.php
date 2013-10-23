<?php

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
        $helper = App::make('JsLocalizationHelper');

        $this->assertEquals($this->testKeysFlat, $helper->resolveMessageKeyArray($this->testKeys));
    }

    public function testResolveMessageArrayToMessageKeys ()
    {
        $helper = App::make('JsLocalizationHelper');

        $this->assertEquals($this->testKeysFlat, $helper->resolveMessageArrayToMessageKeys($this->testMessages));
    }
    
    public function testAddingRetrieving ()
    {
        $helper = App::make('JsLocalizationHelper');

        $helper->addMessagesToExport($this->additionalMessageKeys);

        $this->assertEquals(
            $this->additionalMessageKeysFlat,
            $helper->getAdditionalMessages()
        );


        $this->addTestMessage('another', 'Another test text.');

        $helper->addMessagesToExport(array('another'));

        $this->assertEquals(
            array_merge($this->additionalMessageKeysFlat, array('another')),
            $helper->getAdditionalMessages()
        );
    }
    
    public function testEventBasedAdding ()
    {
        $helper = App::make('JsLocalizationHelper');
        $additionalMessageKeys = $this->additionalMessageKeys;


        Event::listen('JsLocalization.registerMessages', function()
        use($helper, $additionalMessageKeys)
        {
            $helper->addMessagesToExport($additionalMessageKeys);
        });

        $this->assertEquals(array(), $helper->getAdditionalMessages());

        $helper->triggerRegisterMessages();

        $this->assertEquals(
            $this->additionalMessageKeysFlat,
            $helper->getAdditionalMessages()
        );


        $this->addTestMessage('another', 'Another test text.');

        Event::listen('JsLocalization.registerMessages', function() use($helper)
        {
            $helper->addMessagesToExport(array('another'));
        });

        $helper->triggerRegisterMessages();

        $this->assertEquals(
            array_merge($this->additionalMessageKeysFlat, array('another')),
            $helper->getAdditionalMessages()
        );
    }

    public function testAddMessageFileToExport ()
    {
        $helper = App::make('JsLocalizationHelper');

        $fileContents = '<?php return ' . var_export($this->testMessages, true) . ';';
        file_put_contents($this->tmpFilePath, $fileContents);

        $prefix  = 'xyz::';
        $prefix .= preg_replace('/\.php$/i', '', basename($this->tmpFilePath)) . '.';
        $helper->addMessageFileToExport($this->tmpFilePath, 'xyz::');

        $testKeysFlat = $this->testKeysFlat;
        array_walk($testKeysFlat, function(&$key) use($prefix)
            {
                $key = $prefix . $key;
            });

        $this->assertEquals($testKeysFlat, $helper->getAdditionalMessages());
    }

}