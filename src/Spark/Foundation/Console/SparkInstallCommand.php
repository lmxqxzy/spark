<?php

namespace Spark\Foundation\Console;

use Illuminate\Console\Command;

class SparkInstallCommand extends Command
{ 
    /**
     * The console command signature
     *
     * @var string
     */
    protected $signature = 'spark:install';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'spark:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all spark modules, including files and tables';

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
