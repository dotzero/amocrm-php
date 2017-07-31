<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список Webhooks
    // Метод для получения списка Webhooks.

    print_r($amo->webhooks->apiList());

    // Добавление Webhooks
    // Метод для добавления Webhooks на одно событие.

    print_r($amo->webhooks->apiSubscribe('http://example.com/', 'status_lead'));

    // Добавление Webhooks
    // Метод для добавления Webhooks на несколько событий.

    print_r($amo->webhooks->apiSubscribe('http://example.com/', [
        'add_contact',
        'update_contact',
        'delete_contact'
    ]));

    // Удаления Webhooks
    // Метод для удаления Webhooks на одно событие.

    print_r($amo->webhooks->apiUnsubscribe('http://example.com/', 'status_lead'));

    // Удаления Webhooks
    // Метод для удаления Webhooks на несколько событий.

    print_r($amo->webhooks->apiUnsubscribe('http://example.com/', [
        'add_contact',
        'update_contact',
        'delete_contact'
    ]));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
