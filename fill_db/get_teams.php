<?php

require_once("api_keys.inc.php");
require_once("../class.team.php");
require_once("../class.region.php");
$ligaid=filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
if(is_numeric($ligaid))
{
    $curl=curl_init();
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_HTTPHEADER,$api_data);
    curl_setopt($curl, CURLOPT_URL, "https://api-football-v1.p.rapidapi.com/v2/teams/league/$ligaid");
    //print_r($api_data);
    if(($content=curl_exec($curl))===false)
    {
        echo(curl_error($curl));
    }
    else
    {
        $parsed_object=json_decode($content);
        echo("<pre>");
        $team_object=new Team();
        $land_object=new Region();
        $land_object->setTyp("land");
        //print_r($parsed_object);
        foreach($parsed_object->api->teams as $team)
        {
            print(PHP_EOL);
            $inserted=0;
            if(!$team->is_national)
            {
                if(!count(Team::getDataByValue("name", $team->name)))
                {
                    $team_object->setName($team->name);
                    $region_array=Region::getDataByValue("name", $team->country);
                    if(!count($region_array))
                    {
                        $land_object->setName($team->country);
                        $land_object->update();
                        print("Land ".$team->country." wurde eingefuegt!".PHP_EOL);
                        $region_array=Region::getDataByValue("name", $team->country);
                    }
                    $team_object->setRegion($region_array[0]);
                    $team_object->update();
                    print("Team ");
                    $inserted=1;
                }
            }
            else 
            {
                if(!count(Region::getDataByValue("name", $team->name)))
                {                
                    $land_object->setName($team->name);
                    $land_object->update();
                    print("Land ");
                    $inserted=1;
                }
            }
            if($inserted)
            {
                print($team->name." wurde eingefuegt!");
            }
            else
            {
                print($team->name." war schon in der Datenbank!");
            }
        }
        echo("</pre>");
    }
}
else
{
    echo("\"$ligaid\" ist keine gültige ID!");
}
//print($ligaid);