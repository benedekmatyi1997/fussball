<?php
require_once ("class.match.php");
require_once ("smarty.inc.php");

$alle_matches=Match::getAll();
$alle_matches_smarty=array();

foreach ($alle_matches as $match) {
    $match_array=array("description"=>$match->getDescription(),"id"=>$match->getId());
    array_push($alle_matches_smarty,$match_array);
}
$smarty=new Smarty();
$smarty->assign("alle_matches",$alle_matches_smarty);
$smarty->display("match_uebersicht.tpl");