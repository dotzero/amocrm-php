<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список транзакций
    // Метод для получения транзакций аккаунта.

    print_r($amo->transaction->apiList([
        'limit_rows' => 10,
    ]));

    // Добавление элементов каталога
    // Метод позволяет добавлять элементы каталога по одному или пакетно

    $transaction = $amo->transaction;
    $transaction->debug(true); // Режим отладки
    $transaction['customer_id'] = 29729;
    $transaction['date'] = 'now';
    $transaction['price'] = 3500;
    $transaction['next_price'] = 6000;
    $transaction['next_date'] = '+1 day';

    $id = $transaction->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $transaction1 = clone $transaction;
    $transaction1['price'] = '200';
    $transaction2 = clone $transaction;
    $transaction2['price'] = '300';

    $ids = $amo->transaction->apiAdd([$transaction1, $transaction2]);
    print_r($ids);

    // Удаление каталогов
    var_dump($amo->transaction->apiDelete((int)$id));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
