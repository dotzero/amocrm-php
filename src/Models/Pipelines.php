<?php

namespace AmoCRM\Models;

/**
 * Class Pipelines
 *
 * Класс модель для работы с списком воронок и этапов продаж
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Pipelines extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'name',
        'sort',
        'is_main',
        'statuses',
    ];

    /**
     * Сеттер для поля - является ли воронка "главной"
     *
     * @param string $flag Флаг состояния
     * @return $this
     */
    public function setIsMain($flag)
    {
        if ($flag) {
            $this->values['is_main'] = 'on';
        } else {
            unset($this->values['is_main']);
        }

        return $this;
    }

    /**
     * Список воронок и этапов продаж
     *
     * Метод для получения списка воронок и этапов продаж.
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/list.php
     * @param null|int $id Уникальный идентификатор воронки
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiList($id = null)
    {
        if ($id !== null) {
            $this->checkId($id);

            $response = $this->getRequest('/private/api/v2/json/pipelines/list', [
                'id' => $id
            ]);

            return isset($response['pipelines'][$id]) ? $response['pipelines'][$id] : [];
        }

        $response = $this->getRequest('/private/api/v2/json/pipelines/list');

        return isset($response['pipelines']) ? $response['pipelines'] : [];
    }

    /**
     * Добавление воронок и этапов продаж
     *
     * Метод позволяет добавлять воронки и этапов продаж по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/set.php
     * @param array $pipelines Массив воронок для пакетного добавления
     * @return int|array Уникальный идентификатор воронки или массив при пакетном добавлении
     */
    public function apiAdd($pipelines = [])
    {
        if (empty($pipelines)) {
            $pipelines = [$this];
        }

        $parameters = [
            'pipelines' => [
                'add' => [],
            ],
        ];

        foreach ($pipelines AS $pipeline) {
            $parameters['pipelines']['add'][] = $pipeline->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/pipelines/set', $parameters);

        if (isset($response['pipelines']['add']['pipelines'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['pipelines']['add']['pipelines']);
        } else {
            return [];
        }

        return count($pipelines) == 1 ? array_shift($result) : $result;
    }

    /**
     * Обновление воронок и этапов продаж
     *
     * Метод позволяет обновлять данные по уже существующим воронкам и этапам продаж
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/set.php
     * @param int $id Уникальный идентификатор воронки
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id)
    {
        $this->checkId($id);

        $parameters = [
            'pipelines' => [
                'update' => [],
            ],
        ];

        $pipeline = $this->getValues();
        $pipeline['id'] = $id;

        $parameters['pipelines']['update'][$id] = $pipeline;

        $response = $this->postRequest('/private/api/v2/json/pipelines/set', $parameters);

        return isset($response['pipelines']) ? true : false;
    }

    /**
     * Удаление воронок
     *
     * Метод позволяет удалять воронки по одной или пакетно
     *
     * Удаление последней воронки в аккаунте невозможно,
     * при удалении последней воронки выдается ошибка
     * "Impossible to delete last pipeline"
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/delete.php
     * @param int $id Уникальный идентификатор воронки
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiDelete($id)
    {
        $parameters = [
            'id' => $id
        ];

        $response = $this->postRequest('/private/api/v2/json/pipelines/delete', $parameters);

        return $response;
    }

    /**
     * Добавление этапов воронки, необходимо передать хотя бы один этап,
     * кроме успешно/неуспешно завершенного.
     *
     * Для этапов успешно/неуспешно завершено (id 142/143 соответственно)
     * возможно передать только поле name
     *
     * @param mixed $parameters Параметры этапа воронки
     * @param int $id Уникальный идентификатор этапа воронки
     * @return $this
     */
    public function addStatusField($parameters, $id = null)
    {
        if ($id === null) {
            $this->values['statuses'][] = $parameters;
        } else {
            $this->checkId($id);
            $parameters['id'] = $id;
            $this->values['statuses'][$id] = $parameters;
        }

        return $this;
    }
}
