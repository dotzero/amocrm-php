<?php

namespace AmoCRM\Models;

/**
 * Class Account
 *
 * Класс модель для работы с Аккаунтом
 *
 * @package AmoCRM\Models
 * @version 0.3.1
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Account extends Base
{
    /**
     * Данные по аккаунту
     *
     * Получение информации по аккаунту в котором произведена авторизация:
     * название, оплаченный период, пользователи аккаунта и их права,
     * справочники дополнительных полей контактов и сделок, справочник статусов сделок,
     * справочник типов событий, справочник типов задач и другие параметры аккаунта.
     *
     * @link https://developers.amocrm.ru/rest_api/accounts_current.php
     * @param bool $short Краткий формат, только основные поля
     * @return array Ответ amoCRM API
     */
    public function apiCurrent($short = false)
    {
        $result = $this->getRequest('/private/api/v2/json/accounts/current');

        return $short ? $this->getShorted($result['account']) : $result['account'];
    }

    /**
     * Урезание значения возвращаемоного методом apiCurrent,
     * оставляет только основные поля такие как 'id', 'name', 'type_id', 'enums'
     *
     * @param array $account Ответ amoCRM API
     * @return mixed Краткий ответ amoCRM API
     */
    private function getShorted($account)
    {
        $keys = array_fill_keys(['id', 'name', 'login'], 1);
        $account['users'] = array_map(function($val) use ($keys) {
            return array_intersect_key($val, $keys);
        }, $account['users']);

        $keys = array_fill_keys(['id', 'name'], 1);
        $account['leads_statuses'] = array_map(function($val) use ($keys) {
            return array_intersect_key($val, $keys);
        }, $account['leads_statuses']);

        $keys = array_fill_keys(['id', 'name'], 1);
        $account['note_types'] = array_map(function($val) use ($keys) {
            return array_intersect_key($val, $keys);
        }, $account['note_types']);

        $keys = array_fill_keys(['id', 'name'], 1);
        $account['task_types'] = array_map(function($val) use ($keys) {
            return array_intersect_key($val, $keys);
        }, $account['task_types']);

        $keys = array_fill_keys(['id', 'name', 'type_id', 'enums'], 1);
        foreach ($account['custom_fields'] AS $type => $fields) {
            $account['custom_fields'][$type] = array_map(function($val) use ($keys) {
                return array_intersect_key($val, $keys);
            }, $fields);
        }

        $keys = array_fill_keys(['id', 'label', 'name'], 1);
        $account['pipelines'] = array_map(function($val) use ($keys) {
            return array_intersect_key($val, $keys);
        }, $account['pipelines']);

        return $account;
    }
}
