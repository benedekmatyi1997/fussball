<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
require_once("class.AbstractBaseClass.php");

class Spieler extends AbstractBaseClass
{
    protected static $columns=array("id","vorname","nachname","geburtsdatum");
    protected static $all_elements=array();
    protected static $tablename="spieler";
    private $id;
    private $vorname;
    private $nachname;
    private $geburtsdatum;
    
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
        $stmt=static::$db->prepare("SELECT * FROM spieler WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if(DB::execute($stmt))
        {
            $result=$stmt->fetch();
            if($result)
            {
                $this->id=$id;
                $this->vorname=$result["vorname"];
                $this->nachname=$result["nachname"];
                $this->geburtsdatum=$result["geburtsdatum"];
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
        $insert="INSERT INTO spieler (id,vorname,nachname,geburtsdatum) VALUES (:id,:vorname,:nachname,:geburtsdatum)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE vorname=:vorname ,nachname=:nachname ,geburtsdatum=:geburtsdatum");
            
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":vorname",$this->vorname);
        $stmt->bindValue(":nachname",$this->nachname);
        $stmt->bindValue(":geburtsdatum",$this->geburtsdatum);
                
        DB::execute($stmt);  
    }
    public function setValues($id,$vorname,$nachname,$geburtsdatum)
    {
        $this->setId($id);
        $this->setVorname($vorname);
        $this->setNachname($nachname);
        $this->setGeburtsdatum($geburtsdatum);
    }
    public function getId()
    {
        return $this->id;
    }
    public function getVorname()
    {
        return $this->vorname;
    }
    public function getNachname()
    {
        return $this->nachname;
    }
    public function getGeburtsdatum()
    {
        return $this->geburtsdatum;
    }
        
    public function setId($id)
    {
        $this->id=$id;
    }
    public function setVorname($vorname)
    {
        if(strlen($vorname))
        {
            $this->vorname=$vorname;
        }
    }
    public function setNachname($nachname)
    {
        $this->nachname=$nachname;
    }
    public function setGeburtsdatum($geburtsdatum)
    {
        $this->geburtsdatum=$geburtsdatum;
    }
    public function getName()
    {
        return $this->vorname." ".$this->nachname;
    }

    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT * FROM spieler");
            $error="";
            static::$all_elements=array();
            if(DB::execute($stmt))
            {                
                $joinarray=static::getJoinArray($stmt,Spieler::getColumns("spieler"));
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $spieler_temp=new Spieler();
                    $spieler_temp->setValues($joinarray["spielerid"], $joinarray["spielervorname"], $joinarray["spielernachname"], $joinarray["spielergeburtsdatum"]);

                    array_push(static::$all_elements,$spieler_temp);
                }

            }
        }
   
        return static::$all_elements;
    }
}
