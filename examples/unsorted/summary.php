<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Агрегирование неразобранных заявок
    // Метод для получения агрегированной информации о неразобранных заявках.

    print_r($amo->unsorted->apiGetAllSummary());

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
