<?php

namespace AmoCRM\Models;

/**
 * Class Catalog
 *
 * Класс модель для работы с Каталогами
 *
 * @package AmoCRM\Models
 * @author mb@baso-it.ru
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Catalog extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'id',
        'name',
        'created_by',
        'date_create'
    ];

    /**
     * Список каталогов
     *
     * Метод для получения списка каталогов с возможностью фильтрации и постраничной выборки.
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters=[])
    {
        $response = $this->getRequest('/private/api/v2/json/catalogs/list', $parameters);

        return isset($response['catalogs']) ? $response['catalogs'] : [];
    }
}
