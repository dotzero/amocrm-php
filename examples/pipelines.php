<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список воронок и этапов продаж
    // Метод для получения списка воронок и этапов продаж.

    print_r($amo->pipelines->apiList());

    // С доп фильтрацией по ID
    print_r($amo->pipelines->apiList(125373));

    $pipeline = $amo->pipelines;
    $pipeline->debug(true); // Режим отладки
    $pipeline['name'] = 'Воронка 1';
    $pipeline['sort'] = 1;
    $pipeline['is_main'] = 'on'; // or 1, or true
    $pipeline->addStatusField([
        'name' => 'Pending',
        'sort' => 10,
        'color' => '#fffeb2',
    ]);
    // Добавление этапа с ID
    $pipeline->addStatusField([
        'name' => 'Done',
        'sort' => 20,
        'color' => '#f3beff',
    ], 12345);

    $id = $pipeline->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $pipeline1 = clone $pipeline;
    $pipeline1['name'] = 'Воронка 1';
    $pipeline2 = clone $pipeline;
    $pipeline2['name'] = 'Воронка 2';

    $ids = $amo->pipelines->apiAdd([$pipeline1, $pipeline2]);
    print_r($ids);

    // Обновление воронок и этапов продаж
    $pipeline = $amo->pipelines;
    $pipeline->debug(true); // Режим отладки
    $pipeline['name'] = 'Воронка 3';
    // Обновление этапа с ID
    $pipeline->addStatusField([
        'name' => 'Done',
        'sort' => 20,
        'color' => '#f3beff',
    ], 12345);

    $pipeline->apiUpdate((int)$id);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
