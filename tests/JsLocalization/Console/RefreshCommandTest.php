<?php

use Mockery as m;
use JsLocalization\Console\RefreshCommand;

class RefreshCommandTest extends TestCase
{
    public function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function testNoLocalesConfigException()
    {
        // Mock Config
        Illuminate\Support\Facades\Config::swap($config = m::mock('ConfigMock'));

        $config->shouldReceive('get')->with('js-localization.locales')
          ->andReturn(null);

        $this->expectException(Exception::class);

        $this->runCommand();
    }

    protected function runCommand()
    {
        $cmd = new RefreshCommand();

        $cmd->setLaravel(resolve(\Illuminate\Contracts\Foundation\Application::class));

        $cmd->run(
            new Symfony\Component\Console\Input\ArrayInput([]),
            new Symfony\Component\Console\Output\NullOutput
        );
    }

}
