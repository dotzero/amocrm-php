<?php

namespace AmoCRM\Models;

use AmoCRM\Models\Traits\SetDateCreate;
use AmoCRM\Models\Traits\SetLastModified;

/**
 * Class Note
 *
 * Класс модель для работы с Примечаниями
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Note extends AbstractModel
{
    use SetDateCreate, SetLastModified;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'element_id',
        'element_type',
        'note_type',
        'date_create',
        'last_modified',
        'request_id',
        'text',
        'responsible_user_id',
        'created_user_id',
    ];

    /**
     * @link https://developers.amocrm.ru/rest_api/notes_type.php
     * @type array Типы примечаний
     */
    protected $types = [
        self::DEAL_CREATED => 'Сделка создана',
        self::CONTACT_CREATED => 'Контакт создан',
        self::DEAL_STATUS_CHANGED => 'Статус сделки изменен',
        self::COMMON => 'Обычное примечание',
        self::ATTACHMENT => 'Файл',
        self::CALL => 'Звонок приходящий от iPhone-приложений',
        self::EMAIL_MESSAGE => 'Письмо',
        self::EMAIL_ATTACHMENT => 'Письмо с файлом',
        self::CALL_IN => 'Входящий звонок',
        self::CALL_OUT => 'Исходящий звонок',
        self::COMPANY_CREATED => 'Компания создана',
        self::TASK_RESULT => 'Результат по задаче',
        self::SYSTEM => 'Системное сообщение',
        self::SMS_IN => 'Входящее смс',
        self::SMS_OUT => 'Исходящее смс',
    ];

    const DEAL_CREATED = 1;
    const CONTACT_CREATED = 2;
    const DEAL_STATUS_CHANGED = 3;
    const COMMON = 4;
    const ATTACHMENT = 5;
    const CALL = 6;
    const EMAIL_MESSAGE = 7;
    const EMAIL_ATTACHMENT = 8;
    const CALL_IN = 10;
    const CALL_OUT = 11;
    const COMPANY_CREATED = 12;
    const TASK_RESULT = 13;
    const SYSTEM = 25;
    const SMS_IN = 102;
    const SMS_OUT = 103;

    /**
     * @const int Типа задачи Контакт
     */
    const TYPE_CONTACT = 1;

    /**
     * @const int Типа задачи Сделка
     */
    const TYPE_LEAD = 2;

    /** @const int Типа задачи Компания */
    const TYPE_COMPANY = 3;

    /** @const int Типа задачи Задача */
    const TYPE_TASK = 4;

    /** @const int Типа задачи Покупатель */
    const TYPE_CUSTOMER = 12;

    /**
     * Список примечаний
     *
     * Метод для получения списка примечаний с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 примечаний.
     *
     * @link https://developers.amocrm.ru/rest_api/notes_list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/notes/list', $parameters, $modified);

        return isset($response['notes']) ? $response['notes'] : [];
    }

    /**
     * Добавление примечания
     *
     * Метод позволяет добавлять примечание по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     * @param array $notes Массив примечаний для пакетного добавления
     * @return int|array Уникальный идентификатор примечания или массив при пакетном добавлении
     */
    public function apiAdd($notes = [])
    {
        if (empty($notes)) {
            $notes = [$this];
        }

        $parameters = [
            'notes' => [
                'add' => [],
            ],
        ];

        foreach ($notes AS $note) {
            $parameters['notes']['add'][] = $note->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/notes/set', $parameters);

        if (isset($response['notes']['add'])) {
            $result = array_map(function($item) {
                return $item['id'];
            }, $response['notes']['add']);
        } else {
            return [];
        }

        return count($notes) == 1 ? array_shift($result) : $result;
    }

    /**
     * Обновление примечания
     *
     * Метод позволяет обновлять данные по уже существующим примечаниям
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     * @param int $id Уникальный идентификатор примечания
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id, $modified = 'now')
    {
        $this->checkId($id);

        $parameters = [
            'notes' => [
                'update' => [],
            ],
        ];

        $lead = $this->getValues();
        $lead['id'] = $id;
        $lead['last_modified'] = strtotime($modified);

        $parameters['notes']['update'][] = $lead;

        $response = $this->postRequest('/private/api/v2/json/notes/set', $parameters);

        return empty($response['notes']['update']['errors']);
    }
}
