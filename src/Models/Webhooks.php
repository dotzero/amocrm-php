<?php

namespace AmoCRM\Models;

/**
 * Class Webhooks
 *
 * Класс модель для работы с Webhooks
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Webhooks extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'url',
        'events',
    ];

    /**
     * @var array Список всех доступных событий
     */
    public $events_list = [
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
     * Сеттер для списка событий
     *
     * @param string|array $value Название события или массив событий
     * @return $this
     */
    public function setEvents($value)
    {
        if (empty($value)) {
            $value = $this->events_list;
        } elseif (!is_array($value)) {
            $value = [$value];
        }

        $this->values['events'] = $value;

        return $this;
    }

    /**
     * Список Webhooks
     *
     * Метод для получения списка Webhooks.
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
     * Добавление Webhooks
     *
     * Метод для добавления Webhooks.
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/subscribe.php
     * @param null|string $url URL на который необходимо присылать уведомления, должен соответствовать стандарту RFC 2396
     * @param array|string $events Список событий, при которых должны отправляться Webhooks
     * @return array|false Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiSubscribe($url = null, $events = [])
    {
        $parameters = [
            'url' => $url,
            'events' => $events,
        ];

        if ($url === null) {
            $parameters = $this->getValues();
        } elseif (!is_array($parameters['events'])) {
            $parameters['events'] = [$events];
        } elseif (empty($parameters['events'])) {
            $parameters['events'] = $this->events_list;
        }

        $parameters = [
            'webhooks' => [
                'subscribe' => [$parameters],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/webhooks/subscribe', $parameters);

        if (isset($response['webhooks']['subscribe'][0]['result'])) {
            return $response['webhooks']['subscribe'][0]['result'];
        }

        return false;
    }

    /**
     * Удаления Webhooks
     *
     * Метод для удаления Webhooks.
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/unsubscribe.php
     * @param null|string $url URL на который необходимо присылать уведомления, должен соответствовать стандарту RFC 2396
     * @param array|string $events Список событий, от которых необходимо отписать WebHook
     * @return array|false Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiUnsubscribe($url = null, $events = [])
    {
        $parameters = [
            'url' => $url,
            'events' => $events,
        ];

        if ($url === null) {
            $parameters = $this->getValues();
        } elseif (!is_array($parameters['events'])) {
            $parameters['events'] = [$events];
        } elseif (empty($parameters['events'])) {
            $parameters['events'] = $this->events_list;
        }

        $parameters = [
            'webhooks' => [
                'unsubscribe' => [$parameters],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/webhooks/unsubscribe', $parameters);

        if (isset($response['webhooks']['unsubscribe'][0]['result'])) {
            return $response['webhooks']['unsubscribe'][0]['result'];
        }

        return false;
    }
}
