<?php
require_once("class.team2stadion.php");
require_once("smarty.inc.php");
$t2s=new Team2Stadion();
$smarty=new Smarty();
$t2s->setValues(filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "team"),filter_input(INPUT_POST, "stadion"),filter_input(INPUT_POST, "von"),filter_input(INPUT_POST, "bis"));
try
{
    $t2s->update();
    $smarty->assign("id",$t2s->getId());
    $smarty->assign("team",$t2s->getTeam()->getName());
    $smarty->assign("stadion",$t2s->getStadion()->getName());
    $smarty->assign("von",$t2s->getVon());
    $smarty->assign("bis",$t2s->getBis());
    $smarty->display("team2stadion_erstellen_erfolgreich.tpl");
}
catch(Exception $e)
{
    print($e->getMessage());
    print_r($e->getTrace());
    $smarty->assign("fehler",$e->getTrace());
    $smarty->display("team2stadion_erstellen_fehler.tpl");
}

