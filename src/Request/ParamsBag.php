<?php

namespace AmoCRM\Request;

/**
 * Class ParamsBag
 *
 * Класс для хранения аргументов
 *
 * @package AmoCRM\Request
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ParamsBag
{
    /**
     * @var array Список значений параметров для авторизации
     */
    private $authParams = [];

    /**
     * @var array Список значений GET параметров
     */
    private $getParams = [];

    /**
     * @var array Список значений POST параметров
     */
    private $postParams = [];

    /**
     * @var string|null Прокси сервер для отправки запроса
     */
    private $proxy = null;

    /**
     * Добавление прокси сервера
     *
     * @param string $proxy Прокси сервер для отправки запроса
     * @see http://php.net/manual/ru/function.curl-setopt.php
     * @return $this
     */
    public function addProxy($proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Добавление значений параметров для авторизации
     *
     * @param string $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function addAuth($name, $value)
    {
        $this->authParams[$name] = $value;

        return $this;
    }

    /**
     * Получение параметра для авторизации по ключу или список параметров
     *
     * @param string $name Название параметра
     * @return array|null Значение параметра или список параметров
     */
    public function getAuth($name = null)
    {
        if ($name !== null) {
            return isset($this->authParams[$name]) ? $this->authParams[$name] : null;
        }

        return $this->authParams;
    }

    /**
     * Добавление значений GET параметров
     *
     * @param string|array $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function addGet($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            $this->getParams = array_merge($this->getParams, $name);
        } else {
            $this->getParams[$name] = $value;
        }

        return $this;
    }

    /**
     * Получение GET параметра по ключу или список параметров
     *
     * @param string $name Название параметра
     * @return array|null Значение параметра или список параметров
     */
    public function getGet($name = null)
    {
        if ($name !== null) {
            return isset($this->getParams[$name]) ? $this->getParams[$name] : null;
        }

        return $this->getParams;
    }

    /**
     * Получение количества GET параметров
     *
     * @return int количество GET параметров
     */
    public function hasGet()
    {
        return count($this->getParams) ? true : false;
    }

    /**
     * Очистка всех GET параметров
     *
     * @return $this
     */
    public function clearGet()
    {
        $this->getParams = [];

        return $this;
    }

    /**
     * Добавление значений POST параметров
     *
     * @param string|array $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function addPost($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            $this->postParams = array_merge($this->postParams, $name);
        } else {
            $this->postParams[$name] = $value;
        }

        return $this;
    }

    /**
     * Получение POST параметра по ключу или список параметров
     *
     * @param string $name Название параметра
     * @return array|null Значение параметра или список параметров
     */
    public function getPost($name = null)
    {
        if ($name !== null) {
            return isset($this->postParams[$name]) ? $this->postParams[$name] : null;
        }

        return $this->postParams;
    }

    /**
     * Получение количества POST параметров
     *
     * @return int количество POST параметров
     */
    public function hasPost()
    {
        return count($this->postParams) ? true : false;
    }

    /**
     * Очистка всех POST параметров
     *
     * @return $this
     */
    public function clearPost()
    {
        $this->postParams = [];

        return $this;
    }

    /**
     * Получить прокси сервер для отправки запроса
     *
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Получение возможности использования прокси сервера
     *
     * @return bool
     */
    public function hasProxy()
    {
        return is_string($this->proxy);
    }
}
