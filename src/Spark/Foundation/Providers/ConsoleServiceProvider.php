<?php

namespace Spark\Foundation\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Spark\Foundation\Console\SparkCommand;
use Spark\Foundation\Console\SparkInstallCommand;
use Spark\Foundation\Console\SparkUninstallCommand;

class ConsoleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'SparkList' => 'command.spark.list',
        'SparkInstall' => 'command.spark.install',
        'SparkUninstall' => 'command.spark.uninstall'
    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        // 'SparkName' => 'command.spark.name'
    ];


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands,
            $this->devCommands
        ));
    }

    /**
     * Register the given commands.
     *
     * @param  array  $commands
     * 
     * @return void
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], []);
        }

        $this->commands(array_values($commands));
    }

    protected function registerSparkListCommand()
    {
        $this->app->singleton('command.spark.list', function () {
            return new SparkCommand;
        });
    }

    protected function registerSparkInstallCommand()
    {
        $this->app->singleton('command.spark.install', function () {
            return new SparkInstallCommand;
        });
    }

    protected function registerSparkUninstallCommand()
    {
        $this->app->singleton('command.spark.uninstall', function () {
            return new SparkUninstallCommand;
        });
    }

    protected function registerSparkNameCommand()
    {
        $this->app->singleton('command.spark.name', function () { });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), array_values($this->devCommands));
    }
}
