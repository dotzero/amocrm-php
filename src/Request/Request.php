<?php

    namespace AmoCRM\Request;

    class Request
    {
        private $debug = false;

        private $parameters = null;

        public function __construct(ParamsBag $parameters)
        {
            if (!function_exists('curl_init')) {
                throw new NetworkException('The cURL PHP extension was not loaded');
            }

            $this->parameters = $parameters;
        }

        public function debug($flag = false)
        {
            $this->debug = (bool)$flag;
        }

        protected function getRequest($url, $parameters = [], $modified = null)
        {
            if ($parameters) {
                $this->parameters->addGet($parameters);
            }

            return $this->request($url, $modified);
        }

        protected function postRequest($url, $parameters = [])
        {
            if ($parameters) {
                $this->parameters->addPost($parameters);
            }

            return $this->request($url);
        }

        private function request($url, $modified = null)
        {
            $headers = ['Content-Type: application/json'];

            if ($modified !== null) {
                $headers[] = 'IF-MODIFIED-SINCE: ' . (new \DateTime($modified))->format(\DateTime::RFC1123);
            }

            $query = http_build_query(array_merge($this->parameters->getGet(), [
                'USER_LOGIN' => $this->parameters->getAuth('login'),
                'USER_HASH'  => $this->parameters->getAuth('apikey'),
            ]));

            $endpoint = sprintf('https://%s.amocrm.ru%s?%s', $this->parameters->getAuth('domain'), $url, $query);

            if ($this->debug) {
                printf('[DEBUG] url: %s' . PHP_EOL, $endpoint);
                printf('[DEBUG] headers: %s' . PHP_EOL, print_r($headers, 1));
                if ($this->parameters->hasPost()) {
                    printf('[DEBUG] post: %s' . PHP_EOL, json_encode([
                        'request' => $this->parameters->getPost(),
                    ]));
                }
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            if ($this->parameters->hasPost()) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'request' => $this->parameters->getPost(),
                ]));
            }

            $result = curl_exec($ch);
            $info   = curl_getinfo($ch);
            $error  = curl_error($ch);
            $errno  = curl_errno($ch);

            curl_close($ch);

            if ($this->debug) {
                printf('[DEBUG] curl_exec: %s' . PHP_EOL, $result);
                printf('[DEBUG] curl_getinfo: %s' . PHP_EOL, print_r($info, 1));
                printf('[DEBUG] curl_error: %s' . PHP_EOL, $error);
                printf('[DEBUG] curl_errno: %s' . PHP_EOL, $errno);
            }

            if ($error) {
                throw new NetworkException($error, $errno);
            }

            $result = json_decode($result, true);

            if (!isset($result['response'])) {
                return false;
            } elseif (floor($info['http_code'] / 100) >= 3) {
                throw new Exception($result['response']['error'], $result['response']['error_code']);
            }

            return $result['response'];
        }
    }