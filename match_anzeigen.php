<?php
require_once ("class.match.php");
require_once ("class.spielereignis.php");
require_once ("smarty.inc.php");

$matchid=filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
$match=new Match($matchid);
$spielereignisse=Spielereignis::getSpielereignisseForMatch($matchid);
$match_smarty=array("id"=>$match->getId(),"team1"=>$match->getTeam1()->getName(),"team2"=>$match->getTeam2()->getName(),"description"=>$match->getDescription());
$spielereignisse_smarty=array();
foreach ($spielereignisse as $spielereignis) {
    array_push($spielereignisse_smarty,$spielereignis->getAsArray());
}
$smarty=new Smarty();
$smarty->assign("match",$match_smarty);
$smarty->assign("spielereignisse",$spielereignisse_smarty);
$smarty->display("match_anzeigen.tpl");
