<?php

namespace Kaafochino\LaravelRabbitMQ;

use Kaafochino\LaravelRabbitMQ\Contracts\RabbitMQ;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Kaafochino\LaravelRabbitMQ\RabbitMQConnection;

class PubSub implements RabbitMQ
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function setQueue(): string
    {
        return '';
    }

    public function setExchange(): string
    {
        return 'exchange';
    }

    public function setExchangeType(): string
    {
        return "direct";
    }

    public function publish(array $data, array $routing_keys = array("")): void
    {
        $this->connect();

        $message = new AMQPMessage(
            $body = json_encode($data),
            $properties = [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );
        foreach ($routing_keys as $routing_key) {
            $this->channel->basic_publish($message, $this->setExchange(), $routing_key);
        }

        $this->channel->basic_publish($message, $this->setExchange(), $this->setQueue());
    }

    public function subscribe(callable $callback, $consumer_tag="", array $binding_keys=array("")): void
    {
        $this->connect();

        list($queue_name, , ) = $this->channel->queue_declare($this->setQueue(), false, true, true, false);

        foreach ($binding_keys as $binding_key) {
            $this->channel->queue_bind($queue_name, $this->setExchange(), $binding_key);
        }

        $this->channel->basic_consume($queue_name, $consumer_tag, false, false, false, false, $callback);

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
        $this->disconnect();
    }
    
    public function connect(): void
    {
        $service = new RabbitMQConnection;
        $this->connection = $service->connection();
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->setExchange(), $this->setExchangeType(), false, true, false);
    }

    public function disconnect()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
