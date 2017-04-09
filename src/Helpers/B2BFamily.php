<?php

namespace AmoCRM\Helpers;

use AmoCRM\Client;
use AmoCRM\Request\ParamsBag;

/**
 * Class B2BFamily
 *
 * Хелпер для отправки письма через B2BFamily с привязкой к сделке в amoCRM
 *
 * @package AmoCRM\Helpers
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class B2BFamily
{
    /**
     * @const string Тип HTTP запроса GET
     */
    const METHOD_GET = 'GET';

    /**
     * @const string Тип HTTP запроса POST
     */
    const METHOD_POST = 'POST';

    /**
     * @const string Тип HTTP запроса PUT
     */
    const METHOD_PUT = 'PUT';

    /**
     * @const string Тип HTTP запроса DELETE
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var ParamsBag|null Экземпляр ParamsBag для хранения аргументов
     */
    private $parameters = null;

    /**
     * @var null|string
     */
    private $apikey = null;

    /**
     * B2BFamily constructor.
     *
     * @param Client $client экземпляр класса Client
     * @param $appkey B2BFamily API appkey
     * @param $secret B2BFamily API secret
     * @param $email B2BFamily e-mail клиента
     * @param $password B2BFamily пароль клиента
     */
    public function __construct(Client $client, $appkey, $secret, $email, $password)
    {
        $this->parameters = $client->parameters;

        $this->parameters->addAuth('appkey', $appkey);
        $this->parameters->addAuth('secret', $secret);
        $this->parameters->addAuth('email', $email);
        $this->parameters->addAuth('password', $password);
        $this->parameters->addAuth('hash', $this->getHash());
    }

    /**
     * Авторизация в сервисе B2BFamily
     *
     * @return array
     */
    public function login()
    {
        $response = $this->request(self::METHOD_POST, '/user/login', [
            'appkey' => $this->parameters->getAuth('appkey'),
            'email' => $this->parameters->getAuth('email'),
            'password' => $this->parameters->getAuth('password'),
            'hash' => $this->parameters->getAuth('hash'),
        ]);

        if (isset($response['apikey'])) {
            $this->apikey = $response['apikey'];
        }

        return $response;
    }

    /**
     * Подписка amoCRM на получение Web Hook от B2BFamily
     *
     * @return array
     */
    public function subscribe()
    {
        if ($this->apikey === null) {
            $this->login();
        }

        return $this->request(self::METHOD_PUT, '/subscribers/add', [
            'apikey' => $this->apikey,
            'path' => 'http://b2bf.cloudapp.net/post/',
        ]);
    }

    /**
     * Отписка amoCRM на получение Web Hook от B2BFamily
     *
     * @param integer $id Номер подписки для отключения
     * @return mixed
     */
    public function unsubscribe($id)
    {
        if ($this->apikey === null) {
            $this->login();
        }

        return $this->request(self::METHOD_DELETE, '/subscribers/delete', [
            'apikey' => $this->apikey,
            'id' => $id,
        ]);
    }

    /**
     * Отправка письма через B2BFamily с привязкой к сделке в amoCRM
     *
     * @param integer $lead_id Номер сделки в amoCRM
     * @param array $params Список дополнительных параметров
     * @return mixed
     */
    public function mail($lead_id = 0, $params)
    {
        if ($this->apikey === null) {
            $this->login();
        }

        $parameters = array_merge($params, [
            'apikey' => $this->apikey,
            'notification_settings' => [
                'sms_enable' => false,
                'email_enable' => false,
                'webhook_enable' => true,
            ]
        ]);

        if ($lead_id !== 0) {
            $parameters['custom_data'] = [
                'userDomainAmo' => $this->parameters->getAuth('domain'),
                'userLoginAmo' => $this->parameters->getAuth('login'),
                'userHashAmo' => $this->parameters->getAuth('apikey'),
                'userTypeAmo' => 2,
                'userIdAmo' => $lead_id,
            ];
        }

        return $this->request(self::METHOD_POST, '/mail', $parameters);
    }

    /**
     * Выполнить HTTP запрос и вернуть тело ответа
     *
     * @param $method
     * @param string $url Запрашиваемый URL
     * @param array $parameters
     * @return mixed
     * @throws B2BFamilyException
     */
    protected function request($method, $url, $parameters = [])
    {
        $endpoint = 'https://api.b2bfamily.com' . $url;

        if (in_array($method, [self::METHOD_GET, self::METHOD_DELETE])) {
            $endpoint .= '?' . http_build_query($parameters, null, '&');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        if (in_array($method, [self::METHOD_POST, self::METHOD_PUT])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        }

        $result = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($error) {
            throw new B2BFamilyException($error, $errno);
        }

        $result = json_decode($result, true);

        if (isset($result['error'])) {
            throw new B2BFamilyException($result['error']['message'], $result['error']['code']);
        }

        return $result;
    }

    /**
     * Возвращает md5 хеш параметров для авторизации
     *
     * @return string
     */
    private function getHash()
    {
        return md5(implode('&', [
            'appkey=' . $this->parameters->getAuth('appkey'),
            'secret=' . $this->parameters->getAuth('secret'),
            'email=' . $this->parameters->getAuth('email'),
            'password=' . $this->parameters->getAuth('password'),
        ]));
    }
}
