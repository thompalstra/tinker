<?php
namespace Hub\Db;

use Hub\Base\Base;

class Record extends Base
{
    public $isNewRecord = true;

    protected $attributes = [];
    protected $old_attributes = [];

    protected $events = [];

    public function __construct($options = [], $isNewRecord = true)
    {
        $this->isNewRecord = $isNewRecord;

        $class = self::getClass();
        $columns = $class::$columns;
        foreach($columns as $columnName => $columnData){
            if(!property_exists($this, $columnName)){
                $this->$columnName = null;
            }
            $this->attributes[$columnName] = $this->old_attributes[$columnName] = $this->$columnName;
            $this->$columnName = &$this->attributes[$columnName];
        }

        $this->events = [
            \Hub\Events\BeforeSave::class => [],
            \Hub\Events\BeforeCreate::class => [],
            \Hub\Events\AfterCreate::class => [],
            \Hub\Events\AfterSave::class => [],
        ];
    }

    public function validate()
    {
        return true;
    }

    public function dispatch($eventModel)
    {
        if (isset($this->events[$eventModel::getClassName()])) {
            foreach ($this->events[$eventModel::getClassName()] as $method) {
                call_user_func_array($method, [$eventModel]);
            }
        }
    }

    public function save()
    {
        $this->dispatch(new \Hub\Events\BeforeSave($this));
        if($this->isNewRecord){
            $this->dispatch(new \Hub\Events\BeforeCreate($this));
            if ($this->insert()) {
                $this->dispatch(new \Hub\Events\AfterCreate($this));
                $this->dispatch(new \Hub\Events\AfterSave($this));

                return true;
            }
        } else {
            if ($this->update()) {
                $this->dispatch(new \Hub\Events\AfterSave($this));

                return true;
            }
        }

        return false;
    }

    public static function getClass()
    {
        return get_called_class();
    }

    public static function getShortClass()
    {
        $ex = explode("\\", get_called_class());
        return array_pop($ex);
    }

    public static function getTable()
    {
        $class = self::getClass();
        if(property_exists($class, 'table')){
            return $class::$table;
        }

        $shortClass = self::getShortClass();
        $x = strtolower(preg_replace('/\B([A-Z])/', '_$1', $shortClass));
        return "{$x}s";
    }

    public function insert()
    {
        $columns = [];
        $values = [];
        $class = self::getClass();

        foreach($class::$columns as $columnName => $columnData){
            if($columnName != 'id'){
                $set[] = $columnName;
                $where[] = $this->$columnName;
            }
        }

        return Query::insert($class, $set, [$where]);
    }

    public function update()
    {
        $set = [];
        $where = [];
        $class = self::getClass();

        foreach($class::$columns as $columnName => $columnData){
            if($columnName != 'id'){
                $set[] = [ $columnName, '=', $this->$columnName ];
            }
        }

        $where = [
            ['id', '=', $this->id]
        ];

        return Query::update($class, $set, $where);
    }

    public function delete()
    {
        $where = [];
        $class = self::getClass();

        $where = [
            ['id', '=', $this->id]
        ];

        return Query::delete($class, $where);
    }

    public function refresh()
    {
        $class = self::getClass();
        $where = [];
        foreach($class::$columns as $columnName => $columnData){
            if($columnName != 'id'){
                $where[] = [$columnName, '=', $this->$columnName];
            }
        }
        return self::findOne($where);
    }

    public static function find()
    {
        $class = self::getClass();
        $query = new Query($class);
        return $query->select($class::getTable() . '.*')->from($class::getTable());
    }

    public static function findOne($where)
    {
        return self::find()->where($where)->limit(1)->one();
    }

    public static function findAll()
    {
        return self::find()->where($where)->all();
    }
}
