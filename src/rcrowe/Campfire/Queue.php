<?php

/**
 * PHP library for 37Signals Campfire. Designed for incidental notifications from an application.
 *
 * @author Rob Crowe <rob@vocabexpress.com>
 * @copyright Copyright (c) 2012, Alpha Initiatives Ltd.
 * @license MIT
 */

namespace rcrowe\Campfire;

/**
 * Holds a list of messages to send until they are sent.
 */
class Queue implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * The queue it's self. Just a simple array.
     *
     * @var array
     */
    protected $container = array();

    /**
     * Add an item to the queue.
     *
     * The benefit of using the add function over using the queue as an
     * array is that you are given back an index of the new item in the queue.
     * This index allows you to remove it at a later stage if you choose.
     *
     * @see rcrowe\Campfire\Queue::offsetSet()
     *
     * @param string|object $item String or an object that contains the method __toString().
     * @return int Index of the new item in the queue.
     *
     * @throws InvalidArgumentException Thrown when the argument isn't a string.
     */
    public function add($item)
    {
        if (is_string($item)) {

            $msg = $item;

        } elseif (is_object($item)) {

            if (!method_exists($item, '__toString')) {
                throw new \InvalidArgumentException('Object can not be converted to a string');
            }

            $msg = (string)$item;

        } else {
            $msg = null;
        }

        if (!is_string($msg) OR strlen($msg) === 0) {
            throw new \InvalidArgumentException('Can only add a string to the queue');
        }

        // Get the next index in the container that we can insert into
        if (count($this->container) > 0) {
            $keys  = array_keys($this->container);
            $index = ($keys[ count($keys) - 1 ]) + 1;
        } else {
            $index = 0;
        }

        $this->container[$index] = $msg;

        return $index;
    }

    public function remove($index = null)
    {
        if ($index !== null) {

            if (!$this->offsetExists($index)) {
                throw new \OutOfRangeException('Unknown index: '.$index);
            }

            unset($this->container[$index]);
        } else {
            $this->container = array();
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->add($value);
        } else {

            if ($this->offsetExists($offset)) {
                $this->remove($offset);
            }

            $this->add($value);
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset)) ? $this->container[$offset] : null;
    }

    public function rewind()
    {
        reset($this->container);
    }

    public function current()
    {
        return current($this->container);
    }

    public function key()
    {
        return key($this->container);
    }

    public function next()
    {
        return next($this->container);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function count()
    {
        return count($this->container);
    }
}
