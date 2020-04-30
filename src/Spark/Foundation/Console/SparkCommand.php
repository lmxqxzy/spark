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
    protected $signature = 'spark';

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
  ____    _____               _____     _    _
 / ____\ |  ___ \    /\      |  ___ \  | |  / /
/ /      | |   \ \  /  \     | |   \ \ | | / /
\ \____  | |___/ / / /\ \    | |___/ / | |/ /
 \____ \ | |___ / / /__\ \   | |___ /  | |\ \
__    \ \| |     / /____\ \  | |  \ \  | | \ \
\ \___/ /| |    / /      \ \ | |   \ \ | |  \ \
 \_____/ |_|   /_/        \_\| |    \_\|_|   \_\
LOGO;

    /**
     * @var string
     */
    public static $logo_oblique = <<<LOGO
   _____    _____   ___      _____     _
  / ____/  / __  \ / | )    / __  /   / /____
 / /___   / /__/ // /| |   / /_/ /   / /_____/
 \____ \ / _____// /_| |  / ___ /   / / \
 ____/ // /     / /——| | / /  \ \  / / \ \
/_____//_/     /_/   |_|/_/    \_\/_/   \_\
LOGO;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line(static::$logo);
        $this->line(static::$logo_oblique);
        $this->info('spark success');
    }
}
