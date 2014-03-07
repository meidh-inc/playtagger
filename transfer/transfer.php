<?php
//Generate connections to databases

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


/*
//transfer the USERSLOG table

$transferUserLog = mysql_query("SELECT * FROM users", $db_old);
while($array_transferUserLog = mysql_fetch_array($transferUserLog)){
    
    //input data into new table
    $insertUserLog = mysql_query("INSERT INTO userlog (
                               userID,
                               email,
                               verified,
                               userLevel,
                               hashed_password,
                               firstName,
                               lastName,
                               signup_date,
                               last_login,
                               added_by,
                               invited_on,
                               paidDate,
                               expDate
                               )VALUES(
                               '{$array_transferUserLog['id']}',
                               '{$array_transferUserLog['email']}',
                               '{$array_transferUserLog['verified']}',
                               '{$array_transferUserLog['userLevel']}',
                               '{$array_transferUserLog['hashed_password']}',
                               '{$array_transferUserLog['firstName']}',
                               '{$array_transferUserLog['lastName']}',
                               '{$array_transferUserLog['signup_date']}',
                               '{$array_transferUserLog['last_login']}',
                               '{$array_transferUserLog['added_by']}',
                               '{$array_transferUserLog['invited_on']}',
                               '{$array_transferUserLog['paidDate']}',
                               '{$array_transferUserLog['expDate']}'
                               )", $db_new);
}

//who's missing
$transferUserLog = mysql_query("SELECT id, firstName FROM users", $db_old);
    while($array_transferUserLog = mysql_fetch_array($transferUserLog)){
        $check_new = mysql_query("SELECT userID FROM userinfo WHERE userID = '{$array_transferUserLog['id']}' ", $db_new);
        $count = mysql_num_rows($check_new);
        if($count < 1){
            echo $array_transferUserLog['id']." - ".$array_transferUserLog['firstName']."<br>";
        }
        
    }
*/


/*
//transfer the USERINFO table
$allowed_users = array(5, 28, 32, 38, 50);
$get_userlog = mysql_query("SELECT * FROM userlog WHERE userID IN(5, 28, 32, 38, 50)", $db_new);
while($array_userlog = mysql_fetch_array($get_userlog)){
    
    
    echo $userlogname."<br>";
}

*/
/*
$transferUserInfo = mysql_query("SELECT * FROM users", $db_old);
while($array_transferUserInfo = mysql_fetch_array($transferUserInfo)){
    
    $sportID = 1; //1 is for Rugby
    
    //get teamID
    $get_teamID = mysql_query("SELECT * FROM teams WHERE name = '{$array_transferUserInfo['teamName']}' ", $db_old);
    $array_teamID = mysql_fetch_array($get_teamID);
    
    $id = $array_transferUserInfo['id'];
    $teamid = $array_teamID['teamID'];
    $teamgender = $array_transferUserInfo['teamGender'];
    $birth = $array_transferUserInfo['birthday'];
    $hsgrad = $array_transferUserInfo['hsGrad'];
    $colgrad = $array_transferUserInfo['colGrad'];
    $about = $array_transferUserInfo['aboutText'];
    
    
    //input data into new table
    $insertUsersInfo = mysql_query("INSERT INTO userinfo (
                               userID,
                               sportID,
                               teamID,
                               teamGender,
                               birthday,
                               hsGrad,
                               colGrad,
                               aboutText
                               )VALUES(
                               '{$id}',
                               '{$sportID}',
                               '{$teamid}',
                               '{$teamgender}',
                               '{$birth}',
                               '{$hsgrad}',
                               '{$colgrad}',
                               '{$about}'
                               )", $db_new);
   
}
*/

/*
//transfer the TEAMS table

$transferTeams = mysql_query("SELECT * FROM teams", $db_new);
while($array_transferTeams = mysql_fetch_array($transferTeams)){
    
    $sportID = 1; //1 is for Rugby
    
    //input data into new table
    $insertTeams = mysql_query("INSERT INTO teams (
                               teamID,
                               sportID,
                               adminID,
                               name,
                               link,
                               city,
                               state,
                               country,
                               level,
                               about
                               )VALUES(
                               '{$array_transferTeams['teamID']}',
                               $sportID,
                               '{$array_transferTeams['admin']}',
                               '{$array_transferTeams['name']}',
                               '{$array_transferTeams['teamURL']}',
                               '{$array_transferTeams['city']}',
                               '{$array_transferTeams['state']}',
                               '{$array_transferTeams['country']}',
                               '{$array_transferTeams['teamLevel']}',
                               '{$array_transferTeams['aboutText']}'
                               )", $db_new);
    
    /*$insertTeams = mysql_query("UPDATE teams SET sportID = '$sportID'", $db_new);*/
    /*
}


/*
//transfer GAMES table

$transferGames = mysql_query("SELECT * FROM gameslist", $db_old);
while($array_transferGames = mysql_fetch_array($transferGames)){
    
    $sportID = 1; //1 is for Rugby
    
    //input data into new table
    $insertGames = mysql_query("INSERT INTO games (
                               gameID,
                               sportID,
                               hometeamID,
                               awayteamID,
                               hometeamScore,
                               awayteamScore,
                               gender,
                               city,
                               state,
                               country,
                               date,
                               koHR,
                               koMIN,
                               koAP,
                               link,
                               ytchar,
                               status
                               )VALUES(
                               '{$array_transferGames['id']}',
                               $sportID,
                               '{$array_transferGames['homeTeamId']}',
                               '{$array_transferGames['awayTeamId']}',
                               '{$array_transferGames['homeScore']}',
                               '{$array_transferGames['awayScore']}',
                               '{$array_transferGames['gameGender']}',
                               '{$array_transferGames['gameCity']}',
                               '{$array_transferGames['gameState']}',
                               '{$array_transferGames['gameCountry']}',
                               '{$array_transferGames['gameDay']}',
                               '{$array_transferGames['koHR']}',
                               '{$array_transferGames['koMIN']}',
                               '{$array_transferGames['koAP']}',
                               '{$array_transferGames['gameURL']}',
                               '{$array_transferGames['ytchar']}',
                               '{$array_transferGames['status']}'
                               )", $db_new);
}



//transfer TAGS table

$transferTags = mysql_query("SELECT * FROM tags", $db_old);
while($array_transferTags = mysql_fetch_array($transferTags)){
    
    $sportID = 1; //1 is for Rugby
    
    //get gameID
    $get_gameID = mysql_query("SELECT * FROM gameslist WHERE ytchar = '{$array_transferTags['gameURL']}' ", $db_old);
    $array_gameID = mysql_fetch_array($get_gameID);
    $gameID = $array_gameID['id'];
    
    //get teamID
    $get_teamID = mysql_query("SELECT * FROM teams WHERE name = '{$array_transferTags['team']}' ", $db_old);
    $array_teamID = mysql_fetch_array($get_teamID);
    $teamID = $array_teamID['teamID'];
    
    //input data into new table
    $insertTags = mysql_query("INSERT INTO tags (
                               tagID,
                               sportID,
                               playerID,
                               gameID,
                               teamID,
                               taggerID,
                               playerName,
                               teamName,
                               gameURL,
                               eventName,
                               tagTime,
                               tagDate
                               )VALUES(
                               '{$array_transferTags['tagID']}',
                               $sportID,
                               '{$array_transferTags['userID']}',
                               $gameID,
                               $teamID,
                               '{$array_transferTags['taggerID']}',
                               '{$array_transferTags['player']}',
                               '{$array_transferTags['team']}',
                               '{$array_transferTags['gameURL']}',
                               '{$array_transferTags['type']}',
                               '{$array_transferTags['time']}',
                               '{$array_transferTags['dateTagged']}'
                               )", $db_new);
}


//transfer the MESSAGES table

$transferMess = mysql_query("SELECT * FROM messages", $db_old);
while($array_transferMess = mysql_fetch_array($transferMess)){
    
    $sportID = 1; //1 is for Rugby
    
    //input data into new table
    $insertMess = mysql_query("INSERT INTO messages (
                               messageID,
                               sportID,
                               toID,
                               toTeamID,
                               fromID,
                               sentDate,
                               isRead
                               )VALUES(
                               '{$array_transferMess['id']}',
                               $sportID,
                               '{$array_transferMess['toID']}',
                               '{$array_transferMess['toTeamID']}',
                               '{$array_transferMess['fromID']}',
                               '{$array_transferMess['sentDate']}',
                               '{$array_transferMess['isRead']}'
                               )", $db_new);
}



//transfer the FLAGS table

$transferFlags = mysql_query("SELECT * FROM flags", $db_old);
while($array_transferFlags = mysql_fetch_array($transferFlags)){
    
    $sportID = 1; //1 is for Rugby
    
    //get taggerID
    $get_taggerID = mysql_query("SELECT * FROM tags WHERE tagID = '{$array_transferFlags['tagID']}' ", $db_old);
    $array_taggerID = mysql_fetch_array($get_taggerID);
    
    //input data into new table
    $insertFlags = mysql_query("INSERT INTO flags (
                               flagID,
                               sportID,
                               tagID,
                               flaggerID,
                               taggerID,
                               playerID,
                               flagTime,
                               flagType
                               )VALUES(
                               '{$array_transferFlags['ID']}',
                               $sportID,
                               '{$array_transferFlags['tagID']}',
                               '{$array_transferFlags['flaggerID']}',
                               '{$array_taggerID['taggerID']}',
                               '{$array_transferFlags['playerID']}',
                               '{$array_transferFlags['flagTime']}',
                               '{$array_transferFlags['type']}'
                               )", $db_new);
}

*/
/*
//create SPORTS table

$rugby_events = array("Score","Assist","Tackle","Nice Run","Won Possession","Kick");
$vball_events = array("Service Ace","Kill","Single Block","Block Assist","Dig","Set Assist");

foreach($rugby_events as $event){
    
    $sportID = 1;
    $sportName = "Rugby";
    $eventName = $event;
    //echo $event . "<br>";
    
    //input data into new table
    $insertSports = mysql_query("INSERT INTO sports (
                               sportID,
                               sportName,
                               sportEvent
                               )VALUES(
                               '$sportID',
                               '$sportName',
                               '$eventName'
                               )", $db_new);
}
foreach($vball_events as $event){
    
    $sportID = 2;
    $sportName = "Volleyball";
    $eventName = $event;
    //echo $event . "<br>";
    
    //input data into new table
    $insertSports = mysql_query("INSERT INTO sports (
                               sportID,
                               sportName,
                               sportEvent
                               )VALUES(
                               '$sportID',
                               '$sportName',
                               '$eventName'
                               )", $db_new);
}
*/

?>