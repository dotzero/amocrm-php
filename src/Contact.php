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

        public function apiList($parameters)
        {
            return $this->get('/private/api/v2/json/contacts/list', $parameters);
        }

        public function apiAdd($contacts = [])
        {
            // TODO
        }

        public function apiUpdate($id, $modified, $fields)
        {
            // TODO
        }
    }