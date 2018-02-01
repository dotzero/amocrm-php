<?php

namespace AmoCRM\Webhooks;

use AmoCRM\Exception;

/**
 * Class Listener
 *
 * Назначения и вызов callback при уведомлениях.
 *
 * Webhooks – это уведомление сторонних приложений посредством отправки уведомлений о событиях,
 * произошедших в amoCRM. Вы можете настроить HTTP адреса ваших приложений и связанные с ними
 * рабочие правила в настройках своего аккаунта, в разделе «API».
 *
 * @package AmoCRM\Webhooks
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Listener
{
    /**
     * @var array Список callback функций
     */
    private $hooks = [];

    /**
     * @var array Список всех доступных событий
     */
    public $events_list = [
        'add_lead', // Добавить сделку
        'add_contact', // Добавить контакт
        'add_company', // Добавить компанию
        'add_customer', // Добавить покупателя
        'add_task', // Добавить покупателя
        'update_lead', // Изменить сделку
        'update_contact', // Изменить контакт
        'update_company', // Изменить компанию
        'update_customer', // Изменить покупателя
        'update_task', // Изменить покупателя
        'delete_lead', // Удалить сделку
        'delete_contact', // Удалить контакт
        'delete_company', // Удалить компанию
        'delete_customer', // Удалить покупателя
        'delete_task', // Удалить задачу
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
     * Добавление события на уведомление в список событий
     *
     * @param string|array $events Код события или массив событий
     * @param callback|callable $callback Callback-функция
     * @return $this
     * @throws Exception
     */
    public function on($events, $callback)
    {
        if (!is_array($events)) {
            $events = [$events];
        }

        if (!is_callable($callback, true)) {
            throw new Exception('Callback must be callable');
        }

        foreach ($events as $event) {
            if (!in_array($event, $this->events_list)) {
                throw new Exception('Invalid event name');
            }

            if (!isset($this->hooks[$event])) {
                $this->hooks[$event] = [];
            }

            $this->hooks[$event][] = $callback;
        }

        return $this;
    }

    /**
     * Вызов обработчика уведомлений
     *
     * @return bool
     */
    public function listen()
    {
        if (!isset($_POST['account']['subdomain']) || empty($this->hooks)) {
            return false;
        }

        $post = $_POST;
        $domain = $post['account']['subdomain'];
        unset($post['account']);

        foreach ($post as $entityName => $entityData) {
            foreach ($entityData as $actionName => $actionData) {
                foreach ($actionData as $data) {
                    $type = $entityName;
                    switch ($entityName) {
                        case 'contacts':
                            $type = $data['type'];
                            break;
                        case 'leads':
                            $type = 'lead';
                            break;
                    }

                    $callback = $actionName . '_' . $type;
                    $id = isset($data['id']) ? $data['id'] : null;

                    $this->fireCallback($callback, $domain, $id, $data);
                }
            }
        }

        return true;
    }

    /**
     * Очистка списка событий
     *
     * @return $this
     */
    public function clean()
    {
        $this->hooks = [];

        return $this;
    }

    /**
     * Вызов Callback-функции на уведомление
     *
     * @param string $name Код события
     * @param string $domain Поддомен amoCRM
     * @param int $id Id объекта связанного с уведомлением
     * @param array $data Поля возвращаемые уведомлением
     */
    private function fireCallback($name, $domain, $id, $data)
    {
        $callbacks = isset($this->hooks[$name]) ? $this->hooks[$name] : [];

        foreach ($callbacks AS $callback) {
            call_user_func($callback, $domain, $id, $data);
        }
    }
}
