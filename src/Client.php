<?php

    namespace AmoCRM;

    use AmoCRM\Request\ParamsBag;

    class Client
    {
        private $parameters = null;

        public function __construct($domain, $login, $apikey)
        {
            $this->parameters = new ParamsBag();
            $this->parameters->addAuth('domain', $domain);
            $this->parameters->addAuth('login', $login);
            $this->parameters->addAuth('apikey', $apikey);
        }

        public function __get($name)
        {
            $classname = '\\AmoCRM\\Models\\' . ucfirst($name);

            if (!class_exists($classname)) {
                throw new ModelException('Model not exists: ' . $name);
            }

            // Чистим GET и POST от предыдущих вызовов
            $this->parameters->clearGet()->clearPost();

            return new $classname($this->parameters);
        }
    }