<?php
require("function.php");
$q=$_GET["q"];
$t=$_GET["t"];

db_connect();

$resultArray = getTeams($q);

$homeTeam = $resultArray['homeTeam'];
$awayTeam = $resultArray['awayTeam'];

if ($t == 0) {
    echo $homeTeam;
    
}elseif ($t == 1) {
    echo $awayTeam;
    
}elseif ($t == 2) {
    echo $resultArray['homeTeamId'];
    
}else{
    echo $resultArray['awayTeamId'];
    
}

db_close();
?>
