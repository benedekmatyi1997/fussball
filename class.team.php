<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
require_once("class.region.php");
class Team extends AbstractBaseClass
{
    protected static $columns=array("id","name","region");
    protected static $all_elements=array();
    protected static $tablename="team";
    private $id;
    private $name;
    private $region;

    public function __construct($id=0)
    {
        
        $this->id=$id;
        $this->setRegion(0);
        if($id!=0)
        {
            $this->load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT * FROM team WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if(DB::execute($stmt))
        {
            $result=$stmt->fetch();
            if($result)
            {
                $this->id=$id;                
                $this->name=$result["name"];
                $this->setRegion($result["region"]);
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
        $insert="INSERT INTO team (id,name,region) VALUES (:id,:name,:region)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert 
                                      ON DUPLICATE KEY 
                                      UPDATE team SET name=:name,region=:region WHERE id=:id");
            
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":name",$this->name);
        $stmt->bindValue(":region",$this->region->getId());
        DB::execute($stmt);       
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getRegion()
    {
        return $this->region;
    }
    public function setId($id)
    {
        $this->id=$id;
    }
    public function setName($name)
    {
        $this->name=$name;
    }
    public function setRegion($region)
    {
        if($region instanceof Region)
        {
            $this->region=$region;        
        }
        else if(is_numeric($region) && $region)
        {
            $this->region->load($region);
        }
        else
        {
            $this->region=new Region();
        }
    }
    public function setValues($id,$name,$region)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setRegion($region);
    }
    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            static::$all_elements=array();
            $stmt=static::$db->prepare("SELECT * FROM team");
            $error="";

            if(DB::execute($stmt))
            {
                    $joinarray=static::getJoinArray($stmt,Team::getColumns("team"));

                    while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                    {
                        $team_temp=new Team();
                        $team_temp->setValues($joinarray["teamid"], $joinarray["teamname"], $joinarray["teamregion"]);
                        array_push(static::$all_elements,$team_temp);
                    }

            }
        }
        return static::$all_elements;
    }
}