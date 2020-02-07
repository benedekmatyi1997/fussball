<?php


class DB
{
    private static $db;
    
    public static function getDB()
    {
        if(!isset(self::$db))
        {
            require_once("dbconfig.inc.php");
            self::$db = new PDO("mysql:dbname=$dbname;host=$host;charset=$charset", $username,$password);
        }
        return self::$db;
    }
    public static function execute(PDOStatement $stmt)
    {
        $return_code=$stmt->execute();
        if(!$return_code)
        {
            throw new Exception($stmt->errorInfo()[2]);
        }
        return $return_code;
    }
}