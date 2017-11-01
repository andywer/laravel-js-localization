<?php
namespace JsLocalization\Console;

use Config;
use Illuminate\Console\Command;
use JsLocalization\Exceptions\ConfigException;
use JsLocalization\Facades\ConfigCachingService;
use JsLocalization\Facades\MessageCachingService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RefreshCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'js-localization:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Refresh message cache after changing the config file";

    /**
     * Execute the console command.
     *
     * @return void
     * @throws ConfigException
     */
    public function handle()
    {
        $this->line('Refreshing the message cache...');

        $locales = Config::get('js-localization.locales');

        if(!is_array($locales)) {
          throw new ConfigException('Please set the "locales" config! See https://github.com/andywer/laravel-js-localization#configuration');
        }

        MessageCachingService::refreshCache();
        ConfigCachingService::refreshCache();
    }
    
    /**
     * Execute the console command.
     * Compatibility with previous Laravel 5.x versions.
     *
     * @return void
     * @throws ConfigException
     */
    public function fire()
    {
        $this->handle();
    }
}
