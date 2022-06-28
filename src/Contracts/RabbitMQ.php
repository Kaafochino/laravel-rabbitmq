<?php

namespace Kaafochino\LaravelRabbitMQ\Contracts;

interface RabbitMQ
{
    public function connect();

    public function disconnect();
    
    public function publish(array $data): void;

    public function subscribe(callable $callback, string $consumer_tag=''): void;
}
