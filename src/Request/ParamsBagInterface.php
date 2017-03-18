<?php

namespace AmoCRM\Request;

/**
 * Interface ParamsBagInterface
 *
 * Базовый интерфейс класса для хранения аргументов
 *
 * @package AmoCRM\Request
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
interface ParamsBagInterface
{
    /**
     * Добавление значений параметров для авторизации
     *
     * @param string $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function addAuth($name, $value);

    /**
     * Получение параметра для авторизации по ключу или список параметров
     *
     * @param string $name Название параметра
     * @return array|null Значение параметра или список параметров
     */
    public function getAuth($name = null);

    /**
     * Добавление значений GET параметров
     *
     * @param string|array $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function addGet($name, $value = null);

    /**
     * Получение GET параметра по ключу или список параметров
     *
     * @param string $name Название параметра
     * @return array|null Значение параметра или список параметров
     */
    public function getGet($name = null);

    /**
     * Очистка всех GET параметров
     *
     * @return $this
     */
    public function clearGet();

    /**
     * Добавление значений POST параметров
     *
     * @param string|array $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function addPost($name, $value = null);

    /**
     * Получение POST параметра по ключу или список параметров
     *
     * @param string $name Название параметра
     * @return array|null Значение параметра или список параметров
     */
    public function getPost($name = null);

    /**
     * Очистка всех POST параметров
     *
     * @return $this
     */
    public function clearPost();
}