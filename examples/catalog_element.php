<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список элементов каталога
    // Метод для получения элементов каталога аккаунта.

    print_r($amo->catalog_element->apiList([
        'catalog_id' => 4179,
        'term' => 'Product'
    ]));

    // Добавление элементов каталога
    // Метод позволяет добавлять элементы каталога по одному или пакетно

    $element = $amo->catalog_element;
    $element->debug(true); // Режим отладки
    $element['catalog_id'] = 4179;
    $element['name'] = 'Product';
    $element->addCustomField(212937, 1);

    $id = $element->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $element1 = clone $element;
    $element1['name'] = 'Product 1';
    $element2 = clone $element;
    $element2['name'] = 'Product 2';

    $ids = $amo->catalog_element->apiAdd([$element1, $element2]);
    print_r($ids);

    // Обновление элементов каталога
    $element = $amo->catalog_element;
    $element->debug(true); // Режим отладки
    $element['name'] = 'New product';
    $element['catalog_id'] = 4179; // без catalog_id amocrm не обновит
    $element->addCustomField(212937, 5000);

    $element->apiUpdate((int)$id);

    // Удаление каталогов
    $amo->catalog_element->apiDelete((int)$id);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
