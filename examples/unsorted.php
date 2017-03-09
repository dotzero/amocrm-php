<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Список неразобранных заявок
    // Метод для получения списка неразобранных заявок с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 заявок.

    print_r($amo->unsorted->apiList([
        'page_size' => 10,
        'PAGEN_1' => 1,
    ]));

    // Агрегирование неразобранных заявок
    // Метод для получения агрегированной информации о неразобранных заявках.

    print_r($amo->unsorted->apiGetAllSummary());

    // Добавление неразобранных заявок
    // Метод позволяет добавлять неразобранные заявки по одной или пакетно

    $unsorted = $amo->unsorted;
    $unsorted['source'] = 'some@mail.from';
    $unsorted['source_uid'] = '06ea27be-b26e-4ce4-8c20-cb4261a65752';
    $unsorted['source_data'] = [
        'from' => [
            'email' => 'info@site.hh.ru',
            'name' => 'HeadHunter',
        ],
        'date' => 1446544372,
        'subject' => 'Did you like me?',
        'thread_id' => 11774,
        'message_id' => 23698,
    ];

    // Добавление сделки которая будет создана после одобрения заявки.
    $lead = $amo->lead;
    $lead['name'] = 'New lead from this email';
    $unsorted->addDataLead($lead);

    // Добавление контакта или компании которая будет создана после одобрения заявки.
    $contact = $amo->contact;
    $contact['name'] = 'Create contact from this data';

    // Примечания, которые появятся в контакте после принятия неразобранного
    $note = $amo->note;
    $note['text'] = 'foobar';
    $contact['notes'] = $note;

    $unsorted->addDataContact($contact);

    //  Добавление неразобранных заявок с типом MAIL
    $unsortedId = $unsorted->apiAddMail();
    print_r($unsortedId);

    // Добавление неразобранных заявок
    // Метод позволяет добавлять неразобранные заявки по одной или пакетно

    $unsorted = $amo->unsorted;
    $unsorted['source'] = 'www.my-awesome-site.com';
    $unsorted['source_uid'] = null;
    $unsorted['source_data'] = [
        'data' => [
            'name_1' => [
                'type' => 'text',
                'id' => 'name',
                'element_type' => '1',
                'name' => 'Name',
                'value' => 'Some name',
            ]
        ],
        'form_id' => 318,
        'form_type' => 1,
        'origin' => [
            'ip' => '10.4.4.43',
            'datetime' => 'Tue Nov 03 2015 13:02:24 GMT+0300 (Russia Standard Time)',
            'referer' => '',
        ],
        'date' => 1446544971,
        'from' => 'some-url.com',
        'form_name' => 'My name for form',
    ];

    // Добавление сделки которая будет создана после одобрения заявки.
    $lead = $amo->lead;
    $lead['name'] = 'New lead from this form';
    $unsorted->addDataLead($lead);

    // Добавление неразобранных заявок с типом FORMS
    $unsortedId = $unsorted->apiAddForms();
    print_r($unsortedId);

    // Принятие неразобранных заявок
    // Метод для принятия неразобранных заявок.

    $account = $amo->account->apiCurrent();
    $user_id = $account['users'][0]['id'];

    print_r($amo->unsorted->apiAccept($unsortedId, $user_id));

    // Отклонение неразобранных заявок
    // Метод для отклонения неразобранных заявок.

    $account = $amo->account->apiCurrent();
    $user_id = $account['users'][0]['id'];

    print_r($amo->unsorted->apiDecline($unsortedId, $user_id));

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
