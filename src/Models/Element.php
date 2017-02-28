<?php

namespace AmoCRM\Models;

/**
 * Class Element
 *
 * Класс модель для работы с Элементами каталога
 *
 * @package AmoCRM\Models
 * @author mb@baso-it.ru
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Element extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [];

    /**
     * Список элементов каталога
     *
     * Метод для получения элементов каталога аккаунта.
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters)
    {
        $response = $this->getRequest('/private/api/v2/json/catalog_elements/list', $parameters);

        return isset($response['catalog_elements']) ? $response['catalog_elements'] : [];
    }
}
