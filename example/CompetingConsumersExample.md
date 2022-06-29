```
use Kaafochino\LaravelRabbitMQ\CompetingConsumers;

Artisan::command('mq:subscribe', function () {
    $v = new CompetingConsumers;
    $v->subscribe(function ($msg) {
        echo $msg->body;
        $msg->ack();
    });
});

Artisan::command('mq:publish', function () {
    $v = new CompetingConsumers;
    $v->publish(['message' => 'This is a test message from Competing Consumers Pattern!']);
});
```
Subscriber:
```
php artisan mq:subscribe
```

Publisher
```
php artisan mq:publish
```