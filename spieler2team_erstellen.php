<?php
require_once("class.spieler2team.php");
require_once("smarty.inc.php");
$s2t=new Spieler2Team();
$smarty=new Smarty();
print_r($_POST);
$s2t->setValues(filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "team"),filter_input(INPUT_POST, "spieler"),filter_input(INPUT_POST, "von"),filter_input(INPUT_POST, "bis"));
try
{
    $s2t->update();
    $smarty->assign("id",$s2t->getId());
    $smarty->assign("spieler",$s2t->getSpieler()->getName());
    $smarty->assign("team",$s2t->getTeam()->getName());
    $smarty->assign("von",$s2t->getVon());
    $smarty->assign("bis",$s2t->getBis());
    $smarty->display("spieler2team_erstellen_erfolgreich.tpl");
}
catch(Exception $e)
{
    print($e->getMessage());
    print_r($e->getTrace());
    $smarty->assign("fehler",$e->getTrace());
    $smarty->display("spieler2team_erstellen_fehler.tpl");
}

