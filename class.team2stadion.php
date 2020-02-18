<?php
require_once("class.db.php");
require_once("class.stadion.php");
require_once("class.team.php");
require_once("class.AbstractBaseClass.php");

class Team2Stadion extends AbstractBaseClass
{
    protected static $columns=array("id","team","stadion","von","bis");
    protected static $all_elements=array();
    protected static $tablename="team2stadion";
    private $id;
    private $team;
    private $stadion;
    private $von;
    private $bis;
    
    public function __construct($id=0) 
    {
        $this->id=$id;
        $this->setTeam(0);
        $this->setStadion(0);
        if($id!=0)
        {
            $this->load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt= static::$db->prepare("SELECT t2s.*,t.*,s.* FROM team2stadion t2s "
                                    . "LEFT JOIN team t ON t.id=t2s.teamid "
                                    . "LEFT JOIN stadion s ON s.id=t2s.stadionid "
                                    . "WHERE t2s.id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if(DB::execute($stmt))
        {
        
            $joinarray=static::getJoinArray($stmt,array_merge(Team2Stadion::getColumns("t2s"),Team::getColumns("team"),Stadion::getColumns("stadion")));
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);
            if($result)
            {
                $this->id=$id;                
                $this->stadion=new Stadion();
                $this->stadion->setValues($joinarray["stadionid"],$joinarray["stadionname"],$joinarray["stadionort"],$joinarray["stadionkapazitaet"]);
                $this->team=new Team();
                $this->team->setValues($joinarray["teamid"], $joinarray["teamname"], $joinarray["teamregion"]);
                $this->von=$joinarray["t2svon"];
                $this->bis=$joinarray["t2sbis"];
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
        $insert="INSERT INTO team2stadion (id,teamid,stadionid,von,bis) VALUES (:id,:team,:stadion,:von,:bis)";
        if($this->id != 0)
        {
            $stmt=$this->db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE teamid=:team,stadionid=:stadion,von=:von,bis=:bis");    
        }
        else
        {
            $stmt=$this->db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":team",$this->team->getId());
        $stmt->bindValue(":stadion",$this->stadion->getId());
        $stmt->bindValue(":von",$this->von);
        $stmt->bindValue(":bis",$this->bis);
        
        DB::execute($stmt);
    }
    public function getId()
    {
        return $this->id;
    }
    public function getTeam()
    {
        return $this->team;
    }
    public function getStadion()
    {
        return $this->stadion;
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
    public function setVon($von)
    {
        $this->von=$von;        
    }
    public function setBis($bis)
    {
        $this->bis=$bis;        
    }
    public function setValues($id,$team,$stadion,$von,$bis)
    {
        $this->setId($id);
        $this->setTeam($team);
        $this->setStadion($stadion);
        $this->setVon($von);
        $this->setBis($bis);
    }
    public static function getAll() 
    {
        if(static::$all_elements==null)
        {
            $stmt= static::$db->prepare("SELECT t2s.*,t.*,s.* FROM team2stadion t2s "
                    . "LEFT JOIN team t ON t2s.teamid=t.id "
                    . "LEFT JOIN stadion s ON t2s.stadionid=s.id");
            $error="";
            static::$all_elements=array();
            if(DB::execute($stmt))
            {
                $joinarray=static::getJoinArray($stmt,array_merge(Team2Stadion::getColumns("t2s"),Team::getColumns("team"),Stadion::getColumns("stadion")));
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $team_temp=new Team();
                    $team_temp->setValues($joinarray["teamid"], $joinarray["teamname"], $joinarray["teamregion"]);
                    $stadion_temp=new Stadion();
                    $stadion_temp->setValues($joinarray["stadionid"], $joinarray["stadionname"], $joinarray["stadionort"], $joinarray["stadionkapazitaet"]);
                    $t2s_temp=new Team2Stadion();
                    $t2s_temp->setValues($joinarray["t2sid"],$team_temp,$stadion_temp,$joinarray["t2svon"],$joinarray["t2sbis"]);
                    array_push(static::$all_elements,$t2s_temp);
                }

            }
        }
        return static::$all_elements;
    }
}
