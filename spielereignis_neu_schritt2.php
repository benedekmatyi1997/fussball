<?php
require_once ("class.spielereignis.php");
require_once ("smarty.inc.php");
require_once ("class.match.php");
require_once ("class.spieler2team.php");
$smarty=new Smarty();
$match=new Match(filter_input(INPUT_POST, "match"));
$spieler2team_array=Spieler2Team::getSpielerForDate($match->getTeam1()->getId(), $match->getTeam2()->getId(), $match->getZeitpunkt());
$team_id=-1;
$spieler2team_smarty=array();
foreach ($spieler2team_array as $spieler2team) 
{
    $spieler2team_daten=array();
    if($team_id != $spieler2team["team"]->getId())
    {
        $spieler2team_daten["disabled"]=1;
        $team_id=$spieler2team["team"]->getId();
        $spieler2team_daten["team_name"]=$spieler2team["team"]->getName();
    }
    $spieler2team_daten["id"]=$spieler2team["spieler"]->getId();
    $spieler2team_daten["name"]=$spieler2team["spieler"]->getName();
    array_push($spieler2team_smarty,$spieler2team_daten);
}

$smarty->assign("spieler_daten",$spieler2team_smarty);
$smarty->assign("match",filter_input(INPUT_POST, "match"));
$smarty->display("spielereignis_neu_schritt2.tpl");


