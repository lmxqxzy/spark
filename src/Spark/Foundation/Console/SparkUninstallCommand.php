<?php

namespace Spark\Foundation\Console;

use Illuminate\Console\Command;

class SparkUninstallCommand extends Command
{ 
    /**
     * The console command signature
     *
     * @var string
     */
    protected $signature = 'spark:uninstall';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'spark:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all spark modules, including files and tables';

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
