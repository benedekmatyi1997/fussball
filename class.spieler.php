<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
require_once("class.AbstractBaseClass.php");

class Spieler extends AbstractBaseClass
{
    protected static $columns=array("id","vorname","nachname","geburtsdatum");
	private $id;
	private $vorname;
	private $nachname;
	private $geburtsdatum;
	private static $db;
    protected static $return_array;
	public function __construct($id=0)
	{
        $this->id=$id;
        if($id!=0)
        {
            $this->load($id);
        }
	}
	public function load($id)
	{
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
		$stmt=static::$db->prepare("SELECT * FROM spieler WHERE id=:id");
		$stmt->bindValue(":id",$id);
		$error="";
		if($stmt->execute())
		{
			$result=$stmt->fetch();
			if($result)
			{
				$this->vorname=$result["vorname"];
				$this->nachname=$result["nachname"];
				$this->geburtsdatum=$result["geburtsdatum"];
			}
			else
			{
				$error.="Leeres Resultat";
			}
		}
		else
		{
			$error.=$stmt->errorInfo()[2];
		}
		if(strlen($error))
		{
			throw new Exception($error);
		}
	}
	public function update()
	{
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
		$insert="INSERT INTO spieler (id,vorname,nachname,geburtsdatum) VALUES (:id,:vorname,:nachname,:geburtsdatum)";
		if($this->id != 0)
		{
			$stmt=static::$db->prepare("$insert
						ON DUPLICATE KEY
						UPDATE vorname=:vorname ,nachname=:nachname ,geburtsdatum=:geburtsdatum");
			
		}
		else
		{
			$stmt=static::$db->prepare($insert);
			
		}
                
        $stmt->bindValue(":id",$this->id);
		$stmt->bindValue(":vorname",$this->vorname);
		$stmt->bindValue(":nachname",$this->nachname);
		$stmt->bindValue(":geburtsdatum",$this->geburtsdatum);
                
		if(!$stmt->execute())
		{
			throw new Exception($stmt->errorInfo()[2]);
		}		
	}
	public function get_as_array()
	{
		return array("Id" => $this->getId(),
					 "vorname" => $this->getVorname(),
					 "nachname" => $this->getNachname(),
					 "geburtsdatum" => $this->getGeburtsdatum());
	}
    public function setValues($id,$vorname,$nachname,$geburtsdatum)
    {
        $this->setId($id);
        $this->setVorname($vorname);
        $this->setNachname($nachname);
        $this->setGeburtsdatum($geburtsdatum);
    }
	public function getId()
	{
		return $this->id;
	}
	public function getVorname()
	{
		return $this->vorname;
	}
	public function getNachname()
	{
		return $this->nachname;
	}
	public function getGeburtsdatum()
	{
		return $this->geburtsdatum;
	}
        
    public function setId($id)
	{
		$this->id=$id;
	}
    public function setVorname($vorname)
	{
        if(strlen($vorname))
        {
            $this->vorname=$vorname;
        }
	}
	public function setNachname($nachname)
	{
		$this->nachname=$nachname;
	}
	public function setGeburtsdatum($geburtsdatum)
	{
		$this->geburtsdatum=$geburtsdatum;
	}
    public function getName()
    {
        return $this->vorname." ".$this->nachname;
    }

    public static function getAll()
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        if(static::$return_array==null)
        {
            $stmt=static::$db->prepare("SELECT * FROM spieler");
            $error="";
            static::$return_array=array();
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();
                
                $joincolumns=array_merge($joincolumns, Spieler::getColumns("spieler"));
   
                print_r($joincolumns);
                print_r(static::getColumns("spieler"));
                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $spieler_temp=new Spieler();
                    print_r($joinarray);
                    $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                    echo($spieler_temp->getId()." ".$spieler_temp->getName()." ");

                    array_push(static::$return_array,$spieler_temp);
                }

            }
        }
        foreach(static::$return_array as $spieler_temp)
        {
            echo($spieler_temp->getId()." ".$spieler_temp->getName()." ");
        }
        return static::$return_array;
    }
}
