<?php
require_once("class.db.php");
abstract class AbstractBaseClass {
    protected static $columns=array();
    protected static $tablename;
    protected static $all_elements;
    protected static $db;
    
    abstract public function load($id);
    abstract public function update();
    abstract public static function getAll();
    public static function getColumns($prefix="")
    {
        if(!count(static::$columns))
        {
            throw new Exception("Du hast vergessen das in der Klasse zu &auml;ndern!");
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
    public function getAsArray($prefix="")
    {
        if(!count(static::$columns))
        {
            throw new Exception("Du hast vergessen das in der Klasse zu &auml;ndern!");
        }
        $content_array=array();
        foreach(static::$columns as $column)
        {
            $method_name="get".ucfirst($column);
            if($this->$method_name() instanceof AbstractBaseClass)
            {
                $content_array=array_merge($content_array, $this->$method_name()->getAsArray($prefix.$column));
            }
            else
            {
                $content_array[$prefix.$column]=$this->$method_name();
            }
        }
        
        return $content_array;
    }
    protected static function initDB()
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
    }
    protected static function getJoinArray(PDOStatement $stmt,array $joincolumns)
    {
        $joinarray=array();
        for($i=1;$i<=count($joincolumns);$i++)
        {
            $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
        }
        return $joinarray;
    }    
}
