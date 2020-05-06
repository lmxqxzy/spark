<?php

namespace Spark\Foundation\Console;

use Illuminate\Console\Command;
use Spark\Foundation\Spark;

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
    protected $description = 'Lists all spark commands';

    
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
   _____    _____   ___      ____      _
  / ____/  / __  \ / | )    / ___ \   / /____
 / /___   / /__/ // /| |   / /__/ /  / /_____/
 \____ \ / _____// /_| |  / __   /  / / \
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
        // $this->line(static::$logo);
        $this->line(static::$logo_oblique);

        $this->line(Spark::getLongVersion());

        $this->comment('');
        $this->comment('Available commands:');

        $this->listAdminCommands();
    }

    /**
     * Lists commands.
     *
     * @return void
     */
    protected function listAdminCommands()
    {
        $commands = collect(Artisan::all())->mapWithKeys(function ($command, $key) {
            if (Str::startsWith($key, 'spark:')) {
                return [$key => $command];
            }

            return [];
        })->toArray();

        $width = $this->getColumnWidth($commands);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->line(sprintf(" %-{$width}s %s", $command->getName(), $command->getDescription()));
        }
    }
}
