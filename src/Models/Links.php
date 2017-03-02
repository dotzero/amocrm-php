<?php

namespace AmoCRM\Models;

/**
 * Class Links
 *
 * Класс модель для работы со Связями между сущностями
 *
 * @package AmoCRM\Models
 * @author mb@baso-it.ru
 * @author dotzero <mail@dotzero.ru>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Links extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'from',
        'from_id',
        'to',
        'to_id',
        'from_catalog_id',
        'to_catalog_id',
        'quantity',
    ];

    /**
     * Связи между сущностями
     *
     * Метод для получения связей между сущностями аккаунта
     *
     * @link https://developers.amocrm.ru/rest_api/links/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters)
    {
        if (!isset($parameters['links'])) {
            $parameters = [
                'links' => [
                    $parameters
                ]
            ];
        }

        $response = $this->getRequest('/private/api/v2/json/links/list', $parameters);

        return isset($response['links']) ? $response['links'] : [];
    }

    /**
     * Установка связи между сущностями
     *
     * Метод позволяет устанавливать связи между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     * @param array $links Массив связей для пакетного добавления
     * @return bool Флаг успешности выполнения запроса
     */
    public function apiLink($links = [])
    {
        if (empty($links)) {
            $links = [$this];
        }

        $parameters = [
            'links' => [
                'link' => [],
            ],
        ];

        foreach ($links AS $link) {
            $parameters['links']['link'][] = $link->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/links/set', $parameters);

        if (!isset($response['links']['link']['errors'])) {
            return false;
        }

        return empty($response['links']['link']['errors']);
    }

    /**
     * Разрыв связи между сущностями
     *
     * Метод позволяет удалять связи между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     * @param array $links Массив связей для пакетного удаления
     * @return bool Флаг успешности выполнения запроса
     */
    public function apiUnlink($links = [])
    {
        if (empty($links)) {
            $links = [$this];
        }

        $parameters = [
            'links' => [
                'unlink' => [],
            ],
        ];

        foreach ($links AS $link) {
            $parameters['links']['unlink'][] = $link->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/links/set', $parameters);

        if (!isset($response['links']['unlink']['errors'])) {
            return false;
        }

        return empty($response['links']['unlink']['errors']);
    }
}
