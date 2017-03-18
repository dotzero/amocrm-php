<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    $log = new \Monolog\Logger('AmoCRM');
    $handler = new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG);
    $log->pushHandler($handler);

    $amo->setLogger($log);
    print_r($amo->account->getUserByLogin());
} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
