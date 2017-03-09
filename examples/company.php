<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список компаний
    // Метод для получения списка компаний с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 компаний.

    print_r($amo->company->apiList([
        'query' => 'mail',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->company->apiList([
        'query' => 'mail',
        'limit_rows' => 10,
    ], '-100 DAYS'));

    // Добавление и обновление компаний
    // Метод позволяет добавлять компании по одной или пакетно,
    // а также обновлять данные по уже существующим компаниям

    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company['name'] = 'ООО Тестовая компания';
    $company['request_id'] = '123456789';
    $company['date_create'] = '-2 DAYS';
    $company['responsible_user_id'] = 697344;
    $company['tags'] = ['тест1', 'тест2'];
    $company->addCustomField(448, [
        ['+79261112233', 'WORK'],
        ['+79261112200', 'MOB'],
    ]);

    $id = $company->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $company1 = clone $company;
    $company1['name'] = 'ООО Тестовая компания 1';
    $company2 = clone $company;
    $company2['name'] = 'ООО Тестовая компания 2';

    $ids = $amo->company->apiAdd([$company1, $company2]);
    print_r($ids);

    // Обновление компаний
    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company['name'] = 'ООО Тестовая компания 3';

    $company->apiUpdate((int)$id, 'now');

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
