<?php

namespace AmoCRM\Models;

/**
 * Class CustomField
 *
 * Класс модель для работы с Дополнительными полями
 *
 * @package AmoCRM\Models
 * @author mihasichechek <mihasichechek@gmail.com>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CustomField extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'name',
        'request_id',
        'disabled',
        'type',
        'element_type',
        'origin',
        'enums',
    ];

    const TYPE_TEXT = 1;
    const TYPE_NUMERIC = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_SELECT = 4;
    const TYPE_MULTISELECT = 5;
    const TYPE_DATE = 6;
    const TYPE_URL = 7;
    const TYPE_MULTITEXT = 8;
    const TYPE_TEXTAREA = 9;
    const TYPE_RADIOBUTTON = 10;

    /**
     * @const int Типа сущности Контакт
     */
    const ENTITY_CONTACT = 1;

    /**
     * @const int Типа сущности Сделка
     */
    const ENTITY_LEAD = 2;

    /**
     * @const int Типа сущности Компания
     */
    const ENTITY_COMPANY = 3;

    /**
     * Сеттер для флага, указывающего на то,
     * должно ли поле быть редактируемым в веб-интерфейсе
     *
     * @param string $value Значение флага
     * @return $this
     */
    public function setDisabled($value)
    {
        $this->values['disabled'] = (bool)$value ? 1 : 0;

        return $this;
    }

    /**
     * Добавление дополнительных полей
     *
     * Метод позволяет добавлять дополнительные поля по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/fields_set.php
     * @param $fields array Массив дополнительных полей для пакетного добавления
     * @return int|array Уникальный идентификатор поля или массив при пакетном добавлении
     */
    public function apiAdd($fields = [])
    {
        if (empty($fields)) {
            $fields = [$this];
        }

        $parameters = [
            'fields' => [
                'add' => [],
            ],
        ];

        foreach ($fields AS $field) {
            $parameters['fields']['add'][] = $field->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/fields/set', $parameters);

        if (isset($response['fields']['add'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['fields']['add']);
        } else {
            return [];
        }

        return count($fields) == 1 ? array_shift($result) : $result;
    }

    /**
     * Удаление дополнительных полей
     *
     * Метод позволяет удалять дополнительные поля
     *
     * @link https://developers.amocrm.ru/rest_api/fields_set.php
     * @param $id int Уникальный идентификатор дополнительного поля
     * @param $origin string Уникальный идентификатор сервиса заданный при создании параметром origin
     * @return bool Флаг успешности выполнения запроса
     */
    public function apiDelete($id, $origin)
    {
        $parameters = [
            'fields' => [
                'delete' => [
                    [
                        'id' => (int)$id,
                        'origin' => $origin
                    ]
                ]
            ]
        ];

        $response = $this->postRequest('/private/api/v2/json/fields/set', $parameters);

        return isset($response['fields']['delete']) ? true : false;
    }
}