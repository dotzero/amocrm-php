<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список WebHooks
    // Метод для получения списка WebHooks.

    print_r($amo->webhooks->apiList());

    // Добавление WebHooks
    // Метод для добавления WebHooks на одно событие.

    print_r($amo->webhooks->apiSubscribe('http://example.com/', 'status_lead'));

    // Добавление WebHooks
    // Метод для добавления WebHooks на несколько событий.

    print_r($amo->webhooks->apiSubscribe('http://example.com/', [
        'add_contact',
        'update_contact',
        'delete_contact'
    ]));

    // Удаления WebHooks
    // Метод для удаления WebHooks на одно событие.

    print_r($amo->webhooks->apiUnsubscribe('http://example.com/', 'status_lead'));

    // Удаления WebHooks
    // Метод для удаления WebHooks на несколько событий.

    print_r($amo->webhooks->apiUnsubscribe('http://example.com/', [
        'add_contact',
        'update_contact',
        'delete_contact'
    ]));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
