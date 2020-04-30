<?php

namespace Spark\Foundation\Providers;

use Illuminate\Support\AggregateServiceProvider;

class ServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        ConsoleServiceProvider::class
    ];
}
