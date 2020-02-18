<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
require_once("class.region.php");

class Liga extends AbstractBaseClass
{
    protected static $columns=array("id","name","region","aufstieg");
    protected static $all_elements=array();
    protected static $tablename="liga";
    private $id;
    private $name;
    private $region;
    private $aufstieg;
    
    public function __construct($id=0)
    {
        $this->id=$id;
        if($id!=0)
        {
            $this->load($id);
        }
    }
    public function load($id)
    {
        static::initDB();
        $stmt=static::$db->prepare("SELECT li.*,re.* FROM liga li ". 
                "LEFT JOIN region re ON re.id=li.region ".
                "WHERE li.id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {   
            $joinarray=static::getJoinArray($stmt,array_merge(Liga::getColumns("liga"),Region::getColumns("region")));
            $result=$stmt->fetch(PDO::FETCH_BOUND);
            if($result)
            {
                $this->id=$id;
                $this->name=$joinarray["liganame"];
                $this->region=new Region();
                $this->region->setValues($joinarray["regionid"], $joinarray["regionname"], $joinarray["regioncode"], $joinarray["regionuebergeordnet"], $joinarray["regiontyp"]);
                $this->aufstieg=$joinarray["ligaaufstieg"];
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
    public function update() //TODO:befejezni az osztaly, es megirni a saisont!!!!!!!
    {
        static::initDB();
        $insert="INSERT INTO liga (id,name,region,aufstieg) VALUES (:id,:name,:region,:aufstieg)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE name=:name,region=:region,aufstieg=:aufstieg");
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":name",$this->name);
        $stmt->bindValue(":region",$this->region);
        $stmt->bindValue(":aufstieg",$this->aufstieg);
                
        DB::execute($stmt);
    }
    public function setValues($id,$name,$region,$aufstieg)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setRegion($region);
        $this->setAufstieg($aufstieg);
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id=$id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name=$name;
    }
    public function getRegion()
    {
        return $this->region;
    }
    public function setRegion($region)
    {
        $this->region=$region;
    }
    public function getAufstieg()
    {
        return $this->aufstieg;
    }
    public function setAufstieg($aufstieg)
    {
        if($aufstieg instanceof Liga || is_null($aufstieg))
        {
            $this->aufstieg=$aufstieg;
        }
        else if(is_numeric($aufstieg) && $aufstieg)
        {
            $this->aufstieg=new Liga($aufstieg);
        }
    }
    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT la.*,li.* FROM liga li LEFT JOIN region la ON la.id=li.region");
            $error="";
            static::$all_elements=array();
            if(DB::execute($stmt))
            {                
                $joinarray=static::getJoinArray($stmt, array_merge(Liga::getColumns("liga"),Region::getColumns("region")));
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $region_temp=new Region();
                    $region_temp->setValues($joinarray["regionid"],$joinarray["regionname"],$joinarray["regioncode"],$joinarray["regionuebergeordnet"],$joinarray["regiontyp"]);
                    $liga_temp=new Liga();
                    $liga_temp->setValues($joinarray["ligaid"], $joinarray["liganame"],$region_temp,$joinarray["ligaaufstieg"]);

                    array_push(static::$all_elements,$liga_temp);
                }

            }
        }
        return static::$all_elements;
    }

}