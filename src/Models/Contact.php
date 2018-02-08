<?php

namespace AmoCRM\Models;

use AmoCRM\Models\Traits\SetNote;
use AmoCRM\Models\Traits\SetTags;
use AmoCRM\Models\Traits\SetDateCreate;
use AmoCRM\Models\Traits\SetLastModified;
use AmoCRM\Models\Traits\SetLinkedLeadsId;

/**
 * Class Contact
 *
 * Класс модель для работы с Контактами
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Contact extends AbstractModel
{
    use SetNote, SetTags, SetDateCreate, SetLastModified, SetLinkedLeadsId;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'name',
        'request_id',
        'date_create',
        'last_modified',
        'responsible_user_id',
        'created_user_id',
        'linked_leads_id',
        'company_name',
        'linked_company_id',
        'tags',
        'notes',
        'modified_user_id',
    ];

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
            $result = array_map(function ($item) {
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

        return empty($response['contacts']['update']['errors']);
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
