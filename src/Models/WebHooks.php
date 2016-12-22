<?php

namespace AmoCRM\Models;

/**
 * Class WebHooks
 *
 * Класс модель для работы с WebHooks
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class WebHooks extends Base
{
    /**
     * @var array Список всех доступных событий
     */
    public $events = [
        'add_lead', // Добавить сделку
        'add_contact', // Добавить контакт
        'add_company', // Добавить компанию
        'add_customer', // Добавить покупателя
        'update_lead', // Изменить сделку
        'update_contact', // Изменить контакт
        'update_company', // Изменить компанию
        'update_customer', // Изменить покупателя
        'delete_lead', // Удалить сделку
        'delete_contact', // Удалить контакт
        'delete_company', // Удалить компанию
        'delete_customer', // Удалить покупателя
        'status_lead', // Смена статуса сделки
        'responsible_lead', // Смена отв-го сделки
        'restore_contact', // Восстановить контакт
        'restore_company', // Восстановить компанию
        'restore_lead', // Восстановить сделку
        'note_lead', // Примечание в сделке
        'note_contact', // Примечание в контакте
        'note_company', // Примечание в компании
        'note_customer', // Примечание в покупателе
    ];

    /**
     * Список WebHooks
     *
     * Метод для получения списка WebHooks.
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/list.php
     * @return array Ответ amoCRM API
     */
    public function apiList()
    {
        $response = $this->getRequest('/private/api/v2/json/webhooks/list');

        return isset($response['webhooks']) ? $response['webhooks'] : [];
    }

    /**
     * Добавление WebHooks
     *
     * Метод для добавления WebHooks.
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/subscribe.php
     * @param string $url URL на который необходимо присылать уведомления, должен соответствоать стандарту RFC 2396
     * @param string|array $events Список событий, при которых должны отправляться WebHooks
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiSubscribe($url, $events = [])
    {
        if (!is_array($events)) {
            $events = [$events];
        } elseif (empty($events)) {
            $events = $this->events;
        }

        $parameters = [
            'webhooks' => [
                'subscribe' => [
                    [
                        'url' => $url,
                        'events' => $events,
                    ]
                ],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/webhooks/subscribe', $parameters);

        if (isset($response['webhooks']['subscribe'][0]['result'])) {
            return $response['webhooks']['subscribe'][0]['result'];
        }

        return false;
    }

    /**
     * Удаления WebHooks
     *
     * Метод для удаления WebHooks.
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/unsubscribe.php
     * @param string $url URL на который необходимо присылать уведомления, должен соответствоать стандарту RFC 2396
     * @param string|array $events Список событий, от которых необходимо отписать WebHook
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiUnsubscribe($url, $events = [])
    {
        if (!is_array($events)) {
            $events = [$events];
        } elseif (empty($events)) {
            $events = $this->events;
        }

        $parameters = [
            'webhooks' => [
                'unsubscribe' => [
                    [
                        'url' => $url,
                        'events' => $events,
                    ]
                ],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/webhooks/unsubscribe', $parameters);

        if (isset($response['webhooks']['unsubscribe'][0]['result'])) {
            return $response['webhooks']['unsubscribe'][0]['result'];
        }

        return false;
    }
}