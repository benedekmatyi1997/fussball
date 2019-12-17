<?php
require_once("class.stadion.php");
require_once("class.team.php");
require_once("smarty.inc.php");
$alle_stadion_objekte= Stadion::getAll();
$alle_stadion_array=array();
for($i=0;$i<count($alle_stadion_objekte);$i++)
{
    $alle_stadion_array[$i]["id"]=$alle_stadion_objekte[$i]->getId();
    $alle_stadion_array[$i]["name"]=$alle_stadion_objekte[$i]->getName();
}
$alle_teams_objekte=Team::getAll();
$alle_teams_array=array();
for($i=0;$i<count($alle_teams_objekte);$i++)
{
    $alle_teams_array[$i]["id"]=$alle_teams_objekte[$i]->getId();
    $alle_teams_array[$i]["name"]=$alle_teams_objekte[$i]->getName();
}
print_r($alle_stadion_array);
print_r($alle_teams_array);
$smarty=new Smarty();
$smarty->assign("stadion",$alle_stadion_array);
$smarty->assign("teams",$alle_teams_array);
$smarty->display("team2stadion_neu.tpl");
