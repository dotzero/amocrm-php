<?php

    namespace AmoCRM\Request;

    class ParamsBag
    {
        private $authParams = [];
        private $getParams  = [];
        private $postParams = [];

        public function addAuth($name, $value)
        {
            $this->authParams[$name] = $value;
        }

        public function getAuth($key = null)
        {
            if ($key !== null) {
                return isset($this->authParams[$key]) ? $this->authParams[$key] : null;
            }

            return $this->authParams;
        }

        public function addGet($name, $value = null)
        {
            if (is_array($name) AND $value === null) {
                $this->getParams = array_merge($this->getParams, $name);
            } else {
                $this->getParams[$name] = $value;
            }

            return $this;
        }

        public function getGet($key = null)
        {
            if ($key !== null) {
                return isset($this->getParams[$key]) ? $this->getParams[$key] : null;
            }

            return $this->getParams;
        }

        public function hasGet()
        {
            return count($this->getParams);
        }

        public function clearGet()
        {
            $this->getParams = [];
            
            return $this;
        }

        public function addPost($name, $value = null)
        {
            if (is_array($name) AND $value === null) {
                $this->postParams = array_merge($this->postParams, $name);
            } else {
                $this->postParams[$name] = $value;
            }

            return $this;
        }

        public function getPost($key = null)
        {
            if ($key !== null) {
                return isset($this->postParams[$key]) ? $this->postParams[$key] : null;
            }

            return $this->postParams;
        }

        public function hasPost()
        {
            return count($this->postParams);
        }

        public function clearPost()
        {
            $this->postParams = [];

            return $this;
        }
    }