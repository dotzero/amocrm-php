<?php

    namespace AmoCRM\Models;
    
    use AmoCRM\Request\Request;

    class Base extends Request implements \ArrayAccess
    {
        protected $fields = [];

        protected $values = [];

        /**
         * Whether a offset exists
         *
         * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
         *
         * @param mixed $offset An offset to check for.
         *
         * @return boolean true on success or false on failure.
         */
        public function offsetExists($offset)
        {
            return isset($this->values[$offset]);
        }

        /**
         * Offset to retrieve
         *
         * @link  http://php.net/manual/en/arrayaccess.offsetget.php
         *
         * @param mixed $offset The offset to retrieve.
         *
         * @return mixed Can return all value types.
         */
        public function offsetGet($offset)
        {
            if (isset($this->values[$offset])) {
                return $this->values[$offset];
            }

            return null;
        }

        /**
         * Offset to set
         *
         * @link  http://php.net/manual/en/arrayaccess.offsetset.php
         *
         * @param mixed $offset The offset to assign the value to.
         * @param mixed $value  The value to set.
         *
         * @return void
         */
        public function offsetSet($offset, $value)
        {
            $setter = 'set' . $this->toCamelCase($offset);

            if (method_exists($this, $setter)) {
                return $this->$setter($value);
            } elseif (in_array($offset, $this->fields)) {
                $this->values[$offset] = $value;
            }
        }

        /**
         * Offset to unset
         *
         * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
         *
         * @param mixed $offset The offset to unset.
         *
         * @return void
         */
        public function offsetUnset($offset)
        {
            if (isset($this->values[$offset])) {
                unset($this->values[$offset]);
            }
        }
        
        public function getValues()
        {
            return $this->values;
        }

        public function addCustomField($id, $value, $enum = false)
        {
            $field = [
                'id'     => $id,
                'values' => [],
            ];

            if (!is_array($value)) {
                $values = [[$value, $enum]];
            } else {
                $values = $value;
            }

            foreach ($values AS $val) {
                list($value, $enum) = $val;

                $fieldValue = [
                    'value' => $value,
                ];

                if ($enum !== false) {
                    $fieldValue['enum'] = $enum;
                }

                $field['values'][] = $fieldValue;
            }

            $this->values['custom_fields'][] = $field;

            return $this;
        }

        private function toCamelCase($string)
        {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        }
    }