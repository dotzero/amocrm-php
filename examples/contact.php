<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список контактов
    // Метод для получения списка контактов с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 контактов.

    print_r($amo->contact->apiList([
        'query' => 'Илья',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->contact->apiList([
        'query' => 'Илья',
        'limit_rows' => 1,
    ], '-100 DAYS'));

    // Добавление и обновление контактов
    // Метод позволяет добавлять контакты по одному или пакетно,
    // а также обновлять данные по уже существующим контактам.

    $contact = $amo->contact;
    $contact->debug(true); // Режим отладки
    $contact['name'] = 'ФИО';
    $contact['request_id'] = '123456789';
    $contact['date_create'] = '-2 DAYS';
    $contact['responsible_user_id'] = 697344;
    $contact['company_name'] = 'ООО Тестовая компания';
    $contact['tags'] = ['тест1', 'тест2'];
    $contact->addCustomField(448, [
        ['+79261112233', 'WORK'],
    ]);

    $id = $contact->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $contact1 = clone $contact;
    $contact1['name'] = 'ФИО 1';
    $contact2 = clone $contact;
    $contact2['name'] = 'ФИО 2';

    $ids = $amo->contact->apiAdd([$contact1, $contact2]);
    print_r($ids);

    // Обновление контактов
    $contact = $amo->contact;
    $contact->debug(true); // Режим отладки
    $contact['name'] = 'ФИО 3';

    $contact->apiUpdate((int)$id, 'now');

    // Связи между сделками и контактами
    print_r($amo->contact->apiLinks([
        'limit_rows' => 3
    ]));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
