<?php

namespace AmoCRM\Helpers;

/**
 * Class Fields
 *
 * Хелпер для хранения идентификаторов полей amoCRM API
 *
 * @package AmoCRM\Helpers
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Fields implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var array Массив ключей и идентификаторов полей
     */
    private $fields = [];

    /**
     * Магический сеттер для поля
     *
     * @param mixed $name Название поля
     * @param mixed $value Значение поля
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Магический геттер для поля
     *
     * @param mixed $name Название поля
     * @return mixed Значение поля
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Сеттер для поля
     *
     * @param mixed $key Название поля
     * @param mixed $value Значение поля
     */
    public function add($key, $value = null)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Геттер для поля
     *
     * @param mixed $key Название поля
     * @return mixed Значение поля
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Определяет, существует ли заданное смещение (ключ)
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset Смещение (ключ) для проверки
     * @return boolean Возвращает true или false
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * Возвращает заданное смещение (ключ)
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset Смещение (ключ) для возврата
     * @return mixed Значение смещения (ключа)
     */
    public function offsetGet($offset)
    {
        if (isset($this->fields[$offset])) {
            return $this->fields[$offset];
        }

        return null;
    }

    /**
     * Устанавливает заданное смещение (ключ)
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset Смещение (ключ), которому будет присваиваться значение
     * @param mixed $value Значение для присвоения
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * Удаляет смещение
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset Смещение для удаления
     */
    public function offsetUnset($offset)
    {
        if (isset($this->fields[$offset])) {
            unset($this->fields[$offset]);
        }
    }

    /**
     * Возвращает внешний итератор
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable Экземпляр объекта, использующего Iterator или Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * Количество элементов объекта
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int Количество элементов объекта
     */
    public function count()
    {
        return count($this->fields);
    }
}
