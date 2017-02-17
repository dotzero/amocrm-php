<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список покупателей
    // Метод для получения покупателей аккаунта

    print_r($amo->customer->apiList([
        'limit_rows' => 100,
    ]));

    // Добавление покупателей
    // Метод позволяет добавлять покупателей по одному или пакетно

    $customer = $amo->customer;
    $customer->debug(true); // Режим отладки
    $customer['name'] = 'ФИО';
    $customer['request_id'] = '123456789';
    $customer['main_user_id'] = 151516;
    $customer['next_price'] = 5000;
    $customer['periodicity'] = 7;
    $customer['tags'] = ['тест1', 'тест2'];
    $customer['next_date'] = strtotime('+2 DAYS');

    $id = $customer->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $customer1 = clone $customer;
    $customer1['name'] = 'ФИО 1';
    $customer2 = clone $customer;
    $customer2['name'] = 'ФИО 2';

    $ids = $amo->customer->apiAdd([$customer1, $customer2]);
    print_r($ids);

    // Обновление покупателей
    $customer = $amo->customer;
    $customer->debug(true); // Режим отладки
    $customer['name'] = 'ФИО 3';

    $customer->apiUpdate((int)$id);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
