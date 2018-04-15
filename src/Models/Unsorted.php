<?php

namespace AmoCRM\Models;

use AmoCRM\Models\Traits\SetDateCreate;

/**
 * Class Unsorted
 *
 * Класс модель для работы со Списком неразобранных заявок
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Unsorted extends AbstractModel
{
    use SetDateCreate;

    /**
     * @var bool Использовать устаревшую схему авторизации
     */
    protected $v1 = true;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'source',
        'source_uid',
        'source_data',
        'date_create',
        'pipeline_id',
        'data',
    ];

    /**
     * @const string Источник заявки - sip
     */
    const TYPE_SIP = 'sip';

    /**
     * @const string Источник заявки - почта
     */
    const TYPE_MAIL = 'mail';

    /**
     * @const string Источник заявки - web-формы
     */
    const TYPE_FORMS = 'forms';

    /**
     * Список неразобранных заявок
     *
     * Метод для получения списка неразобранных заявок с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 заявок.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters = [])
    {
        $response = $this->getRequest('/api/unsorted/list/', $parameters);

        return isset($response['unsorted']) ? $response['unsorted'] : [];
    }

    /**
     * Агрегирование неразобранных заявок
     *
     * Метод для получения агрегированной информации о неразобранных заявках.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/get_all_summary.php
     * @return array Ответ amoCRM API
     */
    public function apiGetAllSummary()
    {
        $response = $this->getRequest('/api/unsorted/get_all_summary/');

        return isset($response['category']) ? $response : [];
    }

    /**
     * Принятие неразобранных заявок
     *
     * Метод для принятия неразобранных заявок.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/accept.php
     * @param string|array $uids
     * @param string|int $user_id
     * @param null|int $status_id
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiAccept($uids, $user_id, $status_id = null)
    {
        $this->checkId($user_id);

        if (!is_array($uids)) {
            $uids = [$uids];
        }

        $parameters = [
            'unsorted' => [
                'accept' => $uids,
                'user_id' => $user_id,
            ],
        ];

        if ($status_id !== null) {
            $parameters['unsorted']['status_id'] = $status_id;
        }

        $response = $this->postRequest('/api/unsorted/accept/', $parameters);

        if (isset($response['unsorted']['accept']['data'])) {
            $result = array_keys($response['unsorted']['accept']['data']);
        } else {
            return [];
        }

        return count($uids) == 1 ? array_shift($result) : $result;
    }

    /**
     * Отклонение неразобранных заявок
     *
     * Метод для отклонения неразобранных заявок.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/decline.php
     * @param string|array $uids
     * @param string|int $user_id
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiDecline($uids, $user_id)
    {
        $this->checkId($user_id);

        if (!is_array($uids)) {
            $uids = [$uids];
        }

        $parameters = [
            'unsorted' => [
                'decline' => $uids,
                'user_id' => $user_id,
            ],
        ];

        $response = $this->postRequest('/api/unsorted/decline/', $parameters);

        if (isset($response['unsorted']['decline']['data'])) {
            $result = array_keys($response['unsorted']['decline']['data']);
        } else {
            return [];
        }

        return count($uids) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление неразобранных заявок
     *
     * Метод позволяет добавлять неразобранные заявки по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param string $type Название источника заявки
     * @param array $values Массив неразобранных заявок для пакетного добавления
     * @return int|array Уникальный идентификатор заявки или массив при пакетном добавлении
     */
    public function apiAdd($type, $values = [])
    {
        if (empty($values)) {
            $values = [$this];
        }

        $parameters = [
            'unsorted' => [
                'category' => $type,
                'add' => [],
            ],
        ];

        foreach ($values AS $value) {
            $parameters['unsorted']['add'][] = $value->getValues();
        }

        $response = $this->postRequest('/api/unsorted/add/', $parameters);

        if (isset($response['unsorted']['add']['data'])) {
            $result = $response['unsorted']['add']['data'];
        } else {
            return [];
        }

        return count($values) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление неразобранных заявок с типом SIP
     *
     * Метод позволяет добавлять неразобранные заявки по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param array $sip Массив неразобранных заявок для пакетного добавления
     * @return int|array Уникальный идентификатор заявки или массив при пакетном добавлении
     */
    public function apiAddSip($sip = [])
    {
        return $this->apiAdd(self::TYPE_SIP, $sip);
    }

    /**
     * Добавление неразобранных заявок с типом MAIL
     *
     * Метод позволяет добавлять неразобранные заявки по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param array $mails Массив неразобранных заявок для пакетного добавления
     * @return int|array Уникальный идентификатор заявки или массив при пакетном добавлении
     */
    public function apiAddMail($mails = [])
    {
        return $this->apiAdd(self::TYPE_MAIL, $mails);
    }

    /**
     * Добавление неразобранных заявок с типом FORMS
     *
     * Метод позволяет добавлять неразобранные заявки по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param array $forms Массив неразобранных заявок для пакетного добавления
     * @return int|array Уникальный идентификатор заявки или массив при пакетном добавлении
     */
    public function apiAddForms($forms = [])
    {
        return $this->apiAdd(self::TYPE_FORMS, $forms);
    }

    /**
     * Добавление сущности которая будет создана после одобрения заявки.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param string $type Тип сущности
     * @param mixed $values Объект или массив сущностей
     * @return $this
     */
    public function addData($type, $values)
    {
        if (!isset($this->values['data'][$type])) {
            $this->values['data'][$type] = [];
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            if ($value instanceof AbstractModel) {
                $this->values['data'][$type][] = $value->getValues();
            }
        }

        return $this;
    }

    /**
     * Добавление сделки которая будет создана после одобрения заявки.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param mixed $values Сделка или массив сделок
     * @return $this
     */
    public function addDataLead($values)
    {
        return $this->addData('leads', $values);
    }

    /**
     * Добавление контакта или компании которая будет создана после одобрения заявки.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/add.php
     * @param mixed $values Контакт или массив контактов
     * @return $this
     */
    public function addDataContact($values)
    {
        return $this->addData('contacts', $values);
    }
}
