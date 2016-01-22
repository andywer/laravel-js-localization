<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 22.01.16
 * Time: 10:33
 */

namespace JsLocalization\Console;

use File;
use Illuminate\Console\Command;
use JsLocalization\Facades\TranslationData;
use Symfony\Component\Console\Input\InputOption;

class ExportCommand extends Command {
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'js-localization:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Export translations as Javascript file";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $toStdout = $this->input->getOption('stdout');
        
        if ($toStdout) {
            $outFile = 'php://stdout';
        } else {
            $outFile = $this->input->getOption('outfile');
            if (!$outFile) {
                $this->output->writeln('Usage: ' . $this->getSynopsis());
                return;
            }
        }
        
        $jsonData = $this->input->getOption('config') ? TranslationData::getConfigJson() : TranslationData::getMessagesJson();
        File::put($outFile, $jsonData);
        
        if (!$toStdout) {
            $this->line('Wrote Javascript file: '.$outFile);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['outfile', 'o', InputOption::VALUE_OPTIONAL, 'The output file to write to. Alternatively use --stdout.'],
            ['stdout', null, InputOption::VALUE_NONE, 'Write file to stdout.'],
            ['config', null, InputOption::VALUE_NONE, 'Write exported config values instead of translations.'],
        ];
    }
    
}