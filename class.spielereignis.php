<?php
require_once("class.db.php");
require_once("class.spielereignis.php");
require_once("class.match.php");
require_once("class.AbstractBaseClass.php");

class Spielereignis extends AbstractBaseClass
{
	protected static $columns=array("id","spielereignis","minute","nachspielzeit","typ","match");
    private $id;
	private $spielereignis;
	private $minute;
    private $nachspielzeit;
	private $typ;
	private $match;
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
		$stmt=static::$db->prepare("SELECT * FROM spielereigniseignis WHERE id=:id");
            $stmt->bindValue(":id",$id);
            $error="";
            if($stmt->execute())
            {
            $joincolumns=array();
            $joinarray=array();
            array_merge($joincolumns, Spielereignis::getColumns("spielereigniseignis"));
            array_merge($joincolumns, Spieler::getColumns("spielereignis"));
            array_merge($joincolumns, Match::getColumns("spiel"));
            for($i=1;$i<=count($joincolumns);$i++)
            {
                $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
            }
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);
                if($result)
                {
                    $this->stadion=new Spieler();
                    $this->stadion->setValues($joinarray["spielereignisid"],$joinarray["spielereignisvorname"],$joinarray["spielereignisnachname"],$joinarray["spielereignisgeburtsdatum"]);
                    $this->match=new Match();
                    $this->match->setValues($joinarray["spielid"], $joinarray["spielteam1"], $joinarray["spielteam2"], $joinarray["spielzeitpunkt"], $joinarray["spielhalbzeit1"], $joinarray["spielhalbzeit2"],$joinarray["spielstadion"],$joinarray["spielzuschauzahl"]);
                    $this->von=$joinarray["typ"];
                    $this->bis=$joinarray["minute"];
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
             $insert="INSERT INTO spielereignisereignis (id,spielereignis,minute,typ,match) VALUES (:id,:team,:stadion,:von,:bis)";
		if($this->id != 0)
		{
			$stmt=static::$db->prepare("$insert
						ON DUPLICATE KEY
						UPDATE spielereignis=:spielereignis,minute=:minute,typ=:typ,match=:match");	
		}
		else
		{
			$stmt=static::$db->prepare($insert);
			
		}
                
        $stmt->bindValue(":id",$this->id);
		$stmt->bindValue(":spielereignis",$this->spielereignis->getId());
		$stmt->bindValue(":match",$this->match->getId());
        $stmt->bindValue(":minute",$this->minute);
		$stmt->bindValue(":typ",$this->typ);
        
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
		return $this->spielereignis;
	}
	 public function getMatch()
	{
		return $this->match;
	}
	 public function getMinute()
	{
		return $this->minute;
	}
    public function getNachspielzeit()
	{
		return $this->nachspielzeit;
	}
         public function getTyp()
	{
		return $this->typ;
	}
	public function setId($id)
	{
		$this->id=$id;        
        }
        public function setMatch($match)
	{
		$this->match=$match;        
        }
        public function setSpieler($spielereignis)
	{
		$this->spielereignis=$spielereignis;        
        }
        public function setMinute($minute)
	{
		$this->minute=$minute;        
        }
         public function setNachspielzeit($nachspielzeit)
	{
		$this->nachspielzeit=$nachspielzeit;        
        }
        public function setTyp($typ)
	{
		$this->typ=$typ;        
        }
	public function setValues($id,$match,$spielereignis,$minute,$nachspielzeit,$typ)
    {
        $this->setId($id);
        $this->setMatch($match);
        $this->setSpieler($spielereignis);
        $this->setMinute($minute);
        $this->setNachspielzeit($nachspielzeit);
        $this->setTyp($typ);
    }
    public static function getAll()
    {   
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        if(static::$return_array==null)
        {
            $stmt=static::$db->prepare("SELECT m.*,sp.*,se.* FROM spielereigniseignis se LEFT JOIN match m ON m.id=se.matchid LEFT JOIN spielereignis sp ON sp.id=se.spielereignisid");
            $error="";
            static::$return_array=array();
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();
                $joincolumns=array_merge($joincolumns, Match::getColumns("match"));
                $joincolumns=array_merge($joincolumns, Spieler::getColumns("spieler"));
                $joincolumns=array_merge($joincolumns, Spielereignis::getColumns("spielereignis"));
   
                print_r($joincolumns);
                print_r(static::getColumns("spielereignis"));
                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {             
                    $match_temp=new Match();
                    $match_temp->setValues($joinarray["matchid"], $joinarray["matchteam1id"], $joinarray["matchteam2id"], $joinarray["matchzeitpunkt"], $joinarray["matchhalbzeit1"], $joinarray["matchhalbzeit2"], $joinarray["matchstadionid"], $joinarray["matchzuschauzahl"], $joinarray["matchendstand1"], $joinarray["matchendstand2"]);
                    $spieler_temp=new Spieler();
                    $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                    $spielereignis_temp=new Spielereignis();
                    $spielereignis_tempemp->setValues($joinarray["spielereignisid"], $match_temp, $spieler_temp, $joinarray["spielereignisminute"],$joinarray["spielereignisnachspielzeit"], $joinarray["spielereignistyp"]);

                    array_push(static::$return_array,$spielereignis_temp);
                }

            }
        }
        return static::$return_array;
    }

}
