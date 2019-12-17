<?php
require_once("class.team.php");
require_once("smarty.inc.php");
$team=new Team();
$smarty=new Smarty();
$team->setValues(filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "name"));
try
{
    $team->update();
    $smarty->assign("id",$team->getId());
    $smarty->assign("name",$team->getName());
    $smarty->display("team_erstellen_erfolgreich.tpl");
}
catch(Exception $e)
{
    $smarty->assign("fehler",$e->getMessage());
    $smarty->display("team_erstellen_fehler.tpl");
}