<?php

namespace Kaafochino\LaravelRabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnection
{
    private array $config;

    public function __construct()
    {
        $this->config = config('rabbitmq.connection');
    }

    public function connection(): object
    {
        return new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['password']);
    }
}
