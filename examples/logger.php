<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    $context = [
        'key' => 'value',
        'ключ' => 'значение'
    ];

    $logger = new \AmoCRM\Logger\StdOut();

    $logger->emergency('emergency', $context);
    $logger->alert('alert', $context);
    $logger->critical('critical', $context);
    $logger->error('error', $context);
    $logger->warning('warning', $context);
    $logger->notice('notice', $context);
    $logger->info('info', $context);
    $logger->debug('debug', $context);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
