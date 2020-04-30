<?php

namespace Spark\Foundation\Console;

use Illuminate\Console\Command;

class SparkCommand extends Command
{ 

    /**
     * The console command signature
     *
     * @var string
     */
    // protected $signature = 'spark';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'spark';

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
        $this->info('The compiled services & packages files have been removed.');
    }
}
