<?php

namespace Hub\Db;

use Hub\Helpers\ArrayHelper;

class Collection extends ArrayHelper implements \Iterator
{

    protected $key = 0;
    protected $current = null;
    protected $valid = true;
    protected $sth = null;

    public function __construct($sth)
    {
        $this->sth = $sth;
    }

    public function next()
    {
        $this->key++;
        $this->current = $this->sth->fetch();
        if ($this->current == false) {
            $this->valid = false;
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

        $this->sth->closeCursor();
        $this->sth->execute();
        $this->current = $this->sth->fetch();
        if ($this->current == false) {
            $this->valid = false;
        }
    }

    public function valid()
    {
        return $this->valid;
    }

    public function all()
    {
        $this->rewind();
        return $this->sth->fetchAll();
    }

    public function find($closure)
    {
        $out = [];
        foreach ($this->all() as $item) {
            if (call_user_func_array($closure, [$item])) {
                $out[] = $item;
            }
        }
        return $out;
    }
}
