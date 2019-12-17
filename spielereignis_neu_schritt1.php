<?php
require_once ("class.spielereignis.php");
require_once ("smarty.inc.php");
$smarty=new Smarty();
$match_array=array();
foreach(Match::getAll() as $match)
{
    $einzel_daten=array();
    $einzel_daten["id"]=$match->getId();
    $einzel_daten["description"]=$match->getDescription();
    array_push($match_array,$einzel_daten);
}
$smarty->assign("match_daten",$match_array);
$smarty->display("spielereignis_neu_schritt1.tpl");

