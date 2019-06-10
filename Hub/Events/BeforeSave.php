<?php

namespace Hub\Events;

class BeforeSave extends \Hub\Base\Event implements \Hub\Interfaces\EventInterface
{
    public function __construct($model)
    {
        $this->setModel($model);

        if (property_exists($model, "timestamps")) {
            if (empty($model->created_at)) {
                $model->created_at = time();
            }
            $model->updated_at = time();
        }
    }
}
