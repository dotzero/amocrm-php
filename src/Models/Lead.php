<?php

    namespace AmoCRM\Models;

    /**
     * Class Lead
     *
     * Класс модель для работы со Сделками
     *
     * @package AmoCRM\Models
     * @version 0.1.0
     * @author dotzero <mail@dotzero.ru>
     * @link http://www.dotzero.ru/
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */
    class Lead extends Base
    {
        /**
         * @var array Список доступный полей для модели (исключая кастомные поля)
         */
        protected $fields = [
            'name',
            'date_create',
            'last_modified',
            'status_id',
            'price',
            'responsible_user_id',
            'request_id',
        ];

        /**
         * Сеттер для даты создания сделки
         *
         * @param string $date Дата в произвольном формате
         * @return $this
         */
        public function setDateCreate($date)
        {
            $this->values['date_create'] = strtotime($date);

            return $this;
        }

        /**
         * Сеттер для даты последнего изменения сделки
         *
         * @param string $date Дата в произвольном формате
         * @return $this
         */
        public function setLastModified($date)
        {
            $this->values['last_modified'] = strtotime($date);

            return $this;
        }

        /**
         * Список сделок
         *
         * Метод для получения списка сделок с возможностью фильтрации и постраничной выборки.
         * Ограничение по возвращаемым на одной странице (offset) данным - 500 сделок
         *
         * @link https://developers.amocrm.ru/rest_api/leads_list.php
         * @param array  $parameters
         * @param null|string $modified
         * @return array
         */
        public function apiList($parameters, $modified = null)
        {
            $response = $this->getRequest('/private/api/v2/json/leads/list', $parameters, $modified);

            return isset($response['leads']) ? $response['leads'] : [];
        }

        /**
         * Добавление сделки
         *
         * Метод позволяет добавлять сделки по одной или пакетно
         *
         * @link https://developers.amocrm.ru/rest_api/leads_set.php
         * @param array $leads
         * @return int|array
         */
        public function apiAdd($leads = [])
        {
            if (empty($leads)) {
                $leads = [$this];
            }

            $parameters = [
                'leads' => [
                    'add' => [],
                ],
            ];

            foreach ($leads AS $lead) {
                $parameters['leads']['add'][] = $lead->getValues();
            }

            $response = $this->postRequest('/private/api/v2/json/leads/set', $parameters);

            if (isset($response['leads']['add'])) {
                $result = array_map(function ($item) {
                    return $item['id'];
                }, $response['leads']['add']);
            } else {
                return false;
            }

            return count($leads) == 1 ? array_shift($result) : $result;
        }

        /**
         * Обновление сделки
         *
         * Метод позволяет обновлять данные по уже существующим сделкам
         *
         * @link https://developers.amocrm.ru/rest_api/leads_set.php
         * @param int    $id
         * @param string $modified
         * @return bool
         */
        public function apiUpdate($id, $modified = 'now')
        {
            $parameters = [
                'leads' => [
                    'update' => [],
                ],
            ];

            $lead = $this->getValues();
            $lead['id'] = $id;
            $lead['last_modified'] = strtotime($modified);

            $parameters['leads']['update'][] = $lead;

            $response = $this->postRequest('/private/api/v2/json/leads/set', $parameters);

            return isset($response['leads']) ? true : false;
        }
    }