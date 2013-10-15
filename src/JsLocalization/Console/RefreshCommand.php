<?php
namespace JsLocalization\Console;

use App;
use Illuminate\Console\Command;
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
     */
    public function fire()
    {
        $this->line('Refreshing the message cache...');

        $cachingService = App::make('JsLocalizationCachingService');
        $cachingService->refreshMessageCache();
    }

}