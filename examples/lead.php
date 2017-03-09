<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список сделок
    // Метод для получения списка сделок с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 сделок.

    print_r($amo->lead->apiList([
        'query' => 'Илья',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->lead->apiList([
        'query' => 'Илья',
        'limit_rows' => 1,
    ], '-100 DAYS'));

    // Добавление и обновление сделок
    // Метод позволяет добавлять сделки по одному или пакетно,
    // а также обновлять данные по уже существующим сделкам.

    $lead = $amo->lead;
    $lead->debug(true); // Режим отладки
    $lead['name'] = 'Тестовая сделка';
    $lead['date_create'] = '-2 DAYS';
    $lead['status_id'] = 10525225;
    $lead['price'] = 3000;
    $lead['responsible_user_id'] = 697344;
    $lead['tags'] = ['тест1', 'тест2'];
    $lead['visitor_uid'] = '12345678-52d2-44c2-9e16-ba0052d9f6d6';
    $lead->addCustomField(167379, [
        [388733, 'Стартап'],
    ]);
    $lead->addCustomField(167381, [
        [388743, '6 месяцев'],
    ]);
    $lead->addCustomField(167411, 'Спецпроект');

    $id = $lead->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $lead1 = clone $lead;
    $lead1['name'] = 'Тестовая сделка 1';
    $lead2 = clone $lead;
    $lead2['name'] = 'Тестовая сделка 2';

    $ids = $amo->lead->apiAdd([$lead1, $lead2]);
    print_r($ids);

    // Обновление сделок
    $lead = $amo->lead;
    $lead->debug(true); // Режим отладки
    $lead['name'] = 'Тестовая сделка 3';

    $lead->apiUpdate((int)$id, 'now');

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
