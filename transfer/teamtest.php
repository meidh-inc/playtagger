<?php
//$db_old = mysql_connect("localhost", "root", "green2011");
$db_old = mysql_connect("rugbyapp2.db.8540667.hostedresource.com", "rugbyapp2", "Rwa!8472");
if (!$db_old) {
	die("Database connection failed: " . mysql_error());
}
//$db_new = mysql_connect("localhost", "root", "green2011", true);
$db_new = mysql_connect("playtaggerv2.db.8540667.hostedresource.com", "playtaggerv2", "PTedn!2013", true);
if (!$db_new) {
	die("Database connection failed: " . mysql_error());
}

$db_select_old = mysql_select_db('rugbyapp2', $db_old);
if (!$db_select_old) {
	die("Database selection failed: " . mysql_error());
}
$db_select_new = mysql_select_db('playtaggerv2', $db_new);
if (!$db_select_new) {
	die("Database selection failed: " . mysql_error());
}




$get_teams = mysql_query("SELECT * FROM users", $db_old);


while($array_teams = mysql_fetch_array($get_teams)){
    
    $get_teamID = mysql_query("SELECT * FROM teams WHERE name = '{$array_teams['teamName']}' ", $db_old);
    
    $array_teamID = mysql_fetch_array($get_teamID);
    
    
    
    
    echo $array_teams['teamName']." - ".$array_teamID['teamID']."<br>";
    
}

?>