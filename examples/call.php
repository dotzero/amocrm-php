<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Добавление звонков
    // Метод позволяет добавлять звонки по одному или пакетно

    $call = $amo->call;
    $call->debug(true); // Режим отладки
    $call['account_id'] = 1111111;
    $call['uuid'] = '947669bc-ec58-450e-83e8-828a3e6fc354';
    $call['caller'] = '88001000000';
    $call['to'] = '88002000000';
    $call['date'] = 'now';
    $call['type'] = \AmoCRM\Models\Call::TYPE_INBOUND;
    $call['billsec'] = 60;
    $call['link'] = 'http://example.com/audio.mp3';

    $call->apiAdd('my_service_name', '601eb8fab9707d8009dba552f2d411a3');

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
