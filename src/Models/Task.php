<?php

namespace AmoCRM\Models;

use AmoCRM\Exception;
use AmoCRM\Models\Traits\SetDateCreate;
use AmoCRM\Models\Traits\SetLastModified;
use AmoCRM\NetworkException;

/**
 * Class Task
 *
 * Класс модель для работы с Задачами
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Task extends AbstractModel
{
    use SetDateCreate, SetLastModified;
    
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'responsible_user_id',
        'group_id',
        'entity_id',
        'entity_type',
        'duration',
        'is_completed',
        'task_type_id',
        'text',
        'result',
        'complete_till',
        'account_id'
    ];
    
    /**
     * @const int Типа задачи Контакт
     */
    const TYPE_CONTACT = 1;
    
    /**
     * @const int Типа задачи Сделка
     */
    const TYPE_LEAD = 2;
    
    /**
     * Сеттер для дата до которой необходимо завершить задачу
     *
     * Если указано время 23:59, то в интерфейсах системы
     * вместо времени будет отображаться "Весь день"
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setCompleteTill($date)
    {
        $this->values['complete_till'] = strtotime($date);
        
        return $this;
    }
    
    /**
     * Список задач
     * Метод для получения списка задач с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 задач
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#tasks-list
     * @param array       $parameters Массив параметров к amoCRM API
     * @param string      $url
     * @param null|string $modified   Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     * @throws Exception
     * @throws NetworkException
     */
    public function apiList($parameters, $url = '/api/v4/tasks', $modified = null)
    {
        $response = $this->getRequest($url, $parameters, $modified);
    
        return isset($response['_embedded']['tasks']) ? $response['_embedded']['tasks'] : [];
    }
    
    /**
     * Получение одной задачи
     * Метод для получения одной задачи по id
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#task-detail
     * @param string      $taskId   идентификатор задачи
     * @param string      $url
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     * @throws Exception
     * @throws NetworkException
     */
    public function apiListItem($taskId, $url = '/api/v4/tasks/', $parameters = [], $modified = null)
    {
        $response = $this->getRequest($url.$taskId, $parameters, $modified);
        
        return $response ? : [];
    }
    
    /**
     * Добавление задачи
     *
     * Метод позволяет добавлять задачи по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     * @param array $tasks Массив задач для пакетного добавления
     * @return int|array Уникальный идентификатор задачи или массив при пакетном добавлении
     */
    public function apiAdd($tasks = [])
    {
        if (empty($tasks)) {
            $tasks = [$this];
        }
        
        $parameters = [
            'tasks' => [
                'add' => [],
            ],
        ];
        
        foreach ($tasks AS $task) {
            $parameters['tasks']['add'][] = $task->getValues();
        }
        
        $response = $this->postRequest('/private/api/v2/json/tasks/set', $parameters);
        
        if (isset($response['tasks']['add'])) {
            $result = array_map(function($item) {
                return $item['id'];
            }, $response['tasks']['add']);
        } else {
            return [];
        }
        
        return count($tasks) == 1 ? array_shift($result) : $result;
    }
    
    /**
     * Обновление задачи
     *
     * Метод позволяет обновлять данные по уже существующим задачам
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     * @param int $id Уникальный идентификатор задачи
     * @param string $text Текст задачи
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws Exception
     */
    public function apiUpdate($id, $text, $modified = 'now')
    {
        $this->checkId($id);
        
        $parameters = [
            'tasks' => [
                'update' => [],
            ],
        ];
        
        $task = $this->getValues();
        $task['id'] = $id;
        $task['text'] = $text;
        $task['last_modified'] = strtotime($modified);
        
        $parameters['tasks']['update'][] = $task;
        
        $response = $this->postRequest('/private/api/v2/json/tasks/set', $parameters);
        
        return empty($response['tasks']['update']['errors']);
    }
}
