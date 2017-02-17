<?php

namespace AmoCRM\Models;

/**
 * Class CustomersPeriods
 *
 * Класс модель для работы со Списком периодов
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CustomersPeriods extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'id',
        'period',
        'sort',
        'color',
    ];

    /**
     * Список периодов
     *
     * Метод для получения списка периодов
     *
     * @link https://developers.amocrm.ru/rest_api/customers_periods/list.php
     * @return array Ответ amoCRM API
     */
    public function apiList()
    {
        $response = $this->getRequest('/private/api/v2/json/customers_periods/list');

        return isset($response['customers_periods']['list']) ? $response['customers_periods']['list'] : [];
    }

    /**
     * Добавление, удаление и обновление периодов покупателей
     *
     * Метод позволяет изменять данные по периодам.
     * При изменении необходимо передать полный список периодов, включая уже существующие.
     * При удалении периода нужно исключить его из запроса.
     *
     * @link https://developers.amocrm.ru/rest_api/customers_periods/set.php
     * @param array periods Массив периодов
     * @return int|array Уникальный идентификатор периода или массив при пакетном добавлении
     */
    public function apiSet($periods = [])
    {
        if (empty($periods)) {
            $periods = [$this];
        }

        $parameters = [
            'customers_periods' => [
                'update' => [],
            ],
        ];

        foreach ($periods AS $period) {
            if ($period instanceof self) {
                $period = $period->getValues();
            }
            $parameters['customers_periods']['update'][] = $period;
        }

        $response = $this->postRequest('/private/api/v2/json/customers_periods/set', $parameters);

        if (isset($response['customers_periods']['set'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['customers_periods']['set']);
        } else {
            return [];
        }

        return count($periods) == 1 ? array_shift($result) : $result;
    }
}
