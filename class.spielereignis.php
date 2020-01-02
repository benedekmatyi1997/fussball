<?php
require_once("class.db.php");
require_once("class.spieler.php");
require_once("class.match.php");
require_once("class.AbstractBaseClass.php");

class Spielereignis extends AbstractBaseClass
{
    protected static $columns=array("id","spieler","minute","nachspielzeit","typ","match");
    private $id;
    private $spieler;
    private $minute;
    private $nachspielzeit;
    private $typ;
    private $match;
    
    public function __construct($id=0)
    {
        $this->id=$id;
        $this->setSpieler(0);
        $this->setMatch(0);
        if($id!=0)
        {
            load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT * FROM spielereignis WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $joinarray=static::getJoinArray($stmt,array_merge(Spielereignis::getColumns("spielereignis"),Spieler::getColumns("spieler"),Match::getColumns("spiel")));
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);
                if($result)
                {
                    $this->spieler->setValues($joinarray["spielerid"],$joinarray["spielervorname"],$joinarray["spielernachname"],$joinarray["spielergeburtsdatum"]);
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
        static::initDB();

        $insert="INSERT INTO spielereignis (id,spielerid,minute,nachspielzeit,typ,matchid) ".
                "VALUES (:id,:spielerid,:minute,:nachspielzeit,:typ,:matchid)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE spielerid=:spielerid,minute=:minute,nachspielzeit=:nachspielzeit,typ=:typ,matchid=:matchid");    
        }
        else
        {
            $stmt=static::$db->prepare($insert);
        }

        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":spielerid",$this->getSpieler()->getId());
        $stmt->bindValue(":matchid",$this->getMatch()->getId());
        $stmt->bindValue(":minute",$this->minute);
        $stmt->bindValue(":nachspielzeit",$this->nachspielzeit);
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
    public function getSpieler():Spieler
    {
        return $this->spieler;
    }
    public function getMatch():Match
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
        if($match instanceof Match)
        {
            $this->match=$match;        
        }
        else if(is_int($match) && $match)
        {
            $this->match->load($match);
        }
        else// if(!$match)
        {
            $this->match=new Match();
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
    public function setMinute($minute)
    {
        $this->minute=$minute;        
    }
    public function setNachspielzeit($nachspielzeit)
    {
        if(in_array($nachspielzeit, array(45,90,105,120)))
        {
            $this->nachspielzeit=$nachspielzeit;        
        }
        else
        {
            $this->nachspielzeit=0;
        }
    }
    public function setTyp($typ)
    {
        $this->typ=$typ;        
    }
    public function setValues($id,$match,$spieler,$minute,$nachspielzeit,$typ)
    {
        $this->setId($id);
        $this->setMatch($match);
        $this->setSpieler($spieler);
        $this->setMinute($minute);
        $this->setNachspielzeit($nachspielzeit);
        $this->setTyp($typ);
    }
    public static function getAll()
    {   
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT m.*,sp.*,se.* FROM spielereignis se LEFT JOIN match m ON m.id=se.matchid LEFT JOIN spieler sp ON sp.id=se.spielerid");
            $error="";
            static::$all_elements=array();
            if($stmt->execute())
            {
                $joinarray=static::getJoinArray($stmt,array_merge(Match::getColumns("match"),Spieler::getColumns("spieler"),Spielereignis::getColumns("spielereignis")));

                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {             
                    $match_temp=new Match();
                    $match_temp->setValues($joinarray["matchid"], $joinarray["matchteam1id"], $joinarray["matchteam2id"], $joinarray["matchzeitpunkt"], $joinarray["matchhalbzeit1"], $joinarray["matchhalbzeit2"], $joinarray["matchstadionid"], $joinarray["matchzuschauzahl"], $joinarray["matchendstand1"], $joinarray["matchendstand2"]);
                    $spieler_temp=new Spieler();
                    $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                    $spielereignis_temp=new Spielereignis();
                    $spielereignis_tempemp->setValues($joinarray["spielereignisid"], $match_temp, $spieler_temp, $joinarray["spielereignisminute"],$joinarray["spielereignisnachspielzeit"], $joinarray["spielereignistyp"]);

                    array_push(static::$all_elements,$spielereignis_temp);
                }

            }
        }
        return static::$all_elements;
    }
    public static function getSpielereignisseForMatch($matchid) 
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT s.*, se.* FROM spielereignis se ".
                                   "LEFT JOIN spieler s ON se.spielerid=s.id ".
                                   "WHERE se.matchid=:matchid");
        
        $stmt->bindValue(":matchid",$matchid);
        if($stmt->execute())
        {
            $spielereignisse=array();
            
            $joinarray=static::getJoinArray($stmt,array_merge(Spieler::getColumns("spieler"),Spielereignis::getColumns("spielereignis")));
            
            while($result=$stmt->fetch(PDO::FETCH_BOUND))
            {
                $spieler_temp=new Spieler();
                $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);
                $spielereignis_temp=new Spielereignis();
                $spielereignis_temp->setValues($joinarray["spielereignisid"], $joinarray["spielereignismatch"], $spieler_temp, $joinarray["spielereignisminute"], $joinarray["spielereignisnachspielzeit"], $joinarray["spielereignistyp"]);
                array_push($spielereignisse,$spielereignis_temp);
            }
            return $spielereignisse;
        }
        else 
        {
            throw new Exception($stmt->errorInfo()[2]);
        }
    }
}
