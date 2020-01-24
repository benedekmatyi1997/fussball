<?php
require_once("class.db.php");
require_once("class.stadion.php");
require_once("class.team.php");
require_once("class.AbstractBaseClass.php");

class Match extends AbstractBaseClass 
{
    protected static $columns=array("id","team1","team2","saison","zeitpunkt","halbzeit1","halbzeit2","stadion","zuschauzahl","endstand1","endstand2");

    private $id;
    private $team1;
    private $team2;
    private $saison;
    private $zeitpunkt;
    private $halbzeit1;
    private $halbzeit2;
    private $stadion;
    private $zuschauzahl;   //TODO: ändern auf zuschauERzahl
    private $endstand1;
    private $endstand2;
    
    public function __construct($id=0)
    {
        $this->id=$id;
        $this->setTeam1(0);
        $this->setTeam2(0);
        $this->setStadion(0);
        if($id!=0)
        {
            $this->load($id);
        }
    }
    public function update()
    {
        static::initDB();
        $insert="INSERT INTO spiel (id,team1,team2,saison,zeitpunkt,halbzeit1,halbzeit2,stadion,zuschauzahl,endstand1,endstand2) "
              . "VALUES (:id,:team1,:team2,:saison,:zeitpunkt,:halbzeit1,:halbzeit2,:stadion,:zuschauzahl,:endstand1,:endstand2)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE team1=:team1,team2=:team2,saison=:saison,zeitpunkt=:zeitpunkt,".
                        "halbzeit1=:halbzeit1,halbzeit2=:halbzeit2,stadion=:stadion,zuschauzahl=:zuschauzahl,endstand1=:endstand1,endstand2=:endstand2");
            
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":team1",$this->team1->getId());
        $stmt->bindValue(":team2",$this->team2->getId());
        $stmt->bindValue(":saison",$this->saison);
        $stmt->bindValue(":zeitpunkt",$this->zeitpunkt);
        $stmt->bindValue(":halbzeit1",$this->halbzeit1);
        $stmt->bindValue(":halbzeit2",$this->halbzeit2);
        $stmt->bindValue(":stadion",$this->stadion->getId());
        $stmt->bindValue(":zuschauzahl",$this->zuschauzahl);
        $stmt->bindValue(":endstand1",$this->endstand1);
        $stmt->bindValue(":endstand2",$this->endstand2);
        
        if(!$stmt->execute())
        {
            throw new Exception($stmt->errorInfo()[2]);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT s.*,t1.*,t2.*,st.* FROM spiel s "
                . "LEFT JOIN team t1 ON t1.id=s.team1id "
                . "LEFT JOIN team t2 ON t2.id=s.team2id "
                . "LEFT JOIN stadion st ON st.id=s.stadionid "
                . "WHERE s.id=:id");
       
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $joinarray=static::getJoinArray($stmt,array_merge(Match::getColumns("spiel"),Team::getColumns("team1"),
                                                              Team::getColumns("team2"),Stadion::getColumns("stadion")));
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);
            if($result)
            {                
                $this->stadion->setValues($joinarray["stadionid"],$joinarray["stadionname"],$joinarray["stadionort"],$joinarray["stadionkapazitaet"]);
                $this->team1->setValues($joinarray["team1id"], $joinarray["team1name"]);
                $this->team2->setValues($joinarray["team2id"], $joinarray["team2name"]);
                $this->saison=$joinarray["spielsaison"];
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
    public function getTeam1():Team
    {
            return $this->team1;
    }
    public function getTeam2():Team
    {
            return $this->team2;
    }
    public function getSaison()
    {
        return $this->saison;
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
    public function getStadion():Stadion
    {
            return $this->stadion;
    }
    public function getZuschauzahl()
    {
            return $this->zuschauzahl;
    }
    public function getEndstand1() 
    {
        return $this->endstand1;
    }

    public function getEndstand2() 
    {
        return $this->endstand2;
    }

    public function setId($id)
    {
            $this->id=$id;        
    }
    public function setTeam1($team1)
    {
        if($team1 instanceof Team)
        {
            $this->team1=$team1;        
        }
        else if(is_numeric($team1) && $team1)
        {
            $this->team1->load($team1);
        }
        else
        {
            $this->team1=new Team();
        }
    }
    public function setTeam2($team2)
    {
        if($team2 instanceof Team)
        {
            $this->team2=$team2;        
        }
        else if(is_numeric($team2) && $team2)
        {
            $this->team2->load($team2);
        }
        else
        {
            $this->team2=new Team();
        }
    }
    public function setSaison($saison)
    {
        $this->saison=$saison;
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
        if($stadion instanceof Stadion)
        {
            $this->stadion=$stadion;        
        }
        else if(is_numeric($stadion) && $stadion)
        {
            $this->stadion->load($stadion);
        }
        else
        {
            $this->stadion=new Stadion();
        }
    }
    public function setZuschauzahl($zuschauzahl)
    {
            $this->zuschauzahl=$zuschauzahl;        
    }

    public function setEndstand1($endstand1) 
    {
        $this->endstand1 = $endstand1;
    }

    public function setEndstand2($endstand2) 
    {
        $this->endstand2 = $endstand2;
    }


    public function setValues($id,$team1,$team2,$saison,$zeitpunkt,$halbzeit1,$halbzeit2,$stadion,$zuschauzahl,$endstand1,$endstand2)
    {
        static::initDB();
        $this->setId($id);
        $this->setTeam1($team1);
        $this->setTeam2($team2);
        $this->setSaison($saison);
        $this->setZeitpunkt($zeitpunkt);
        $this->setHalbzeit1($halbzeit1);
        $this->setHalbzeit2($halbzeit2);
        $this->setStadion($stadion);
        $this->setZuschauzahl($zuschauzahl);
        $this->setEndstand1($endstand1);
        $this->setEndstand2($endstand2);
    }
    public static function getAll() 
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT m.*,t1.*,t2.*,s.* FROM spiel m LEFT JOIN team t1 ON t1.id=m.team1id LEFT JOIN team t2 ON t2.id=m.team2id LEFT JOIN stadion s ON s.id=m.stadionid");
            $error="";
            static::$all_elements=array();
            if($stmt->execute())
            {
                $joinarray=static::getJoinArray($stmt,array_merge(Match::getColumns("spiel"),Team::getColumns("team1"),
                                                              Team::getColumns("team2"),Stadion::getColumns("stadion")));
                 
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {             
                    $team1_temp=new Team();
                    $team1_temp->setValues($joinarray["team1id"], $joinarray["team1name"]);
                    $team2_temp=new Team();
                    $team2_temp->setValues($joinarray["team2id"], $joinarray["team2name"]);
                    $stadion_temp=new Stadion();
                    $stadion_temp->setValues($joinarray["stadionid"], $joinarray["stadionname"], $joinarray["stadionort"], $joinarray["stadionkapazitaet"]);
                    $match_temp=new Match();
                    $match_temp->setValues($joinarray["matchid"], $team1_temp, $team2_temp, $joinarray["matchsaison"], $joinarray["matchzeitpunkt"], 
                                           $joinarray["matchhalbzeit1"], $joinarray["matchhalbzeit2"], $stadion_temp, $joinarray["matchzuschauzahl"], 
                                           $joinarray["matchendstand1"], $joinarray["matchendstand2"]);
                 
                    array_push(static::$all_elements,$match_temp);
                }

            }
        }
        return static::$all_elements;
    }        
    public function getDescription()
    {
        return "".$this->id." - ".$this->zeitpunkt." - ".$this->team1->getName()." - ".$this->team2->getName()." - ".$this->stadion->getName();
    }
}