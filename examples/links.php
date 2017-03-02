<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Связи между сущностями
    // Метод для получения связей между сущностями аккаунта

    print_r($amo->links->apiList([
        'from' => 'leads',
        'from_id' => 1125199,
        'to' => 'contacts',
        'to_id' => 3673249
    ]));

    // Установка связи между сущностями
    // Метод позволяет устанавливать связи между сущностями

    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 1125199;
    $link['to'] = 'contacts';
    $link['to_id'] = 3673249;
    var_dump($link->apiLink());

    // Разрыв связи между сущностями
    // Метод позволяет удалять связи между сущностями

    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 1125199;
    $link['to'] = 'contacts';
    $link['to_id'] = 3673249;
    var_dump($link->apiUnlink());

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
