<?php
require_once("class.db.php");
require_once("class.stadion.php");
require_once("class.team.php");
require_once("class.AbstractBaseClass.php");

class Team2Stadion extends AbstractBaseClass
{
	protected static $columns=array("id","team","stadion","von","bis");
    private $id;
	private $team;
	private $stadion;
	private $von;
	private $bis;
	private $db;
    
        public function __construct($id=0) 
        {
            $this->db=DB::getDB();
            $this->id=$id;
            if($id!=0)
            {
                load($id);
            }
        }
        public function load($id)
        {
            $stmt=$this->db->prepare("SELECT * FROM team2stadion WHERE id=:id");
            $stmt->bindValue(":id",$id);
            $error="";
            if($stmt->execute())
            {
            $joincolumns=array();
            $joinarray=array();
            array_merge($joincolumns, Team2Stadion::getColumns("t2s"));
            array_merge($joincolumns,Team::getColumns("team"));
            array_merge($joincolumns,Stadion::getColumns("stadion"));
            for($i=1;$i<=count($joincolumns);$i++)
            {
                $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
            }
            
            $result=$stmt->fetch(PDO::FETCH_BOUND);
                if($result)
                {
                    $this->stadion=new Stadion();
                    $this->stadion->setValues($joinarray["stadionid"],$joinarray["stadionname"],$joinarray["stadionort"],$joinarray["stadionkapazitaet"]);
                    $this->team=new Team();
                    $this->team->setValues($joinarray["teamid"], $joinarray["teamname"]);
                    $this->von=$joinarray["t2svon"];
                    $this->bis=$joinarray["t2sbis"];
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
        
        if(!$stmt->execute())
		{
			throw new Exception($stmt->errorInfo()[2]);
		}
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
		$this->team=$team;        
        }
        public function setStadion($stadion)
	{
		$this->stadion=$stadion;        
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
            
            $stmt=$this->db->prepare("SELECT t.*,s.* FROM team2stadion t2s LEFT JOIN team t ON t2s.teamid=t.id LEFT JOIN stadion s ON t2s.stadionid=s.id WHERE t2s.id=:id");
            $stmt->bindValue(":id",$this->id);
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();
                $temp=array();
                
                array_merge($joincolumns,Team::getColumns("team"));
                array_merge($joincolumns,Stadion::getColumns("stadion"));
                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }

                
                if($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $temp_team=new Team();
                    
                    $temp_team->setValues($joinarray["teamid"], $joinarray["teamname"]);
                    $this->setTeam($temp_team);
                    $temp_stadion=new Stadion();
                    $temp_stadion->setValues($joinarray["stadionid"], $joinarray["stadionname"], $joinarray["stadionort"], $joinarray["stadionkapazitaet"]);
                    $this->setStadion($temp_stadion);
                    
                }
                else 
                {
                    $this->setTeam(new Team($team));
                    $this->setStadion(new Stadion($stadion));
                }
            }
            else
            {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $this->setTeam(new Team($team));
            $this->setStadion(new Stadion($stadion));
            $this->setVon($von);
            $this->setBis($bis);
        }
    public static function getAll() 
    {
        if(static::$return_array==null)
        {
            $stmt=$this->db->prepare("SELECT t2s.*,t.*,s.* FROM team2stadion LEFT JOIN team t ON t2s.teamid=t.id LEFT JOIN stadion s ON t2s.stadion=s.id");
            $error="";
            $return_array=array();
            if($stmt->execute())
            {
                $joincolumns=array();
                $joinarray=array();
                $joincolumns=array_merge($joincolumns, Team2Stadion::getColumns("t2s"));
                $joincolumns=array_merge($joincolumns,Team::getColumns("team"));
                $joincolumns=array_merge($joincolumns, Stadion::getColumns("stadion"));

                for($i=1;$i<=count($joincolumns);$i++)
                {
                    $stmt->bindColumn($i,$joinarray[$joincolumns[$i-1]]);
                }

                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $team_temp=new Team();
                    $team_temp->setValues($result["teamid"], $result["teamname"]);
                    $stadion_temp=new Stadion();
                    $stadion_temp->setValues($result["stadionid"], $result["stadionname"], $result["stadionort"], $result["stadionkapazitaet"]);
                    $t2s_temp=new Team2Stadion();
                    $t2s_temp->setValues($result["t2sid"],$team_temp,$stadion_temp,$result["t2svon"],$result["t2sbis"]);
                    array_push($return_array,$t2s_temp);
                }

            }
        }
        return $return_array;
    }
}
