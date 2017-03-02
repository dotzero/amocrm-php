<?php

namespace AmoCRM\Models;

/**
 * Class Links
 *
 * Класс модель для работы со Связями между сущностями
 *
 * @package AmoCRM\Models
 * @author mb@baso-it.ru
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
    protected $fields = [];

    /**
     * Связи между сущностями
     *
     * Метод для получения связей между сущностями аккаунта.
     * @link https://developers.amocrm.ru/rest_api/links/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters)
    {
        $response = $this->getRequest('/private/api/v2/json/links/list', $parameters);

        return isset($response['links']) ? $response['links'] : [];
    }
}
