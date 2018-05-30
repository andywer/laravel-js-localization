<?php
namespace JsLocalization\Console;

use Config;
use Illuminate\Console\Command;
use File;
use JsLocalization\Exceptions\ConfigException;
use JsLocalization\Facades\ConfigCachingService;
use JsLocalization\Facades\MessageCachingService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'js-localization:export';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'js-localization:export {--no-cache : Ignores cache completely}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Refresh message cache and export to static files";

    /**
     *  Options defined for Laravel < 5.1
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['no-cache', 'd', InputOption::VALUE_NONE, 'Ignores cache completely'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws ConfigException
     */
    public function handle()
    {
        $noCache = (bool)$this->option('no-cache');
        if ($noCache === true) $this->line('Exporting messages and config...');
        else $this->line('Refreshing and exporting the message and config cache...');

        $locales = Config::get('js-localization.locales');

        if(!is_array($locales)) {
          throw new ConfigException('Please set the "locales" config! See https://github.com/andywer/laravel-js-localization#configuration');
        }

        if ($noCache === false) MessageCachingService::refreshCache();
        $messagesFilePath = $this->createPath('messages.js');
        $this->generateMessagesFile($messagesFilePath, $noCache);

        if ($noCache === false) ConfigCachingService::refreshCache();
        $configFilePath = $this->createPath('config.js');
        $this->generateConfigFile($configFilePath, $noCache);
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

    /**
     * Create full file path.
     * This method will also generate the directories if they don't exist already.
     *
     * @var string $filename
     *
     * @return string $path
     */
    public function createPath($filename)
    {
        $dir = Config::get('js-localization.storage_path');
        if (!is_dir($dir)) {
            mkdir($dir, '0777', true);
        }

        return $dir . $filename;
    }

    /**
     * Generate the messages file.
     *
     * @param string $path
     * @param bool $noCache
     */
    public function generateMessagesFile($path, $noCache = false)
    {
        $splitFiles = Config::get('js-localization.split_export_files');
        $messages = MessageCachingService::getMessagesJson($noCache);

        if ($splitFiles) {
            $this->generateMessageFiles(File::dirname($path), $messages);
        }

        $contents = 'Lang.addMessages(' . $messages . ');';

        File::put($path, $contents);

        $this->line("Generated $path");
    }

    /**
     * Generate the lang-{locale}.js files
     *
     * @param string $path Directory to where we will store the files
     * @param string $messages JSON string of messages
     */
    protected function generateMessageFiles(string $path, string $messages)
    {
        $locales = Config::get('js-localization.locales');
        $messages = json_decode($messages, true);

        foreach ($locales as $locale) {
            $fileName = $path . "/lang-{$locale}.js";

            if (key_exists($locale, $messages)) {
                $content = 'Lang.addMessages(' . json_encode([$locale => $messages[$locale]], JSON_PRETTY_PRINT) . ');';

                File::put($fileName, $content);
                $this->line("Generated $fileName");
            }
        }
    }

    /**
     * Generate the config file.
     *
     * @param string $path
     * @param bool $noCache
     */
    public function generateConfigFile($path, $noCache = false)
    {
        $config = ConfigCachingService::getConfigJson($noCache);
        if ($config === '{}') {
            $this->line('No config specified. Config not written to file.');
            return;
        }

        $contents = 'Config.addConfig(' . $config . ');';

        File::put($path, $contents);

        $this->line("Generated $path");
    }
}
