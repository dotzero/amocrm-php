<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Для хранения ID полей можно воспользоваться хелпером \AmoCRM\Helpers\Fields
    $amo->fields->StatusId = 10525225;
    $amo->fields->ResponsibleUserId = 697344;

    // Добавление сделок с использованием хелпера
    $lead = $amo->lead;
    $lead['name'] = 'Тестовая сделка';
    $lead['status_id'] = $amo->fields->StatusId;
    $lead['price'] = 3000;
    $lead['responsible_user_id'] = $amo->fields->ResponsibleUserId;
    $lead->apiAdd();

    // Также можно просто использовать хелпер без клиента
    $fields = new \AmoCRM\Helpers\Fields();

    // Как объект
    $fields->StatusId = 10525225;
    $fields->ResponsibleUserId = 697344;

    // Или как массив
    $fields['StatusId'] = 10525225;
    $fields['ResponsibleUserId'] = 697344;

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
