<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Включение логера и печать сообщений в StdOut
    $amo->account->debug(true)->getUserByLogin();

    // Использование логгера отдельно
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

    // Использование Monolog
    // composer require monolog/monolog
    $log = new \Monolog\Logger('amocrm');
    $log->pushHandler(
        new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG)
    );
    $amo->setLogger($log);
    $amo->account->getUserByLogin();

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
