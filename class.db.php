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
}