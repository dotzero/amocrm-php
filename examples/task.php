<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список задач
    // Метод для получения списка задач с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 задач.

    print_r($amo->task->apiList([
        'type' => 'lead',
        'limit_rows' => 5,
        'query' => 'mail',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->task->apiList([
        'type' => 'lead',
        'limit_rows' => 5,
        'query' => 'mail',
    ], '-100 DAYS'));

    // Добавление и обновление задач
    // Метод позволяет добавлять задачи по одной или пакетно,
    // а также обновлять данные по уже существующим задачам

    $task = $amo->task;
    $task->debug(true); // Режим отладки
    $task['element_id'] = 11029224;
    $task['element_type'] = 1;
    $task['date_create'] = '-2 DAYS';
    $task['task_type'] = 1;
    $task['text'] = "Текст\nзадачи";
    $task['responsible_user_id'] = 798027;
    $task['complete_till'] = '+1 DAY';

    $id = $task->apiAdd();
    print_r($id);

    // Или массовое добавление
    $task1 = clone $task;
    $task1['text'] = 'Текст задачи 1';
    $task2 = clone $task;
    $task2['name'] = 'Текст задачи 2';

    $ids = $amo->task->apiAdd([$task1, $task2]);
    print_r($ids);

    // Обновление задач
    $task = $amo->task;
    $task->debug(true); // Режим отладки

    $task->apiUpdate((int)$id, 'Текст задачи', 'now');

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
