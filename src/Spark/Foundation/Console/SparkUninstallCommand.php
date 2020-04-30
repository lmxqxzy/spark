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
    protected $description = 'list all spark commands';

    
    /**
     * @var string
     */
    public static $logo = <<<LOGO
   __ __    _ ____       _        _ ____     _    _
  / __ __\ |  ____ \    / \      |  ____ \  | |  / /
 / /       | |    \ \  /   \     | |    \ \ | | / /
 \ \__ __  | |____/ / / / \ \    | |____/ / | |/ /
  \__ __ \ | |____ / / /___\ \   | |____ /  | |\ \
 __     \ \| |      / /__ __\ \  | |   \ \  | | \ \
 \ \____/ /| |     / /       \ \ | |    \ \ | |  \ \
  \__ __ / |_|    /_/         \_\| |     \_\|_|   \_\
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
