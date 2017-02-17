<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список периодов
    // Метод для получения списка периодов

    print_r($amo->customers_periods->apiList());

    // Добавление, удаление и обновление периодов покупателей

    // Можно использовать как модель
    $period1 = $amo->customers_periods;
    $period1['period'] = 60;
    $period1['sort'] = 3;
    $period1['color'] = '#ffdc7f';

    // Или как массив
    $period2 = [
        'period' => 60,
        'sort' => 7,
        'color' => '#ccc8f9',
    ];

    // Метод позволяет изменять данные по периодам.
    // При изменении необходимо передать полный список периодов, включая уже существующие.
    // При удалении периода нужно исключить его из запроса.

    print_r($amo->customers_periods->apiSet([$period1, $period2]));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
