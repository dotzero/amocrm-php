<?php

namespace AmoCRM\Models;

/**
 * Class Widgets
 *
 * Класс модель для работы с Виджетами
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Widgets extends AbstractModel
{
    /**
     * Список виджетов
     *
     * Метод для получения списка доступных для установки виджетов.
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters = [])
    {
        $response = $this->getRequest('/private/api/v2/json/widgets/list', $parameters);

        return isset($response['widgets']) ? $response['widgets'] : [];
    }

    /**
     * Включение виджетов
     *
     * Метод позволяет включать виджеты по одному или пакетно,
     * а также обновлять данные включённых и выключенных виджетов.
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/set.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiInstall($parameters)
    {
        $parameters = [
            'widgets' => [
                'install' => $parameters,
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/widgets/set', $parameters);

        return isset($response['widgets']['install']) ? $response['widgets']['install'] : [];
    }

    /**
     * Выключение виджетов
     *
     * Метод позволяет выключать виджеты по одному или пакетно,
     * а также обновлять данные включённых и выключенных виджетов.
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/set.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiUninstall($parameters)
    {
        $parameters = [
            'widgets' => [
                'uninstall' => $parameters,
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/widgets/set', $parameters);

        return isset($response['widgets']['uninstall']) ? $response['widgets']['uninstall'] : [];
    }
}