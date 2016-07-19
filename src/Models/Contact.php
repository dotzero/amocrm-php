<?php

namespace AmoCRM\Models;

/**
 * Class Contact
 *
 * Класс модель для работы с Контактами
 *
 * @package AmoCRM\Models
 * @version 0.3.1
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Contact extends Base
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'name',
        'request_id',
        'date_create',
        'last_modified',
        'responsible_user_id',
        'linked_leads_id',
        'company_name',
        'tags',
    ];

    /**
     * Сеттер для даты создания контакта
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setDateCreate($date)
    {
        $this->values['date_create'] = strtotime($date);

        return $this;
    }

    /**
     * Сеттер для даты последнего изменения контакта
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setLastModified($date)
    {
        $this->values['last_modified'] = strtotime($date);

        return $this;
    }

    /**
     * Сеттер для списка связанных сделок контакта
     *
     * @param int|array $value Номер связанной сделки или список сделок
     * @return $this
     */
    public function setLinkedLeadsId($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->values['linked_leads_id'] = $value;

        return $this;
    }

    /**
     * Сеттер для списка тегов контакта
     *
     * @param int|array $value Название тегов через запятую или массив тегов
     * @return $this
     */
    public function setTags($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->values['tags'] = implode(',', $value);

        return $this;
    }

    /**
     * Список контактов
     *
     * Метод для получения списка контактов с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 контактов.
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/contacts/list', $parameters, $modified);

        return isset($response['contacts']) ? $response['contacts'] : [];
    }

    /**
     * Добавление контактов
     *
     * Метод позволяет добавлять контакты по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     * @param array $contacts Массив контактов для пакетного добавления
     * @return int|array Уникальный идентификатор контакта или массив при пакетном добавлении
     */
    public function apiAdd($contacts = [])
    {
        if (empty($contacts)) {
            $contacts = [$this];
        }

        $parameters = [
            'contacts' => [
                'add' => [],
            ],
        ];

        foreach ($contacts AS $contact) {
            $parameters['contacts']['add'][] = $contact->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/contacts/set', $parameters);

        if (isset($response['contacts']['add'])) {
            $result = array_map(function($item) {
                return $item['id'];
            }, $response['contacts']['add']);
        } else {
            return [];
        }

        return count($contacts) == 1 ? array_shift($result) : $result;
    }

    /**
     * Обновление контактов
     *
     * Метод позволяет обновлять данные по уже существующим контактам
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     * @param int $id Уникальный идентификатор контакта
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id, $modified = 'now')
    {
        $this->checkId($id);

        $parameters = [
            'contacts' => [
                'update' => [],
            ],
        ];

        $contact = $this->getValues();
        $contact['id'] = $id;
        $contact['last_modified'] = strtotime($modified);

        $parameters['contacts']['update'][] = $contact;

        $response = $this->postRequest('/private/api/v2/json/contacts/set', $parameters);

        return isset($response['contacts']) ? true : false;
    }

    /**
     * Связи между сделками и контактами
     *
     * Метод для получения списка связей между сделками и контактами
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_links.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiLinks($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/contacts/links', $parameters, $modified);

        return isset($response['links']) ? $response['links'] : [];
    }
}
