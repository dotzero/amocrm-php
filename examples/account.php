<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Получение информации по аккаунту в котором произведена авторизация:
    // название, оплаченный период, пользователи аккаунта и их права,
    // справочники дополнительных полей контактов и сделок, справочник статусов сделок,
    // справочник типов событий, справочник типов задач и другие параметры аккаунта.

    // Полный формат
    print_r($amo->account->apiCurrent());

    // Краткий формат (если полный не влезает в буфер консоли)
    print_r($amo->account->apiCurrent(true));

    // Возвращает сведения о пользователе по его логину.
    print_r($amo->account->getUserByLogin('mail@example.com'));

    // Если не указывать логин, вернутся сведения о владельце API ключа.
    print_r($amo->account->getUserByLogin());

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
