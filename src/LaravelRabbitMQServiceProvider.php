<?php

namespace Kaafochino\LaravelRabbitMQ;

use Illuminate\Support\ServiceProvider;

class LaravelRabbitMQServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rabbitmq.php', 'rabbitmq');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/rabbitmq.php' => config_path('rabbitmq.php'),
            ], 'config');
        }
    }
}
