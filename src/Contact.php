<?php

    namespace AmoCRM;

    class Contact extends Base
    {
        protected $fields = [
            'name',
            'request_id',
            'date_create',
            'last_modified',
            'responsible_user_id',
            'linked_leads_id',
            'company_name',
            'tags',
        ];

        public function setDateCreate($date)
        {
            $this->values['date_create'] = strtotime($date);

            return $this;
        }

        public function setLastModified($date)
        {
            $this->values['last_modified'] = strtotime($date);

            return $this;
        }

        public function setLinkedLeadsId($value)
        {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->values['linked_leads_id'] = $value;

            return $this;
        }

        public function setTags($value)
        {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->values['tags'] = implode(',', $value);

            return $this;
        }

        /**
         * Список контактов
         *
         * Метод для получения списка контактов с возможностью фильтрации и постраничной выборки.
         * Ограничение по возвращаемым на одной странице (offset) данным - 500 контактов.
         *
         * @link https://developers.amocrm.ru/rest_api/contacts_list.php
         *
         * @param null|array  $parameters
         * @param null|string $modified
         *
         * @return array
         */
        public function apiList($parameters, $modified = null)
        {
            $response = $this->getRequest('/private/api/v2/json/contacts/list', $parameters, $modified);

            return isset($response['contacts']) ? $response['contacts'] : [];
        }

        /**
         * Добавление контактов
         *
         * Метод позволяет добавлять контакты по одному или пакетно,
         * а также обновлять данные по уже существующим контактам.
         *
         * @link https://developers.amocrm.ru/rest_api/contacts_set.php
         *
         * @param array $contacts
         *
         * @return array
         */
        public function apiAdd($contacts = [])
        {
            if (empty($contacts)) {
                $contacts = [$this];
            }

            $parameters = [
                'contacts' => [
                    'add' => [],
                ],
            ];

            foreach ($contacts AS $contact) {
                $parameters['contacts']['add'][] = $contact->getValues();
            }

            $response = $this->postRequest('/private/api/v2/json/contacts/set', $parameters);

            $result = [];
            if (isset($response['contacts']['add'])) {
                $result = array_map(function ($item) {
                    return $item['id'];
                }, $response['contacts']['add']);
            }

            return $result;
        }

        /**
         * Обновление контактов
         *
         * Метод позволяет добавлять контакты по одному или пакетно,
         * а также обновлять данные по уже существующим контактам.
         *
         * @link https://developers.amocrm.ru/rest_api/contacts_set.php
         *
         * @param array $contacts
         */
        public function apiUpdate($id, $modified = 'now')
        {
            $parameters = [
                'contacts' => [
                    'update' => [
                        array_merge(
                            ['id' => $id, 'last_modified' => strtotime($modified),],
                            $this->getValues()
                        ),
                    ],
                ],
            ];

            $response = $this->postRequest('/private/api/v2/json/contacts/set', $parameters);

            return isset($response['contacts']['contacts']) ? true : false;
        }
    }