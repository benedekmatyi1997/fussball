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
    public static function getDataByValue($columns,$values)
    {
        if(!strlen(static::$tablename))
        {
            throw new Exception("Tabellenname nicht gesetzt");
        }
        static::initDB();
        $tablename= static::$tablename;
        $return_value=array();
        //$stmt="";
        $sql="SELECT * FROM $tablename WHERE ";
        if(is_array($columns) && is_array($values) && count($columns)==count($values))
        {
            
            $count_cols=count($columns);
            for($i=0;$i<$count_cols;$i++)
            {
                if(!in_array($columns[$i], static::$columns))
                {
                    throw new Exception("Falsche Spalte bei Index $i!");
                }
                
                $sql.=$columns[$i]."= :".$columns[$i];
                
                if(($count_cols-$i) > 1)
                {
                    $sql.=" AND ";
                }
            }
            
            $stmt= static::$db->prepare($sql);
            
            for($i=0;$i<$count_cols;$i++)
            {
                $stmt->bindValue(":".$columns[$i],$values[$i]);
            }
        }
        else if(!is_array($columns) && !is_array($values) && strlen($columns)) 
        {
            if(!in_array($columns, static::$columns))
            {
                throw new Exception("Falsche Spalte!");
            }
            $sql.=$columns."=:".$columns;
            $stmt= static::$db->prepare($sql);
            $stmt->bindValue(":".$columns,$values);
        }
        
        if(isset($stmt) && $stmt instanceof PDOStatement && DB::execute($stmt))
        {
            foreach($stmt->fetchAll(PDO::FETCH_NUM) as $result)
            {   
                
                //print_r($result);
                $classname=static::class;
                $temp_object=new $classname();
                $count_cols=count($result);
                for($i=0;$i<$count_cols;$i++)
                {
                    if(!isset(static::$columns[$i]))
                    {
                        throw new Exception("Too many columns. SQL Statement: $sql".PHP_EOL."Count Result:".count($result)." Count Columns:".count($classname::$columns)." I:".$i.PHP_EOL. print_r($result,false));
                    }
                    $method_name="set".ucfirst(static::$columns[$i]);
                    $temp_object->$method_name($result[$i]);
                }
                array_push($return_value,$temp_object);
            }
        }
        
        return $return_value;
    }
}
