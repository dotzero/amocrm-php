<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Если не указывать логин, вернутся сведения о владельце API ключа.
    $account = $amo->account->getUserByLogin();

    $unsortedId = 'a1587c45b09329a19bdf592c8a45ee37b6f095700be10f943d46820bcaee';

    // Метод для отклонения неразобранных заявок.
    print_r($amo->unsorted->apiDecline($unsortedId, $account['id']));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
