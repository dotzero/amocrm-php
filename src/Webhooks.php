<?php

namespace AmoCRM;

/**
 * Class Webhooks
 *
 * Класс для добавление и вызова WebHooks
 *
 * WebHooks – это уведомление сторонних приложений посредством отправки уведомлений о событиях,
 * произошедших в amoCRM. Вы можете настроить HTTP адреса ваших приложений и связанные с ними
 * рабочие правила в настройках своего аккаунта, в разделе «API».
 *
 * @package AmoCRM
 * @version 0.3.1
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Webhooks
{
    /**
     * @var array Список callback функций
     */
    private $hooks = [];

    /**
     * Добавление события на уведомление в список событий
     *
     * @param string|array $events Код события или массив событий
     * @param callback $callback Callback-функция
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
            if (!isset($this->hooks[$event])) {
                $this->hooks[$event] = [];
            }

            $this->hooks[$event][] = $callback;
        }
    }

    /**
     * Вызов обработчика уведомлений
     *
     * @return bool
     */
    public function listen()
    {
        if (!isset($_POST['account']['subdomain'])) {
            return false;
        }

        $post = $_POST;
        $domain = $post['account']['subdomain'];
        unset($post['account']);

        foreach ($post AS $entityName => $entityData) {
            foreach ($entityData AS $actionName => $actionData) {
                $data = $actionData[0];
                $callback = (isset($data['type']) && $data['type'] == 'company') ? 'companies' : $entityName;
                $callback .= '-' . $actionName;
                $this->fireCallback($callback, $domain, $data['id'], $data);
            }
        }

        return true;
    }

    /**
     * Вызов Callback-функции на уведомление
     *
     * @param string $name Код события
     * @param string $domain Поддомен amoCRM
     * @param int $id Id объекта связаного с уведомленим
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
