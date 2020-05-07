<?php

namespace Spark\Foundation\Console;

class UninstallCommand extends SparkCommand
{ 
    /**
     * The console command signature
     *
     * @var string
     */
    protected $signature = 'uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all spark installed modules, files and tables';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('spark uninstall success!');
    }
}
