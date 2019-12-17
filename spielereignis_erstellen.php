<?php
require_once ("class.spielereignis.php");
require_once ("smarty.inc.php");

$se=new Spielereignis();
$smarty=new Smarty();
$se->setValues(filter_input(INPUT_POST, "id"), new Match(filter_input(INPUT_POST, "match")), new Spieler(filter_input(INPUT_POST, "spieler")),filter_input(INPUT_POST, "minute"),filter_input(INPUT_POST, "nachspielzeit"),filter_input(INPUT_POST, "typ"));
try
{
    $se->update();
    $smarty->assign("id",$se->getId());
    $smarty->assign("match",$se->getMatch()->getId());
    $smarty->assign("spieler",$se->getSpieler()->getId());
    $smarty->assign("minute",$se->getMinute());
    $smarty->assign("nachspielzeit",$se->getNachspielzeit());
    $smarty->assign("typ",$se->getTyp());
    $smarty->display("spielereignis_erstellen_erfolgreich.tpl");
}
catch(Exception $e)
{
    print($e->getMessage());
    print_r($e->getTrace());
    $smarty->assign("fehler",$e->getTrace());
    $smarty->display("spielereignis_erstellen_fehler.tpl");
}


