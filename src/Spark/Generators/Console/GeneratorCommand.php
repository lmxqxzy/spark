<?php

namespace Spark\Generators\Console;

use Spark\Foundation\Console\SparkCommand;

class GeneratorCommand extends SparkCommand
{ 
     /**
     * The console command signature
     *
     * @var string
     */
    protected $signature = 'generator';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->choice('Which you want gererate?', ['module', 'controller']);
    }
}
