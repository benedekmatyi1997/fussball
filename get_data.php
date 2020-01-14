<?php

$classes=array("match","spieler","team","spieler2team","spielereignis","stadion","team2stadion");
$class=strtolower(filter_input(INPUT_GET,"class"));
$id=filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);

if(in_array($class, $classes) && $id)
{
    require_once ("class.".$class.".php");
    $class=ucfirst($class);
    $object=new $class($id);
    echo(json_encode($object->getAsArray()));
}
else
{
    echo("ERROR");
}
