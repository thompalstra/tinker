<?php

namespace Hub\Events;

class AfterCreate extends \Hub\Base\Event implements \Hub\Interfaces\EventInterface
{
    public function __construct($model)
    {
        $this->setModel($model);
    }
}
