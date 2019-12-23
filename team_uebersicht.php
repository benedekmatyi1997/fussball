<?php
require_once ("class.team.php");
require_once ("smarty.inc.php");

$alle_teams=Team::getAll();
$alle_teams_smarty=array();

foreach($alle_teams as $team)
{
    $team_array=array("id"=>$team->getId(), "name"=>$team->getName());
    array_push($alle_teams_smarty,$team_array);
}
$smarty=new Smarty();
$smarty->assign("alle_teams",$alle_teams_smarty);
$smarty->display("team_uebersicht.tpl");