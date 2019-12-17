<?php

abstract class AbstractBaseClass {
    protected static $columns=array();
    protected static $tablename;
    
    abstract public function load($id);
    abstract public function update();
    abstract public static function getAll();
    public static function getColumns($prefix="")
    {
        if(!count(static::$columns))
        {
            echo("Du hast vergessen das in der Klasse zu ändern!");
        }
        if(!strlen($prefix))
        {
            return static::$columns;
        }
        $columns_with_prefix=array();
        foreach(static::$columns as $column)
        {
            $columns_with_prefix[]=$prefix.$column;
        }
        return $columns_with_prefix;
    }
    
//put your code here
}
