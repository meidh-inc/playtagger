<?php
//Generate connections to databases

$db_old = mysql_connect("localhost", "root", "green2011");
if (!$db_old) {
	die("Database connection failed: " . mysql_error());
}
$db_new = mysql_connect("localhost", "root", "green2011", true);
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




$transferUsers = mysql_query("SELECT * FROM users", $db_old);

while($array_transferUsers = mysql_fetch_array($transferUsers)){
    
    
    
    $array_transferUsers['name'];
    
    
    $insertUsers = mysql_query("INSERT INTO users (
                               userID,
                               email,
                               verified,
                               userLevel,
                               hashed_password,
                               firstName,
                               lastName,
                               teamID,
                               teamGender,
                               birthday,
                               hsGrad,
                               colGrad,
                               aboutText,
                               signup_date,
                               last_login,
                               added_by,
                               invited_on,
                               paidDate,
                               expDate
                               )VALUES(
                               $array_transferUsers['id'],
                               $array_transferUsers['email'],
                               $array_transferUsers['verified'],
                               $array_transferUsers['userLevel'],
                               $array_transferUsers['hashed_password'],
                               $array_transferUsers['firstName'],
                               $array_transferUsers['lastName'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name'],
                               $array_transferUsers['name']
                               )", $db_new);
    
    
    
}




//mysql_query('select * from tablename', $db_old);
//mysql_query('select * from tablename', $db_new);



















?>