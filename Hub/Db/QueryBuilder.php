<?php
namespace Hub\Db;

class QueryBuilder
{
    public static function build($parts)
    {
        $command = [];

        if(isset($parts["delete"])){
            $command[] = self::delete($parts["delete"]);
            unset($parts["delete"]);
        }

        if(isset($parts["select"])){
            $command[] = self::select($parts["select"]);
            unset($parts["select"]);
        }

        if(isset($parts["from"])){
            $command[] = self::from($parts["from"]);
            unset($parts["from"]);
        }

        if(isset($parts["update"])){
            $command[] = self::update($parts["update"]);
            unset($parts["update"]);
        }

        if(isset($parts["set"])){
            $command[] = self::set($parts["set"]);
            unset($parts["set"]);
        }

        foreach($parts as $type => $params){
            if(method_exists(self::class, $type)){
                $command[] = self::$type($params);
            } else {
                echo "not implemented {$type}"; exit;
            }
        }

        return implode(" ", $command);
    }

    public static function create_table($table)
    {
        return "CREATE TABLE {$table}";
    }

    public static function drop_table($table)
    {
        return "DROP TABLE {$table}";
    }

    public static function select($select)
    {
        return "SELECT {$select}";
    }

    public static function delete($delete)
    {
        return "DELETE FROM {$delete}";
    }

    public static function update($update)
    {
        return "UPDATE {$update}";
    }

    public static function from($from)
    {
        return "FROM {$from}";
    }

    public static function insert($tableName)
    {
        return "INSERT INTO {$tableName}";
    }

    public static function columns($columns)
    {
        $columns = implode(",", $columns);
        return "({$columns})";
    }

    public static function set($set)
    {
        $out = [];
        foreach($set as $params){
            $column = $params[0];
            $type = $params[1];
            $value = self::value($params[2]);
            $out[] = "{$column}{$type}{$value}";
        }
        return "SET " . implode(", ", $out);
    }

    public static function values($values)
    {
        $rows = [];

        foreach ($values as $row) {
            foreach ($row as $index => $value) {
                $row[$index] = self::value($value);
            }
            $rows[] = "(" . implode(",", $row) . ")";
        }

        return "VALUES " . implode(", ", $rows);
    }

    public static function where($wheres)
    {
        $out = [];
        foreach($wheres as $where){
            foreach($where as $type => $params){
                $out[] = self::whereGroup($type, $params);
            }
        }
        return implode(" ", $out);
    }

    public static function whereGroup($type, $params)
    {
        $out = [];
        foreach($params as $idx => $condition){
            if(count($condition) == 3){
                $out[] = (($idx == 0) ? "" : "AND ") . self::condition($condition[0], $condition[1], $condition[2]);
            } else if(in_array($condition[0], ['and', 'or'])){
                $out[] = self::whereGroup($condition[0], $condition[1]);
            }
        }
        return "{$type} (" . implode(" ", $out) . ")";
    }

    public static function limit($limit)
    {
        return "LIMIT {$limit}";
    }

    public static function offset($offset)
    {
        return "OFFSET {$offset}";
    }

    public static function order($order)
    {
        $order = implode(", ", $order);
        return "ORDER BY {$order}";
    }

    public static function condition($column, $type, $value)
    {
        $value = self::value($value);
        return "{$column} {$type} {$value}";
    }

    public static function value($value)
    {
        if (is_array($value)) {
            return "(" . implode(",", $value) . ")";
        } else if((is_string($value) && ($value == 'NULL') || $value === null)){
            return "NULL";
        } else if(is_numeric($value)){
            return $value;
        } else {
            $value = addslashes($value);
            return "'{$value}'";
        }
    }

}
