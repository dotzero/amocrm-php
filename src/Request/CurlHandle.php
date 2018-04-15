<?php

namespace AmoCRM\Request;

use AmoCRM\NetworkException;

/**
 * Class CurlHandle
 *
 * Класс, хранящий повторно используемый обработчик cURL
 *
 * @package AmoCRM\Request
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CurlHandle
{
    /**
     * @var resource Повторно используемый обработчик cURL
     */
    private $handle;

    /**
     * Закрывает обработчик cURL
     */
    public function __destruct()
    {
        if ($this->handle !== null) {
            @curl_close($this->handle);
        }
    }

    /**
     * Возвращает повторно используемый обработчик cURL или создает новый
     *
     * @return resource
     * @throws NetworkException
     */
    public function open()
    {
        if ($this->handle !== null) {
            return $this->handle;
        }

        if (!function_exists('curl_init')) {
            throw new NetworkException('The cURL PHP extension was not loaded.');
        }
        $this->handle = curl_init();

        return $this->handle;
    }

    /**
     * Сбрасывает настройки обработчика cURL
     */
    public function close()
    {
        if ($this->handle === null) {
            return;
        }

        curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, null);
        curl_setopt($this->handle, CURLOPT_READFUNCTION, null);
        curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, null);
        curl_setopt($this->handle, CURLOPT_PROGRESSFUNCTION, null);
        curl_reset($this->handle);
    }
}
