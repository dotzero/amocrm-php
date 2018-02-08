<?php

namespace AmoCRM\Models;

use AmoCRM\Models\Traits\SetTags;
use AmoCRM\Models\Traits\SetNextDate;

/**
 * Class Customer
 *
 * Класс модель для работы с Покупателями
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Customer extends AbstractModel
{
    use SetTags, SetNextDate;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'name',
        'main_user_id',
        'created_by',
        'next_price',
        'periodicity',
        'tags',
        'next_date',
        'request_id',
    ];

    /**
     * Список покупателей
     *
     * Метод для получения покупателей аккаунта
     *
     * @link https://developers.amocrm.ru/rest_api/customers/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters)
    {
        $response = $this->getRequest('/private/api/v2/json/customers/list', $parameters);

        return isset($response['customers']) ? $response['customers'] : [];
    }

    /**
     * Добавление покупателей
     *
     * Метод позволяет добавлять покупателей по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     * @param array $customers Массив покупателей для пакетного добавления
     * @return int|array Уникальный идентификатор покупателя или массив при пакетном добавлении
     */
    public function apiAdd($customers = [])
    {
        if (empty($customers)) {
            $customers = [$this];
        }

        $parameters = [
            'customers' => [
                'add' => [],
            ],
        ];

        foreach ($customers AS $customer) {
            $parameters['customers']['add'][] = $customer->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/customers/set', $parameters);

        if (isset($response['customers']['add'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['customers']['add']['customers']);
        } else {
            return [];
        }

        return count($customers) == 1 ? array_shift($result) : $result;
    }

    /**
     * Обновление покупателей
     *
     * Метод позволяет обновлять данные по уже существующим покупателям
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     * @param int $id Уникальный идентификатор покупателя
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id)
    {
        $this->checkId($id);

        $parameters = [
            'customers' => [
                'update' => [],
            ],
        ];

        $customer = $this->getValues();
        $customer['id'] = $id;

        $parameters['customers']['update'][] = $customer;

        $response = $this->postRequest('/private/api/v2/json/customers/set', $parameters);

        return isset($response['customers']) ? true : false;
    }
}
