<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Добавление дополнительных полей
    // Метод позволяет добавлять дополнительные поля по одному или пакетно

    $field = $amo->custom_field;
    $field->debug(true); // Режим отладки
    $field['name'] = 'Tracking ID';
    $field['type'] = \AmoCRM\Models\CustomField::TYPE_TEXT;
    $field['element_type'] = \AmoCRM\Models\CustomField::ENTITY_CONTACT;
    $field['origin'] = '528d0285c1f9180911159a9dc6f759b3_zendesk_widget';

    $id = $field->apiAdd();
    print_r($id);

    // Добавления поля типа список

    $field = $amo->custom_field;
    $field->debug(true); // Режим отладки
    $field['name'] = 'Multi';
    $field['type'] = \AmoCRM\Models\CustomField::TYPE_MULTISELECT;
    $field['element_type'] = \AmoCRM\Models\CustomField::ENTITY_CONTACT;
    $field['origin'] = '528d0285c1f9180911159a9dc6f759b3_zendesk_widget';
    $field['enums'] = [
        'Value 1',
        'Value 2',
        'Value 3',
    ];

    // Удаление дополнительных полей
    $field = $amo->custom_field;
    //$field->debug(true); // Режим отладки
    $result = $field->apiDelete($id, '528d0285c1f9180911159a9dc6f759b3_zendesk_widget');
    var_dump($result);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
