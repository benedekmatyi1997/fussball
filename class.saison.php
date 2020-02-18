<?php
require_once("class.db.php");
require_once("class.AbstractBaseClass.php");
require_once("class.liga.php");


class Saison extends AbstractBaseClass
{
    protected static $columns=array("id","liga","aufstieg","von","bis");
    protected static $all_elements=array();
    protected static $tablename="saison";
    private $id;
    private $liga;
    private $aufstieg;
    private $von;
    private $bis;
    
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
        $stmt=static::$db->prepare("SELECT sa.*,li.* FROM saison sa ". 
                "LEFT JOIN liga li ON li.id=sa.liga ".
                "WHERE sa.id=:id");
        $stmt->bindValue(":id",$id);
        $error="";
        if(DB::execute($stmt))
        {   
            $joinarray=static::getJoinArray($stmt,array_merge(Saison::getColumns("saison"),Liga::getColumns("liga")));
            $result=$stmt->fetch(PDO::FETCH_BOUND);
            if($result)
            {
                $this->id=$id;
                $this->liga=new Liga();
                $this->liga->setValues($joinarray["ligaid"], $joinarray["liganame"], $joinarray["ligaregion"], $joinarray["ligaaufstieg"]);
                $this->von=$joinarray["saisonaufstieg"];
                $this->von=$joinarray["saisonvon"];
                $this->bis=$joinarray["saisonbis"];
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
        $insert="INSERT INTO saison (id,liga,aufstieg,von,bis) VALUES (:id,:liga,:aufstieg,:von,:bis)";
        if($this->id != 0)
        {
            $stmt=static::$db->prepare("$insert
                        ON DUPLICATE KEY
                        UPDATE liga=:liga,aufstieg=:aufstieg,von=:von,bis=:bis");
        }
        else
        {
            $stmt=static::$db->prepare($insert);
            
        }
                
        $stmt->bindValue(":id",$this->id);
        $stmt->bindValue(":liga",$this->liga);
        $stmt->bindValue(":aufstieg",$this->aufstieg);
        $stmt->bindValue(":von",$this->von);
        $stmt->bindValue(":bis",$this->bis);
                
        DB::execute($stmt);
    }
    public function setValues($id,$liga,$aufstieg,$von,$bis)
    {
        $this->setId($id);
        $this->setLiga($liga);
        $this->setAufstieg($aufstieg);
        $this->setVon($von);
        $this->setBis($bis);
    }
    public function getId()
{
    return $this->id;
}
    public function setId($id)
    {
        $this->id=$id;
    }
    public function getLiga()
    {
        return $this->liga;
    }
    public function setLiga($liga)
    {
        $this->liga=$liga;
    }
    public function getAufstieg()
    {
        return $this->aufstieg;
    }
    public function setAufstieg($aufstieg)
    {
        $this->aufstieg=$aufstieg;
    }
    public function getVon()
    {
        return $this->von;
    }
    public function setVon($von)
    {
        $this->von=$von;
    }
    public function getBis()
    {
        return $this->bis;
    }
    public function setBis($bis)
    {
        $this->bis=$bis;
    }
    public static function getAll()
    {
        static::initDB();
        if(static::$all_elements==null)
        {
            $stmt=static::$db->prepare("SELECT sa.*,li.*,re.* FROM saison sa "
                                     . "LEFT JOIN liga li ON li.id=sa.liga "
                                     . "LEFT JOIN region re ON re.id=li.region");
            $error="";
            static::$all_elements=array();
            if(DB::execute($stmt))
            {                
                $joinarray=static::getJoinArray($stmt, array_merge(Saison::getColumns("saison"),Liga::getColumns("liga"),Region::getColumns("region")));
                
                while ($result=$stmt->fetch(PDO::FETCH_BOUND))
                {
                    $region_temp=new Region();
                    $region_temp->setValues($joinarray["regionid"], $joinarray["regionname"],$joinarray["regioncode"],$joinarray["regionuebergeordnet"],$joinarray["regiontyp"]);
                    $liga_temp=new Liga();
                    $liga_temp->setValues($joinarray["ligaid"], $joinarray["liganame"],$region_temp,$joinarray["ligaaufstieg"]);
                    $saison_temp=new Saison();
                    $saison_temp->setValues($joinarray["saisonid"], $liga_temp,$joinarray["saisonaufstieg"],$joinarray["saisonvon"],
                                          $joinarray["saisonbis"]);

                    array_push(static::$all_elements,$saison_temp);
                }

            }
        }
        return static::$all_elements;
    }

}