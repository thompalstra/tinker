<?php

namespace Hub\Helpers;

class ArrayHelper implements \Iterator
{

    protected $key = 0;
    protected $current = null;
    protected $valid = true;
    protected $array = [];

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function next()
    {
        $this->key++;
        if (isset($this->array[$this->key])) {
            $this->current = $this->array[$this->key];
        } else {
            $this->valid = false;
            $this->result = null;
        }
    }

    public function current()
    {
        return $this->current;
    }

    public function key()
    {
        return $this->key;
    }

    public function rewind()
    {
        $this->key = 0;
        $this->valid = true;
        $this->current = $this->array[$this->key];
    }

    public function valid()
    {
        return $this->valid;
    }

    public function all()
    {
        return $this->array;
    }

    public function filter($closure, $flag)
    {
        return array_filter($this->all(), $closure, $flag);
    }

    public function map($closure)
    {
        return array_map($closure, $this->all());
    }

    public function sort($flags = null)
    {
        $items = $this->all();
        if (!empty($flags)) {
            sort($items, $flags);
        }
        sort($items);
        return $items;
    }

    public function usort($closure)
    {
        $items = $this->all();
        usort($items, $closure);
        return $items;
    }

    public function uasort($closure)
    {
        $items = $this->all();
        uasort($items, $closure);
        return $items;
    }
}
