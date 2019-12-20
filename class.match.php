<?php
require_once("class.db.php");
require_once("class.stadion.php");
require_once("class.team.php");
require_once("class.AbstractBaseClass.php");

class Match extends AbstractBaseClass 
{
    protected static $columns=array("id","team1","team2","zeitpunkt","halbzeit1","halbzeit2","stadion","zuschauzahl","endstand1","endstand2");

    private $id;
    private $team1;
    private $team2;
    private $zeitpunkt;
    private $halbzeit1;
    private $halbzeit2;
    private $stadion;
    private $zuschauzahl;   //TODO: ändern auf zuschauERzahl
    private static $db;
    private static $return_array;


    public function __construct($id=0)
    {
        $this->id=$id;
        if($id!=0)
        {
            $this->load($id);
        }
    }
    public function update()
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        $insert="INSERT INTO spiel (id,team1,team2,zeitpunkt,halbzeit1,halbzeit2,stadion,zuschauzahl) VALUES (:id,:team1,:team2,:zeitpunkt,:halbzeit1,:halbzeit2,:stadion,:zuschauzahl)";
		if($this->id != 0)
		{
			$stmt=static::$db->prepare("$insert
						ON DUPLICATE KEY
						UPDATE team1=:team1,team2=:team2,zeitpunkt=:zeitpunkt,".
                        "halbzeit1=:halbzeit1,halbzeit2=:halbzeit2,stadion=:stadion,zuschauzahl=:zuschauzahl");
			
		}
		else
		{
			$stmt=static::$db->prepare($insert);
			
		}
                
        $stmt->bindValue(":id",$this->id);
		$stmt->bindValue(":team1",$this->team1->getId());
		$stmt->bindValue(":team2",$this->team2->getId());
        $stmt->bindValue(":zeitpunkt",$this->zeitpunkt);
		$stmt->bindValue(":halbzeit1",$this->halbzeit1);
        $stmt->bindValue(":halbzeit2",$this->halbzeit2);
        $stmt->bindValue(":stadion",$this->stadion->getId());
        $stmt->bindValue(":zuschauzahl",$this->zuschauzahl);
        
        if(!$stmt->execute())
		{
			throw new Exception($stmt->errorInfo()[2]);
		}
    }
    public function load($id)
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        $stmt=static::$db->prepare("SELECT s.*,t1.*,t2.*,st.* FROM spiel s "
                . "LEFT JOIN team t1 ON t1.id=s.team1id "
                . "LEFT JOIN team t2 ON t2.id=s.team2id "
                . "LEFT JOIN stadion st ON st.id=s.stadionid "
                . "WHERE s.id=:id");
       
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $joincolumns=array();
            $joinarray=array();
            $joincolumns=array_merge($joincolumns, Match::getColumns("spiel"));
            $joincolumns=array_merge($joincolumns,Team::getColumns("team1"));
            $joincolumns=array_merge($joincolumns,Team::getColumns("team2"));
            $joincolumns=array_merge($joincolumns,Stadion::getColumns("stadion"));
            for($i=1;$i<=count($joincolumns);$i++)
            {
                $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
            }
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);
            if($result)
            {
                
                $this->stadion=new Stadion();
                $this->stadion->setValues($joinarray["stadionid"],$joinarray["stadionname"],$joinarray["stadionort"],$joinarray["stadionkapazitaet"]);
                $this->team1=new Team();
                $this->team1->setValues($joinarray["team1id"], $joinarray["team1name"]);
                $this->team2=new Team();
                $this->team2->setValues($joinarray["team2id"], $joinarray["team2name"]);
                $this->id=$joinarray["spielid"];
                $this->zeitpunkt=$joinarray["spielzeitpunkt"];
                $this->halbzeit1=$joinarray["spielhalbzeit1"];
                $this->halbzeit2=$joinarray["spielhalbzeit2"];
                $this->zuschauzahl=$joinarray["spielzuschauzahl"];
                $this->endstand1=$joinarray["spielendstand1"];
                $this->endstand2=$joinarray["spielendstand2"];
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
    public function getId()
    {
            return $this->id;
    }
    public function getTeam1()
    {
            return $this->team1;
    }
     public function getTeam2()
    {
            return $this->team2;
    }
     public function getZeitpunkt()
    {
            return $this->zeitpunkt;
    }
     public function getHalbzeit1()
    {
            return $this->halbzeit1;
    }
    public function getHalbzeit2()
    {
            return $this->halbzeit2;
    }
    public function getStadion()
    {
            return $this->stadion;
    }
    public function getZuschauzahl()
    {
            return $this->zuschauzahl;
    }
    public function setId($id)
    {
            $this->id=$id;        
    }
    public function setTeam1($team1)
    {
            $this->team1=$team1;        
    }
    public function setTeam2($team2)
    {
            $this->team2=$team2;        
    }
    public function setZeitpunkt($zeitpunkt)
    {
            $this->zeitpunkt=$zeitpunkt;        
    }
    public function setHalbzeit1($halbzeit1)
    {
            $this->halbzeit1=$halbzeit1;        
    }
    public function setHalbzeit2($halbzeit2)
    {
            $this->halbzeit2=$halbzeit2;        
    }
    public function setStadion($stadion)
    {
            $this->stadion=$stadion;        
    }
    public function setZuschauzahl($zuschauzahl)
    {
            $this->zuschauzahl=$zuschauzahl;        
    }
    public function setValues($id,$team1,$team2,$zeitpunkt,$halbzeit1,$halbzeit2,$stadion,$zuschauzahl)
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        $this->setId($id);
        if($team1 instanceof Team && $team2 instanceof Team && $stadion instanceof Stadion) 
        {
            $this->setTeam1($team1);
            $this->setTeam2($team2);
            $this->setStadion($stadion);
        }
        else 
        {
            $stmt=static::$db->prepare("SELECT t1.*,t2.*,s.*,m.* FROM spiel m "
                    . "LEFT JOIN t1.id=m.team1id ON team t1 "
                    . "LEFT JOIN t2.id=m.team2id ON team t2 "
                    . "LEFT JOIN s.id=m.stadionid ON stadion s "
                    . "WHERE m.team1id=:team1id AND m.team2is=:team2id AND m.stadionid=:stadionid");
            $stmt->bindValue(":team1id",$team1 instanceof Team?$team1->getId():$team1);
            $stmt->bindValue(":team2id",$team2 instanceof Team?$team2->getId():$team2);
            $stmt->bindValue(":stadionid",$stadion instanceof Stadion?$stadion->getId():$stadion);
            
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();

                $joincolumns=array_merge($joincolumns, Team::getColumns("team1"));
                $joincolumns=array_merge($joincolumns, Team::getColumns("team2"));
                $joincolumns=array_merge($joincolumns, Stadion::getColumns("stadion"));
                $joincolumns=array_merge($joincolumns, Match::getColumns("match"));
                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }

                if($stmt->fetch(PDO::FETCH_BOUND))
                {
                    $team1_temp=new Team();
                    $team1_temp->setValues($joinarray["team1id"], $joinarray["team1name"]);
                    $team2_temp=new Team();
                    $team2_temp->setValues($joinarray["team2id"], $joinarray["team2name"]);
                    $stadion_temp=new Stadion();
                    $stadion_temp->setValues($joinarray["stadionid"], $joinarray["stadionname"], $joinarray["stadionort"], $joinarray["stadionkapaitaet"]);
                    $this->setTeam1($team1_temp);
                    $this->setTeam2($team2_temp);
                    $this->setStadion($stadion_temp);
                }
            }
        }
        
        $this->setZeitpunkt($zeitpunkt);
        $this->setHalbzeit1($halbzeit1);
        $this->setHalbzeit2($halbzeit2);
        
        $this->setZuschauzahl($zuschauzahl);
    }
    public static function getAll() 
    {
        if(static::$db==null)
        {
            static::$db=DB::getDB();
        }
        if(static::$return_array==null)
        {
            $stmt=static::$db->prepare("SELECT t1.*,t2.*,s.*,m.* FROM spiel m LEFT JOIN team t1 ON t1.id=m.team1id LEFT JOIN team t2 ON t2.id=m.team2id LEFT JOIN stadion s ON s.id=m.stadionid");
            $error="";
            static::$return_array=array();
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();
                $joincolumns=array_merge($joincolumns, Team::getColumns("team1"));
                $joincolumns=array_merge($joincolumns, Team::getColumns("team2"));
                $joincolumns=array_merge($joincolumns, Stadion::getColumns("stadion"));
                $joincolumns=array_merge($joincolumns, Match::getColumns("match"));
  
                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {             
                    $team1_temp=new Team();
                    $team1_temp->setValues($joinarray["team1id"], $joinarray["team1name"]);
                    $team2_temp=new Team();
                    $team2_temp->setValues($joinarray["team2id"], $joinarray["team2name"]);
                    $stadion_temp=new Stadion();
                    $stadion_temp->setValues($joinarray["stadionid"], $joinarray["stadionname"], $joinarray["stadionort"], $joinarray["stadionkapazitaet"]);
                    $match_temp=new Match();
                    $match_temp->setValues($joinarray["matchid"], $team1_temp, $team2_temp, $joinarray["matchzeitpunkt"], $joinarray["matchhalbzeit1"], $joinarray["matchhalbzeit2"], $stadion_temp, $joinarray["matchzuschauzahl"], $joinarray["matchendstand1"], $joinarray["matchendstand2"]);
                 
                    array_push(static::$return_array,$match_temp);
                }

            }
        }
        return static::$return_array;
    }    
    public function getDescription()
    {
        return "".$this->id." - ".$this->zeitpunkt." - ".$this->team1->getName()." - ".$this->team2->getName()." - ".$this->stadion->getName();
    }
}