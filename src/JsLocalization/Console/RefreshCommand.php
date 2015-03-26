<?php
namespace JsLocalization\Console;

use Config;
use Illuminate\Console\Command;
use JsLocalization\Exceptions\ConfigException;
use JsLocalization\Facades\CachingService;
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
    public function fire()
    {
        $this->line('Refreshing the message cache...');

        $locales = Config::get('js-localization.locales');

        if(!is_array($locales)) {
          throw new ConfigException('Please set the "locales" config! See https://github.com/andywer/laravel-js-localization#configuration');
        }

        CachingService::refreshMessageCache();
    }

}
