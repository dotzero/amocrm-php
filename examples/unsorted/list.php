<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список неразобранных заявок
    // Метод для получения списка неразобранных заявок с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 заявок.

    print_r($amo->unsorted->apiList([
        'page_size' => 10,
        'PAGEN_1' => 1,
    ]));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
