<?php

namespace Spark\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spark\Foundation\Spark;

/**
 * @author lmxqxzy <lmxqxzy@outlook.com>
 */
class SparkCommand extends Command
{
    /**
     * The console command signature
     *
     * @var string
     */
    protected $prefix = 'spark';

    /**
     * The console command signature
     *
     * @var string
     */
    protected $signature = null;

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
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (is_null($this->signature)) {
            $this->signature = $this->prefix;
        } else {
            $this->signature = $this->prefix . ':' . $this->signature;
        }
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
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
            if (Str::startsWith($key, $this->prefix . ':')) {
                return [$key => $command];
            }

            return [];
        })->toArray();

        $width = $this->getColumnWidth($commands);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->line(sprintf("  <info>%-{$width}s</info> %s", $command->getName(), $command->getDescription()));
        }
    }

    /**
     * @param (Command|string)[] $commands
     *
     * @return int
     */
    private function getColumnWidth(array $commands)
    {
        $widths = [];

        foreach ($commands as $command) {
            $widths[] = static::strlen($command->getName());
            foreach ($command->getAliases() as $alias) {
                $widths[] = static::strlen($alias);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }

    /**
     * Returns the length of a string, using mb_strwidth if it is available.
     *
     * @param string $string The string to check its length
     *
     * @return int The length of the string
     */
    public static function strlen($string)
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }
}
