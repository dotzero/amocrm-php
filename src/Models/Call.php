<?php

namespace AmoCRM\Models;

use AmoCRM\Models\Traits\SetDate;

/**
 * Class Call
 *
 * Класс модель для работы со Звонками
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Call extends AbstractModel
{
    use SetDate;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'account_id',
        'uuid',
        'caller',
        'to',
        'date',
        'type',
        'billsec',
        'link',
    ];

    /**
     * @const string Тип звонка входящий
     */
    const TYPE_INBOUND = 'inbound';

    /**
     * @const string Тип звонка исходящий
     */
    const TYPE_OUTBOUND = 'outbound';

    /**
     * Добавление звонков
     *
     * Метод позволяет добавлять звонки по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/calls_set.php
     * @param string $code Уникальный идентификатор сервиса
     * @param string $key Ключ сервиса, который можно получить написав в техническую поддержку amoCRM
     * @param array $calls Массив звонков для пакетного добавления
     * @return string|array Уникальный идентификатор звонка или массив при пакетном добавлении
     */
    public function apiAdd($code, $key, $calls = [])
    {
        $this->getParameters()->addGet('code', $code);
        $this->getParameters()->addGet('key', $key);

        if (empty($calls)) {
            $calls = [$this];
        }

        $parameters = [
            'add' => [],
        ];

        foreach ($calls AS $call) {
            $parameters['add'][] = $call->getValues();
        }

        $response = $this->postRequest('/api/calls/add/', $parameters);

        $result = [];
        if (!isset($response['calls']['add']['errors'])) {
            return $result;
        } elseif (isset($response['calls']['add']['success'])) {
            $result = $response['calls']['add']['success'];
        }

        return count($calls) == 1 ? array_shift($result) : $result;
    }
}
