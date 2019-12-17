<?php
require_once("class.db.php");
require_once("class.spieler.php");
require_once("class.team.php");
require_once("class.AbstractBaseClass.php");

class Spieler2Team extends AbstractBaseClass
{
	protected static $columns=array("id","spieler","team","von","bis");
    
    private $id;
	private $spieler;
	private $team;
	private $von;
	private $bis;
	private static $db;
	
	public function __construct($id=0)
	{
        $this->id=$id;
        if($id!=0)
        {
                load($id);
        }
	}
	public function load($id)
	{
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
		$stmt=static::$db->prepare("SELECT * FROM spieler2team WHERE id=:id");
		$stmt->bindValue(":id",$id);
		$error="";
		if($stmt->execute())
		{
            //TODO:$stmt->bindColumn();...
            $joincolumns=array();
            $joinarray=array();
            array_merge($joincolumns, Spieler2Team::getColumns("s2t"));
            array_merge($joincolumns, Spieler::getColumns("spieler"));
            array_merge($joincolumns,Team::getColumns("team"));
            for($i=1;$i<=count($joincolumns);$i++)
            {
                $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
            }
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);

			if($result)
			{
				$this->spieler=new Spieler();
                $this->spieler->setValues($joinarray["spielerid"],$joinarray["spielervorname"],
                                          $joinarray["spielernachname"],$joinarray["spielergeburtsdatum"]);
				$this->team=new Team(/*$joinarray["teamid"]*/);
                $this->team->setValues($joinarray["teamid"], $joinarray["teamname"]);
				$this->von=$joinarray["s2tvon"];
				$this->bis=$joinarray["s2tbis"];
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
        $insert="INSERT INTO spieler2team (id,teamid,spielerid,von,bis) VALUES (:id,:team,:spieler,:von,:bis)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE teamid=:team,spielerid=:spieler,von=:von,bis=:bis");	
        }
        else
        {
            $stmt=static::$db->prepare($insert);

        }

        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":team",$this->team->getId());
        $stmt->bindValue(":spieler",$this->spieler->getId());
        $stmt->bindValue(":von",$this->von);
        $stmt->bindValue(":bis",$this->bis);

        if(!$stmt->execute())
        {
            throw new Exception($stmt->errorInfo()[2]);
        }
    }
        public function getId()
	{
		return $this->id;
	}
        public function getSpieler()
	{
		return $this->spieler;
	}
	 public function getTeam()
	{
		return $this->team;
	}
	 public function getVon()
	{
		return $this->von;
	}
         public function getBis()
	{
		return $this->bis;
	}
	public function setId($id)
	{
		$this->id=$id;        
    }
    public function setTeam($team)
    {
    $this->team=$team;        
    }
    public function setSpieler($spieler)
    {
    $this->spieler=$spieler;        
    }
    public function setVon($von)
    {
    $this->von=$von;        
    }
    public function setBis($bis)
    {
    $this->bis=$bis;        
    }
    public function setValues($id,$team,$spieler,$von,$bis)
    {
        $this->setId($id);
        $this->setSpieler(new Spieler($spieler));
        $this->setTeam(new Team($team));
        $this->setVon($von);
        $this->setBis($bis);
    }

    public static function getAll()
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        if(static::$return_array==null)
        {
            $stmt=static::$db->prepare("SELECT s2t.*,t.*,sp.* FROM spieler2team LEFT JOIN team t ON s2t.teamid=t.id LEFT JOIN spieler sp ON s2t.spieler=sp.id");
            $error="";
            $return_array=array();
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();
                array_merge($joincolumns, Spieler2Team::getColumns("s2t"));
                array_merge($joincolumns, Spieler::getColumns("spieler"));
                array_merge($joincolumns,Team::getColumns("team"));

                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }

                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $team_temp=new Team();
                    $team_temp->setValues($result["teamid"], $result["teamname"]);
                    $spieler_temp=new Spieler();
                    $spieler_temp->setValues($result["spielerid"], $result["spielervorname"], $result["spielernachname"], $result["spielergeburtsdatum"]);
                    $s2t_temp=new Spieler2Team();
                    $s2t_temp->setValues($result["s2tid"],$spieler_temp,$team_temp,$result["s2tvon"],$result["s2tbis"]);
                    array_push($return_array,$s2t_temp);
                }

            }
        }
        return $return_array;
    }
    public static function getSpielerForDate($team1,$team2,$date)
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        $return_array=array();
        $stmt=static::$db->prepare("SELECT s2t.*,t.*,sp.* FROM spieler2team s2t ".
                                   "LEFT JOIN team t ON s2t.teamid=t.id ". 
                                   "LEFT JOIN spieler sp ON s2t.spieler=sp.id ".
                                   "WHERE s2t.von <= :date AND s2t.bis >=:date AND (s2t.teamid=:team1 OR s2t.teamid=:team2) ORDER BY s2t.teamid");
        $stmt->bindValue(":team1",$team1);
        $stmt->bindValue(":team2",$team2);
        $stmt->bindValue(":date",$date);
        
        if($stmt->execute())
        {
            
            $joincolumns= array();
            $joinarray= array();
            array_merge($joincolumns, Spieler2Team::getColumns("spieler2team"));
            array_merge($joincolumns, Team::getColumns("team"));
            array_merge($joincolumns, Spieler::getColumns("spieler"));
            for($i=1;$i<=count($joincolumns);$i++)
            {
                $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
            }
            while($result=$stmt->fetch(PDO::FETCH_BOUND))
            {
                $temp_element["spieler"]=new Spieler();
                $temp_element["team"]=new Team();
                $temp_element["spieler"]->setValues($joinarray["spielerid"], $joinarray["spielervorname"], 
                                                    $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                $temp_element["team"]->setValues($joinarray["teamid"], $joinarray["teamname"]);
                array_push($return_array,$temp_element);
            }
        }
        return $return_array;
    }
}
/*
$test_array=Spieler2Team::getAll();
$test_array[1]->getSpieler()->setVorname("asdf");
$test_array[1]->getSpieler()->update();

 */
