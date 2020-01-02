 <?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
class Stadion extends AbstractBaseClass
{
    protected static $columns=array("id","name","ort","kapazitaet");
    private $id;
    private $name;
    private $ort;
    private $kapazitaet;

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
        $stmt=static::$db
                ->prepare("SELECT * FROM stadion WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $result=$stmt->fetch();
            if($result)
            {
                $this->name=$result["name"];
                $this->ort=$result["ort"];
                $this->kapazitaet=$result["kapazitaet"];
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
        $insert="INSERT INTO stadion (id,name,ort,kapazitaet) VALUES (:id,:name,:ort,:kapazitaet)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert 
                                      ON DUPLICATE 
                                      UPDATE stadion SET name=:name,ort=:ort,kapazitaet=:kapazitaet WHERE id=:id");
        
        }
        else
        {
            $stmt=static::$db->prepare($insert);
        
        }
                $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":name",$this->name);
        $stmt->bindValue(":ort",$this->ort);
        $stmt->bindValue(":kapazitaet",$this->kapazitaet);
        if(!$stmt->execute())
        {
            throw new Exception($stmt->errorInfo()[2]);
        }        
    }
    public function get_as_array()
    {
        return array("Id" => $this->getId(),
                     "name" => $this->getVorname(),
                     "ort" => $this->getOrt(),
                     "kapazitaet" => $this->getKapazitaet());
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getOrt()
    {
        return $this->ort;
    }
    public function getKapazitaet()
    {
        return $this->kapazitaet;
    }
    public function setId($id)
    {
        $this->id=$id;
    }
    public function setName($name)
    {
        $this->name=$name;
    }
    public function setOrt($ort)
    {
        $this->ort=$ort;
    }
    public function setKapazitaet($kapazitaet)
    {
        $this->kapazitaet=$kapazitaet;
    }
    public function setValues($id,$name,$ort,$kapazitaet)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setOrt($ort);
        $this->setKapazitaet($kapazitaet);
    }
    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            static::$all_elements=array();
            $stmt=static::$db->prepare("SELECT * FROM stadion");
            $error="";
            if($stmt->execute())
            {
                    $joinarray=static::getJoinArray($stmt,array_merge(static::getColumns("stadion")));

                    while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                    {
                        $stadion_temp=new Stadion();
                        $stadion_temp->setValues($joinarray["stadionid"], $joinarray["stadionname"], $joinarray["stadionort"], $joinarray["stadionkapazitaet"]);
                        array_push(static::$all_elements,$stadion_temp);
                    }

                    
            }
        }
        return static::$all_elements;
    }
}
