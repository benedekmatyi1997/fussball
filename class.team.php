<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
class Team extends AbstractBaseClass
{
    protected static $columns=array("id","name");
    private $id;
    private $name;

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
        $stmt=static::$db->prepare("SELECT * FROM team WHERE id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if($stmt->execute())
        {
            $result=$stmt->fetch();
            if($result)
            {
                $this->id=$id;                
                $this->name=$result["name"];
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
        $insert="INSERT INTO team (id,name) VALUES (:id,:name)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert 
                                      ON DUPLICATE KEY 
                                      UPDATE team SET name=:name WHERE id=:id");
            
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":name",$this->name);
        if(!$stmt->execute())
        {
            throw new Exception($stmt->errorInfo()[2]);
        }        
    }
    public function get_as_array()
    {
        return array("Id" => $this->getId(),
                     "name" => $this->getName());
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setId($id)
    {
        $this->id=$id;
    }
    public function setName($name)
    {
        $this->name=$name;
    }
    public function setValues($id,$name)
    {
        $this->setId($id);
        $this->setName($name);
    }
    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            static::$all_elements=array();
            $stmt=static::$db->prepare("SELECT * FROM team");
            $error="";

            if($stmt->execute())
            {
                    $joinarray=static::getJoinArray($stmt,Team::getColumns("team"));

                    while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                    {
                        $team_temp=new Team();
                        $team_temp->setValues($joinarray["teamid"], $joinarray["teamname"]);
                        array_push(static::$all_elements,$team_temp);
                    }

            }
        }
        return static::$all_elements;
    }
}