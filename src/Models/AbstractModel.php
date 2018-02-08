<?php

namespace AmoCRM\Models;

use ArrayAccess;
use AmoCRM\Exception;
use AmoCRM\Helpers\Format;
use AmoCRM\Request\Request;

/**
 * Class AbstractModel
 *
 * Абстрактный класс для всех моделей
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class AbstractModel extends Request implements ArrayAccess, ModelInterface
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [];

    /**
     * @var array Список значений полей для модели
     */
    protected $values = [];

    /**
     * Возвращает называние Модели
     *
     * @return mixed
     */
    public function __toString()
    {
        return static::class;
    }

    /**
     * Определяет, существует ли заданное поле модели
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset Название поля для проверки
     * @return boolean Возвращает true или false
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * Возвращает заданное поле модели
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset Название поля для возврата
     * @return mixed Значение поля
     */
    public function offsetGet($offset)
    {
        if (isset($this->values[$offset])) {
            return $this->values[$offset];
        }

        return null;
    }

    /**
     * Устанавливает заданное поле модели
     *
     * Если есть сеттер модели, то будет использовать сеттер
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset Название поля, которому будет присваиваться значение
     * @param mixed $value Значение для присвоения
     */
    public function offsetSet($offset, $value)
    {
        $setter = 'set' . Format::camelCase($offset);

        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        } elseif (in_array($offset, $this->fields)) {
            $this->values[$offset] = $value;
        }
    }

    /**
     * Удаляет поле модели
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset Название поля для удаления
     */
    public function offsetUnset($offset)
    {
        if (isset($this->values[$offset])) {
            unset($this->values[$offset]);
        }
    }

    /**
     * Получение списка значений полей модели
     *
     * @return array Список значений полей модели
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Добавление кастомного поля модели
     *
     * @param int $id Уникальный идентификатор заполняемого дополнительного поля
     * @param mixed $value Значение заполняемого дополнительного поля
     * @param mixed $enum Тип дополнительного поля
     * @param mixed $subtype Тип подтипа поля
     * @return $this
     */
    public function addCustomField($id, $value, $enum = false, $subtype = false)
    {
        $field = [
            'id' => $id,
            'values' => [],
        ];

        if (!is_array($value)) {
            $values = [[$value, $enum]];
        } else {
            $values = $value;
        }

        foreach ($values as $val) {
            list($value, $enum) = $val;

            $fieldValue = [
                'value' => $value,
            ];

            if ($enum !== false) {
                $fieldValue['enum'] = $enum;
            }

            if ($subtype !== false) {
                $fieldValue['subtype'] = $subtype;
            }

            $field['values'][] = $fieldValue;
        }

        $this->values['custom_fields'][] = $field;

        return $this;
    }

    /**
     * Добавление кастомного поля типа мультиселект модели
     *
     * @param int $id Уникальный идентификатор заполняемого дополнительного поля
     * @param mixed $values Значения заполняемого дополнительного поля типа мультиселект
     * @return $this
     */
    public function addCustomMultiField($id, $values)
    {
        $field = [
            'id' => $id,
            'values' => [],
        ];

        if (!is_array($values)) {
            $values = [$values];
        }

        $field['values'] = $values;

        $this->values['custom_fields'][] = $field;

        return $this;
    }

    /**
     * Проверяет ID на валидность
     *
     * @param mixed $id ID
     * @return bool
     * @throws Exception
     */
    protected function checkId($id)
    {
        if (intval($id) != $id || $id < 1) {
            throw new Exception('Id must be integer and positive');
        }

        return true;
    }
}
