<?php

use Mockery as m;
use JsLocalization\Console\RefreshCommand;

class RefreshCommandTest extends TestCase
{

    public function setUp ()
    {
        parent::setUp();
    }

    public function tearDown ()
    {
        m::close();

        parent::tearDown();
    }

    public function testNoLocalesConfigException ()
    {
        // Mock Config
        Illuminate\Support\Facades\Config::swap($config = m::mock('ConfigMock'));

        $config->shouldReceive('get')->with('js-localization::config.locales')
          ->andReturn(null);


        $this->setExpectedException('Exception');

        $this->runCommand();
    }

    protected function runCommand ()
    {
        $cmd = new RefreshCommand();

        $cmd->run(
            new Symfony\Component\Console\Input\ArrayInput(array('package' => 'foo')),
            new Symfony\Component\Console\Output\NullOutput
        );
    }

}
