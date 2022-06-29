```
use Kaafochino\LaravelRabbitMQ\PubSub;

Artisan::command('mq:subscribe {binding_keys*}', function ($binding_keys) {
    $v = new PubSub;
    $v->subscribe(function ($msg) {
        echo $msg->body;
        $msg->ack();
    }, "", $binding_keys);
});

Artisan::command('mq:publish {routing_keys*}', function ($routing_keys) {
    $v = new PubSub;
    $v->publish(
        ['message' => 'This is a test message from Competing Consumers Pattern!'],
        $routing_keys
    );
});
```


Subscribers:
```
$ php artisan mq:subscribe foo bar
$ php artisan mq:subscribe bar baz
```

Publisher
```
$ php artisan mq:publish bar // both subscribers receive message
$ php artisan mq:publish foo // fiest subscriber receives message
$ php artisan mq:publish baz // second subscriber receives message
```