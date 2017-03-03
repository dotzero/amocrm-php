<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список каталогов
    // Метод для получения списка каталогов аккаунта.

    print_r($amo->catalog->apiList());

    // С фильтрацией по ID

    print_r($amo->catalog->apiList(4143));

    // Добавление каталогов
    // Метод позволяет добавлять каталоги по одному или пакетно.

    $catalog = $amo->catalog;
    $catalog->debug(true); // Режим отладки
    $catalog['name'] = 'Products';

    $id = $catalog->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $catalog1 = clone $catalog;
    $catalog1['name'] = 'Products 1';
    $catalog2 = clone $catalog;
    $catalog2['name'] = 'Products 2';

    $ids = $amo->catalog->apiAdd([$catalog1, $catalog2]);
    print_r($ids);

    // Обновление каталогов
    $catalog = $amo->catalog;
    $catalog->debug(true); // Режим отладки
    $catalog['name'] = 'Tariffs';

    $catalog->apiUpdate((int)$id);

    // Удаление каталогов
    $amo->catalog->apiDelete((int)$id);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
