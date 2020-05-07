<?php

namespace Spark\Foundation\Console;

class InstallCommand extends SparkCommand
{ 
    /**
     * The console command signature
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all spark modules, files and tables';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('spark install success!');
    }
}
