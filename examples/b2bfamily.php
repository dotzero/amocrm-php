<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    $b2b = new \AmoCRM\Helpers\B2BFamily(
        $amo,
        getenv('B2B_APPKEY'),
        getenv('B2B_SECRET'),
        getenv('B2B_EMAIL'),
        getenv('B2B_PASSWORD')
    );

    // Подписать клиента AmoCrm на Webhooks
    $b2b->subscribe();

    // Отправить письмо и прикрепить его к сделке
    $b2b->mail(6003277, [
        'to' => 'loki.dz@gmail.com',
        'type' => 'message',
        'subject' => 'Тест b2bfamily',
        'text' => 'Тигр, тигр, тигр',
        'events' => [
            'trigger' => 'message_open',
            'not_open_timeout' => 1
        ]
    ]);

} catch (\AmoCRM\Helpers\B2BFamilyException $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
