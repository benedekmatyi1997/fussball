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
    
    public function __construct($id=0)
    {
        $this->id=$id;
        $this->setSpieler(0);
        $this->setTeam(0);
        if($id!=0)
        {
                load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT s2t.*,sp.*,t.* FROM spieler2team s2t ".
                                    "LEFT JOIN team t ON s2t.teamid=t.id ".
                                    "LEFT JOIN spieler sp ON s2t.spieler=sp.id WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $joinarray=static::getJoinArray($stmt,array_merge(Spieler2Team::getColumns("s2t"),Spieler::getColumns("spieler"),Team::getColumns("team")));
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);

            if($result)
            {
                $this->spieler->setValues($joinarray["spielerid"],$joinarray["spielervorname"],
                                          $joinarray["spielernachname"],$joinarray["spielergeburtsdatum"]);
                $this->team=new Team();
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
        static::initDB();
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
    public function getSpieler():Spieler
    {
        return $this->spieler;
    }
    public function getTeam():Team
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
        if($team instanceof Team)
        {
            $this->team=$team;        
        }
        else if(is_int($team) && $team)
        {
            $this->team->load($team);
        }
        else
        {
            $this->team=new Team();
        }
    }
    public function setSpieler($spieler)
    {
        if($spieler instanceof Spieler)
        {
            $this->spieler=$spieler;        
        }
        else if(is_int($spieler) && $spieler)
        {
            $this->spieler->load($spieler);
        }
        else// if(!$spieler)
        {
            $this->spieler=new Spieler();
        }
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
        $this->setSpieler($spieler);
        $this->setTeam($team);
        $this->setVon($von);
        $this->setBis($bis);
    }

    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT s2t.*,sp.*,t.* FROM spieler2team ".
                                        "LEFT JOIN team t ON s2t.teamid=t.id ".
                                        "LEFT JOIN spieler sp ON s2t.spieler=sp.id");
            $error="";
            if($stmt->execute())
            {
                $joinarray=static::getJoinArray($stmt,array_merge(Spieler2Team::getColumns("s2t"),Spieler::getColumns("spieler"),Team::getColumns("team")));
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $team_temp=new Team();
                    $team_temp->setValues($result["teamid"], $result["teamname"]);
                    $spieler_temp=new Spieler();
                    $spieler_temp->setValues($result["spielerid"], $result["spielervorname"], $result["spielernachname"], $result["spielergeburtsdatum"]);
                    $s2t_temp=new Spieler2Team();
                    $s2t_temp->setValues($result["s2tid"],$spieler_temp,$team_temp,$result["s2tvon"],$result["s2tbis"]);
                    array_push(static::$all_elements,$s2t_temp);
                }

            }
        }
        return static::$all_elements;
    }
    public static function getSpielerForDate($team1,$team2,$date)
    {
        static::initDB();
        $return_array=array();
        $stmt=static::$db->prepare("(SELECT s2t.*,t.*,sp.* FROM spieler2team s2t ".
                                   "LEFT JOIN team t ON s2t.teamid=t.id ". 
                                   "LEFT JOIN spieler sp ON s2t.spielerid=sp.id ".
                                   "WHERE s2t.von <= :date AND s2t.bis >=:date AND s2t.teamid=:team1) UNION ".
                                   "(SELECT s2t.*,t.*,sp.* FROM spieler2team s2t ".
                                   "LEFT JOIN team t ON s2t.teamid=t.id ". 
                                   "LEFT JOIN spieler sp ON s2t.spielerid=sp.id ".
                                   "WHERE s2t.von <= :date AND s2t.bis >=:date AND s2t.teamid=:team2)");
        $stmt->bindValue(":team1",$team1);
        $stmt->bindValue(":team2",$team2);
        $stmt->bindValue(":date",$date);
        if($stmt->execute())
        {
            $joinarray=static::getJoinArray($stmt,array_merge(Spieler2Team::getColumns("s2t"),Team::getColumns("team"),Spieler::getColumns("spieler")));

            while($result=$stmt->fetch(PDO::FETCH_BOUND))
            {
                $temp_element=array();
                $temp_element["spieler"]=new Spieler();
                $temp_element["team"]=new Team();
                $temp_element["spieler"]->setValues($joinarray["spielerid"], $joinarray["spielervorname"], 
                                                    $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                $temp_element["team"]->setValues($joinarray["teamid"], $joinarray["teamname"]);
                array_push($return_array,$temp_element);
            }
        }
        else 
        {
            throw new Exception($stmt->errorInfo()[2]);
        }
        return $return_array;
    }
}