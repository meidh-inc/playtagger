<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php 

$startDate = "2013-03-01";


$get_tags = mysql_query("SELECT tagID, type, userID, player FROM tags WHERE dateTagged >= '{$startDate}' ");

$count_tags = mysql_num_rows($get_tags);
echo $count_tags."<br><br>";
while($array_tags = mysql_fetch_array($get_tags)){
    
    
    
    echo $array_tags['tagID'].",".$array_tags['userID'].",".$array_tags['player'].",".$array_tags['type']."<br>";
    
    
    
    
    
    
}





















?>