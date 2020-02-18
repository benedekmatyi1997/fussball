<?php
require_once("class.db.php");
require_once("class.spieler.php");
require_once("class.team.php");
require_once("class.AbstractBaseClass.php");

class Spieler2Team extends AbstractBaseClass
{
    protected static $columns=array("id","spieler","team","von","bis");
    protected static $all_elements=array();
    protected static $tablename="spieler2team";
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
                $this->load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT s2t.*,sp.*,t.* FROM spieler2team s2t ".
                                    "LEFT JOIN team t ON s2t.teamid=t.id ".
                                    "LEFT JOIN spieler sp ON s2t.spielerid=sp.id WHERE s2t.id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if(DB::execute($stmt))
        {
            $joinarray=static::getJoinArray($stmt,array_merge(Spieler2Team::getColumns("s2t"),Spieler::getColumns("spieler"),Team::getColumns("team")));
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);

            if($result)
            {
                $this->id=$id;                
                $this->spieler->setValues($joinarray["spielerid"],$joinarray["spielervorname"],
                                          $joinarray["spielernachname"],$joinarray["spielergeburtsdatum"]);
                $this->team->setValues($joinarray["teamid"], $joinarray["teamname"], $joinarray["teamregion"]);
                $this->von=$joinarray["s2tvon"];
                $this->bis=$joinarray["s2tbis"];
            }
            else
            {
                $error.="Leeres Resultat";
            }
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

        DB::execute($stmt);
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
        else if(is_numeric($team) && $team)
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
        else if(is_numeric($spieler) && $spieler)
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
    public function setValues($id,$spieler,$team,$von,$bis)
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
            $stmt=static::$db->prepare("SELECT s2t.*,sp.*,t.* FROM spieler2team s2t ".
                                        "LEFT JOIN team t ON s2t.teamid=t.id ".
                                        "LEFT JOIN spieler sp ON s2t.spielerid=sp.id");
            $error="";
            if(DB::execute($stmt))
            {
                $joinarray=static::getJoinArray($stmt,array_merge(Spieler2Team::getColumns("s2t"),Spieler::getColumns("spieler"),Team::getColumns("team")));
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $team_temp=new Team();
                    $team_temp->setValues($joinarray["teamid"], $joinarray["teamname"], $joinarray["teamregion"]);
                    $spieler_temp=new Spieler();
                    $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                    $s2t_temp=new Spieler2Team();
                    $s2t_temp->setValues($joinarray["s2tid"],$spieler_temp,$team_temp,$joinarray["s2tvon"],$joinarray["s2tbis"]);
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
        if(DB::execute($stmt))
        {
            $joinarray=static::getJoinArray($stmt,array_merge(Spieler2Team::getColumns("s2t"),Team::getColumns("team"),Spieler::getColumns("spieler")));

            while($result=$stmt->fetch(PDO::FETCH_BOUND))
            {
                $temp_element=array();
                $temp_element["spieler"]=new Spieler();
                $temp_element["team"]=new Team();
                $temp_element["spieler"]->setValues($joinarray["spielerid"], $joinarray["spielervorname"], 
                                                    $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                $temp_element["team"]->setValues($joinarray["teamid"], $joinarray["teamname"], $joinarray["teamregion"]);
                array_push($return_array,$temp_element);
            }
        }
        
        return $return_array;
    }
}