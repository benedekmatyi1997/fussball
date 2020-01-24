<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");

class Region extends AbstractBaseClass
{
    protected static $columns=array("id","name","code","uebergeordnet","typ");
    private $id;
    private $name;
    private $code;
    private $uebergeordnet;
    private $typ;
    
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
        $stmt=static::$db->prepare("SELECT * FROM region WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $result=$stmt->fetch();
            if($result)
            {
                $this->id=$id;
                $this->name=$result["name"];
                $this->code=$result["code"];
                $this->code=$result["uebergeordnet"];
                $this->code=$result["typ"];
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
        $insert="INSERT INTO region (id,name,code,uebergeordnet,typ) VALUES (:id,:name,:code,:uebergeordnet,:typ)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE name=:name,code=:code,uebergeordnet=:uebergeordnet,typ=:typ");
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":name",$this->name);
        $stmt->bindValue(":code",$this->code);
        $stmt->bindValue(":code",$this->uebergeordnet);
        $stmt->bindValue(":code",$this->typ);
                
        if(!$stmt->execute())
        {
            throw new Exception($stmt->errorInfo()[2]);
        }
    }
    public function setValues($id,$name,$code,$uebergeordnet,$typ)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setCode($code);
        $this->setCode($uebergeordnet);
        $this->setCode($typ);
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
    public function getCode()
    {
        return $this->code;
    }
    public function setCode($code)
    {
        $this->code=$code;
    }
    public function getUebergeordnet()
{
    return $this->uebergeordnet;
}
public function setUebergeordnet($uebergeordnet)
{
    $this->uebergeordnet=$uebergeordnet;
}
public function getTyp()
{
    return $this->typ;
}
public function setTyp($typ)
{
    $this->typ=$typ;
}
    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT * FROM region");
            $error="";
            static::$all_elements=array();
            if($stmt->execute())
            {                
                $joinarray=static::getJoinArray($stmt,Region::getColumns("region"));
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $region_temp=new Region();
                    $region_temp->setValues($joinarray["regionid"], $joinarray["regionname"], $joinarray["regioncode"], $joinarray["regionuebergeordnet"], $joinarray["regiontyp"]);

                    array_push(static::$all_elements,$region_temp);
                }

            }
        }
        return static::$all_elements;
    }
    
}