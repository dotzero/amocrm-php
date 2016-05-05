<?php

    namespace AmoCRM\Models;

    /**
     * Class Company
     *
     * Класс модель для работы с Компаниями
     *
     * @package AmoCRM
     * @version 0.1.0
     * @author dotzero <mail@dotzero.ru>
     * @link http://www.dotzero.ru/
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */
    class Company extends Base
    {
        /**
         * @var array Список доступный полей для модели (исключая кастомные поля)
         */
        protected $fields = [
            'name',
            'request_id',
            'date_create',
            'last_modified',
            'responsible_user_id',
            'linked_leads_id',
            'tags',
        ];

        /**
         * Сеттер для даты создания компании
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
         * Сеттер для даты последнего изменения компании
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
         * Сеттер для списка связанных сделок компании
         *
         * @param int|array $value Номер связанной сделки или список сделок
         * @return $this
         */
        public function setLinkedLeadsId($value)
        {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->values['linked_leads_id'] = $value;

            return $this;
        }

        /**
         * Сеттер для списка тегов компании
         *
         * @param int|array $value Название тегов через запятую или массив тегов
         * @return $this
         */
        public function setTags($value)
        {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->values['tags'] = implode(',', $value);

            return $this;
        }

        /**
         * Список компаний
         *
         * Метод для получения списка компаний с возможностью фильтрации и постраничной выборки.
         * Ограничение по возвращаемым на одной странице (offset) данным - 500 компаний.
         *
         * @link https://developers.amocrm.ru/rest_api/company_list.php
         * @param array  $parameters
         * @param null|string $modified
         * @return array
         */
        public function apiList($parameters, $modified = null)
        {
            $response = $this->getRequest('/private/api/v2/json/company/list', $parameters, $modified);

            return isset($response['contacts']) ? $response['contacts'] : [];
        }

        /**
         * Добавление компаний
         *
         * Метод позволяет добавлять компании по одной или пакетно
         *
         * @link https://developers.amocrm.ru/rest_api/company_set.php
         * @param array $companies
         * @return int|array
         */
        public function apiAdd($companies = [])
        {
            if (empty($companies)) {
                $companies = [$this];
            }

            $parameters = [
                'contacts' => [
                    'add' => [],
                ],
            ];

            foreach ($companies AS $company) {
                $parameters['contacts']['add'][] = $company->getValues();
            }

            $response = $this->postRequest('/private/api/v2/json/company/set', $parameters);

            if (isset($response['contacts']['add'])) {
                $result = array_map(function ($item) {
                    return $item['id'];
                }, $response['contacts']['add']);
            } else {
                return false;
            }

            return count($companies) == 1 ? array_shift($result) : $result;
        }

        /**
         * Обновление компаний
         *
         * Метод позволяет обновлять данные по уже существующим компаниям
         *
         * @link https://developers.amocrm.ru/rest_api/company_set.php
         * @param int    $id
         * @param string $modified
         * @return bool
         */
        public function apiUpdate($id, $modified = 'now')
        {
            $parameters = [
                'contacts' => [
                    'update' => [],
                ],
            ];

            $company = $this->getValues();
            $company['id'] = $id;
            $company['last_modified'] = strtotime($modified);

            $parameters['contacts']['update'][] = $company;

            $response = $this->postRequest('/private/api/v2/json/company/set', $parameters);

            return isset($response['contacts']) ? true : false;
        }
    }