<?php
namespace Hub\Db;

use PDO;
use Exception;

use Frame;

use Hub\Base\Base;


class Query extends Base
{
    protected $result = false;

    public function __construct($class = null)
    {
        if(isset($class) && class_exists($class)){
            $this->setClassName($class);
            $this->setFetchMode(PDO::FETCH_CLASS);
        }
    }

    protected $parts = [
        "where" => []
    ];

    public static function insert($class, $columns, $values)
    {
        $query = new self($class);
        $query->_insert($class::getTable());
        $query->columns($columns);
        $query->values($values);

        $sth = $query->execute($query->createCommand(), null, null);
        return $sth->rowCount() ? true : false;
    }

    public static function update($class, $set, $where)
    {
        $query = new self($class);
        $query->_update($class::getTable());
        $query->set($set);
        $query->where($where);

        if($class){
            $sth = $query->execute($query->createCommand(), null, null);
        }

        return $sth->rowCount() ? true : false;
    }

    public static function delete($class, $where)
    {
        $query = new self($class);
        $query->_delete($class::getTable());
        $query->where($where);
        $sth = $query->execute($query->createCommand(), null, null);
        return $sth->rowCount() ? true : false;
    }

    public static function createTable($tableName, $columns)
    {
        $query = new self();
        $query->parts['create_table'] = $tableName;
        $col = [];
        foreach($columns as $columnName => $columnData){
            $col[] = "{$columnName} {$columnData}";
        }
        $query->columns($col);
        $sth = $query->execute($query->createCommand(), null, null);
        return $query->result;
    }

    public static function dropTable($tableName)
    {
        $query = new self();
        $query->parts['drop_table'] = $tableName;
        $sth = $query->execute($query->createCommand(), null, null);
        return $query->result;
    }

    public static function hasTable($tableName)
    {
        $query = new Query();
        return $query->select('*')
            ->from('information_schema.tables')
            ->where([
                ['table_schema', '=', Frame::$app->ini->mysql['dbname']],
                ['table_name', '=', $tableName]
            ])->exists();
    }

    public function _delete($delete)
    {
        $this->parts["delete"] = $delete;
        return $this;
    }

    public function select($select)
    {
        $this->parts["select"] = $select;
        return $this;
    }

    public function from($from)
    {
        $this->parts["from"] = $from;
        return $this;
    }

    public function where($where)
    {
        $this->parts["where"][] = ["where" => $where];
        return $this;
    }

    public function orWhere($where)
    {
        $this->parts["where"][] = ["or" => $where];
        return $this;
    }

    public function andWhere($where)
    {
        $this->parts["where"][] = ["and" => $where];
        return $this;
    }

    public function _insert($tableName)
    {
        $this->parts["insert"] = $tableName;
        return $this;
    }

    public function _update($tableName)
    {
        $this->parts["update"] = $tableName;
        return $this;
    }

    public function columns($columns)
    {
        $this->parts["columns"] = $columns;
        return $this;
    }

    public function values($values)
    {
        $this->parts["values"] = $values;
        return $this;
    }

    public function set($set)
    {
        $this->parts["set"] = $set;
    }

    public function limit($limit)
    {
        $this->parts["limit"] = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->parts["offset"] = $offset;
        return $this;
    }

    public function order($order)
    {
        $this->parts["order"] = $order;
        return $this;
    }

    public function group($group)
    {
        $this->parts["group"] = $group;
        return $this;
    }

    public function one()
    {
        return $this->execute($this->createCommand(), $this->getFetchMode(), "fetch");
    }

    public function all()
    {
        return $this->execute($this->createCommand(), $this->getFetchMode(), "fetchAll");
    }

    public function each()
    {
        $sth = Frame::$app->db->prepare($this->createCommand(), [
            \PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL
        ]);
        if($this->getFetchMode()){
            $sth->setFetchMode($this->getFetchMode(), $this->getClassName(), [[], false]);
        }
        $sth->execute();
        return new Collection($sth);
    }

    public function count()
    {
        return $this->execute($this->createCommand(), null, null)->rowCount();
    }

    public function exists()
    {
        $this->limit(1);
        return $this->execute($this->createCommand(), null, null)->rowCount() > 0;
    }

    public function createCommand()
    {
        return QueryBuilder::build($this->parts);
    }

    public function lastInsertId()
    {
        return Frame::$app->db->lastInsertId();
    }

    public function execute($command, $fetchMode = null, $fetchMethod = null)
    {
        $sth = Frame::$app->db->prepare($command);
        $class = $this->getClassName();
        $result = null;

        if($fetchMode){
            $sth->setFetchMode($fetchMode, $this->getClassName(), [[], false]);
        }
        try {
            $result = $sth->execute();
        } catch (Exception $e) {
            switch($e->getCode()) {
                case "42S02":
                    if ($class) {
                        if (method_exists($class, 'getTable') && property_exists($class, 'columns')) {
                            if (self::createTable($class::getTable(), $class::$columns)) {
                                $result = $sth->execute();
                                if (method_exists($class, 'seed')) {
                                    $seed = $class::seed();
                                    self::insert($class, $seed[0], $seed[1]);
                                }
                            } else {
                            }
                        }
                    }
                break;
            }
        }

        $this->result = $result;

        if($fetchMethod){
            return $sth->$fetchMethod();
        }
        return $sth;
    }
}
