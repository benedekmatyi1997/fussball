<?php
require_once ("class.team.php");
require_once ("smarty.inc.php");

$teamid=filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
$team=new Team($teamid);
$team_smarty=array("id"=>$team->getId(),"name"=>$team->getName());

$smarty=new Smarty();
$smarty->assign("team",$team_smarty);
$smarty->display("team_anzeigen.tpl");