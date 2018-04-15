<?php

namespace AmoCRM\Models;

use AmoCRM\Models\Traits\SetDate;
use AmoCRM\Models\Traits\SetNextDate;

/**
 * Class Transaction
 *
 * Класс модель для работы с транзакциями
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Transaction extends AbstractModel
{
    use SetDate, SetNextDate;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'customer_id',
        'date',
        'price',
        'comment',
        'request_id',
        'next_price',
        'next_date',
    ];

    /**
     * Список транзакций
     *
     * Метод для получения транзакций аккаунта.
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiList($parameters = [])
    {
        $response = $this->getRequest('/private/api/v2/json/transactions/list', $parameters);

        return isset($response['transactions']) ? $response['transactions'] : [];
    }

    /**
     * Добавление транзакций
     *
     * Метод позволяет добавлять транзакции по одной или пакетно.
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/set.php
     * @param array $transactions Массив транзакций для пакетного добавления
     * @return int|array Уникальный идентификатор транзакции или массив при пакетном добавлении
     */
    public function apiAdd($transactions = [])
    {
        if (empty($transactions)) {
            $transactions = [$this];
        }

        $parameters = [
            'transactions' => [
                'add' => [],
            ],
        ];

        foreach ($transactions AS $transaction) {
            $parameters['transactions']['add'][] = $transaction->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/transactions/set', $parameters);

        if (isset($response['transactions']['add']['transactions'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['transactions']['add']['transactions']);
        } else {
            return [];
        }

        return count($transactions) == 1 ? array_shift($result) : $result;
    }

    /**
     * Удаление элементов транзакций
     *
     * Метод позволяет удалять транзакции.
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/set.php
     * @param int $id Уникальный идентификатор транзакции
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiDelete($id)
    {
        $this->checkId($id);

        $parameters = [
            'transactions' => [
                'delete' => [$id],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/transactions/set', $parameters);

        if (!isset($response['transactions']['delete']['errors'])) {
            return false;
        }

        return empty($response['transactions']['delete']['errors']);
    }
}
