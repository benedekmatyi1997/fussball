<?php
require_once("class.spieler.php");
require_once("class.team.php");
require_once("smarty.inc.php");
$alle_spieler_objekte= Spieler::getAll();
$alle_spieler_array=array();
for($i=0;$i<count($alle_spieler_objekte);$i++)
{
    $alle_spieler_array[$i]["id"]=$alle_spieler_objekte[$i]->getId();
    $alle_spieler_array[$i]["name"]=$alle_spieler_objekte[$i]->getName();
}
$alle_teams_objekte=Team::getAll();
$alle_teams_array=array();
for($i=0;$i<count($alle_teams_objekte);$i++)
{
    $alle_teams_array[$i]["id"]=$alle_teams_objekte[$i]->getId();
    $alle_teams_array[$i]["name"]=$alle_teams_objekte[$i]->getName();
}
print_r($alle_spieler_array);
print_r($alle_teams_array);
$smarty=new Smarty();
$smarty->assign("spieler",$alle_spieler_array);
$smarty->assign("teams",$alle_teams_array);
$smarty->display("spieler2team_neu.tpl");
