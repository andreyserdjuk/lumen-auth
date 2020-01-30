<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Mailer;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

class SymfonyLumenCommandBridge extends Command
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var SymfonyCommand
     */
    protected $command;

    public function __construct(SymfonyCommand $command)
    {
        $this->command = $command;
        $this->name = $command->getName();

        parent::__construct();
        $this->signature = $command->getName();
        $this->description = $command->getDescription();
    }

    public function handle()
    {
        $allArgs = $this->input->getArguments();
        $commandDefinition = $this->command->getDefinition();
        $allowedOptions = $commandDefinition->getOptions();

        unset($allArgs['command']);
        $allOpts = $this->input->getOptions();
        $optsInput = [];
        foreach ($allOpts as $index => $opt) {
            if ($opt && isset($allowedOptions[$index])) {
                $optsInput['--'.$index] = $opt;
            }
        }

        $output = (function(){ return $this->output; })->call($this->output);
        $input = new ArrayInput($optsInput, $commandDefinition);
        $this->command->execute($input, $output);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $symfonyOptions = $this->command->getDefinition()->getOptions();
        $getMode = (function(){ return $this->mode; });

        $options = [];
        /**  @var InputOption $option **/
        foreach ($symfonyOptions as $name => $option) {
            $options[] = [
                $option->getName(),
                $option->getShortcut(),
                $getMode->call($option),
                $option->getDescription(),
            ];
        }
        return $options;
    }
}
