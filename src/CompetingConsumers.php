<?php

namespace Kaafochino\LaravelRabbitMQ;

use Kaafochino\LaravelRabbitMQ\Contracts\RabbitMQ;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Kaafochino\LaravelRabbitMQ\RabbitMQConnection;

class CompetingConsumers implements RabbitMQ
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function setQueue(): string
    {
        return 'queue';
    }

    final public function setExchange(): string
    {
        return '';
    }

    public function publish(array $data): void
    {
        $this->connect();

        $message = new AMQPMessage(
            $body = json_encode($data),
            $properties = [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $this->channel->basic_publish($message, $this->setExchange(), $this->setQueue());
        $this->disconnect();
    }

    public function subscribe(callable $callback, string $consumer_tag=''): void
    {
        $this->connect();

        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume($this->setQueue(), $consumer_tag, false, false, false, false, $callback);

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
        $this->channel->queue_declare($this->setQueue(), false, true, false, false);
    }

    public function disconnect()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
