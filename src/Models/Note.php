<?php

namespace AmoCRM\Models;

/**
 * Class Note
 *
 * Класс модель для работы с Примечаниями
 *
 * @package AmoCRM\Models
 * @version 0.1.0
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Note extends Base
{
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
    ];

    /**
     * Сеттер для даты создания примечания
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
     * Сеттер для даты последнего изменения примечания
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
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['notes']['add']);
        } else {
            return false;
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
     */
    public function apiUpdate($id, $modified = 'now')
    {
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

        return isset($response['notes']) ? true : false;
    }
}
