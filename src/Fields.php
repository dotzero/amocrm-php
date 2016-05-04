<?php

    namespace AmoCRM;

    class Fields implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        private $fields = [];

        public function __set($name, $value)
        {
            $this->offsetSet($name, $value);
        }

        public function __get($name)
        {
            return $this->offsetGet($name);
        }

        public function add($key, $value = null)
        {
            $this->offsetSet($key, $value);
        }

        public function get($key)
        {
            return $this->offsetGet($key);
        }
        
        /**
         * Whether a offset exists
         *
         * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
         * @param mixed $offset An offset to check for.
         * @return boolean true on success or false on failure.
         */
        public function offsetExists($offset)
        {
            return isset($this->fields[$offset]);
        }

        /**
         * Offset to retrieve
         *
         * @link  http://php.net/manual/en/arrayaccess.offsetget.php
         * @param mixed $offset The offset to retrieve.
         * @return mixed Can return all value types.
         */
        public function offsetGet($offset)
        {
            if (isset($this->fields[$offset])) {
                return $this->fields[$offset];
            }

            return null;
        }

        /**
         * Offset to set
         * 
         * @link  http://php.net/manual/en/arrayaccess.offsetset.php
         * @param mixed $offset The offset to assign the value to.
         * @param mixed $value  The value to set.
         * @return void
         */
        public function offsetSet($offset, $value)
        {
            $this->fields[$offset] = $value;
        }

        /**
         * Offset to unset
         * 
         * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
         * @param mixed $offset The offset to unset.
         * @return void
         */
        public function offsetUnset($offset)
        {
            if (isset($this->fields[$offset])) {
                unset($this->fields[$offset]);
            }
        }

        /**
         * Retrieve an external iterator
         * 
         * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
         * @return Traversable An instance of an object implementing Iterator or Traversable
         */
        public function getIterator()
        {
            return new \ArrayIterator($this->fields);
        }

        /**
         * Count elements of an object
         * 
         * @link  http://php.net/manual/en/countable.count.php
         * @return int The custom count as an integer.
         */
        public function count()
        {
            return count($this->fields);
        }
    }