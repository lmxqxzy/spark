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
    protected $description = 'list all spark commands';

    
    /**
     * @var string
     */
    public static $logo = <<<LOGO
     ____    __ __               _ ___     _    _
    / ____\ |  ___ \    /\      |  ___ \  | |  / /
   / /      | |   \ \  /  \     | |   \ \ | | / /
   \ \____  | |___/ / / /\ \    | |___/ / | |/ /
    \____ \ | |___ / / /__\ \   | |___ /  | |\ \
   __    \ \| |     / /____\ \  | |  \ \  | | \ \
   \ \___/ /| |    / /      \ \ | |   \ \ | |  \ \
    \____ / |_|   /_/        \_\| |    \_\|_|   \_\
LOGO;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line(static::$logo);
        $this->info('spark success');
    }
}
