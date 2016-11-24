<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список виджетов
    // Метод для получения списка доступных для установки виджетов.

    print_r($amo->widgets->apiList());

    // С доп фильтрацией
    print_r($amo->widgets->apiList([
        'widget_id' => 62121
    ]));

    // Включение виджетов
    print_r($amo->widgets->apiInstall([
        'widget_id' => 62121
    ]));

    // Выключение виджетов
    print_r($amo->widgets->apiUninstall([
        'widget_id' => 62121
    ]));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
