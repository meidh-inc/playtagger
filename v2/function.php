<?php require_once("includes/session.php"); ?>
<?php

date_default_timezone_set('UTC');

function db_connect() {
    //DB Connection stuff will go here
    
   /*
    //--local
    $server="localhost";
    $user="root";
    $pass="green2011";
   */ 
    //--live
    $server="playtaggerv2.db.8540667.hostedresource.com";
    $user="playtaggerv2";
    $pass="PTedn!2013";
  
    $connect = mysql_connect($server, $user, $pass);
    
    
    if (!$connect) {
        die('Could not connect: ' . mysql_error());
    }
    
    @mysql_select_db("playtaggerv2") or die("Unable to select database");
}

function db_close() {
    mysql_close();
}

function clean($str) {
    $str = @trim($str);
    
    if (version_compare(phpversion(),'4.3.0') >= 0) {
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        if (@mysql_ping()) {
            $str = mysql_real_escape_string($str);
        }
        else {
            $str = addslashes($str);
        }
    }
    else {
        if (!get_magic_quotes_gpc()) {
            $str = addslashes($str);
        }
    }
    
    return $str;    
}


function getTeams($urlID = "none") {
        // Connect to the DB by using above function
    db_connect();
    $sportID = $_SESSION['sport'];
    if ($urlID == "none") {
       // Prepare query and number results
       $result = mysql_query("SELECT * FROM teams WHERE sportID = '{$sportID}' ");
        $num = mysql_numrows($result);
        $i = 0;
    
    
        // No teams = error
        if ($num == 0) {
            // Stop the script
            die('Database error: Team Database contains no teams');
        }
    
        else {
            //Output loop
            $resultArray = array();
        
            while ($i < $num) {
                $name = mysql_result($result,$i,"name");
       
                $resultArray[] = $name;
            
            
                $i++;
            }
        
        }
    }
    
    else {
        $result = mysql_query("SELECT * FROM games WHERE sportID = '{$sportID}' AND ytchar = '$urlID'");
        $num = mysql_numrows($result);
        
        if ($num == 1) {
            
            $homeTeamId = mysql_result($result,0,"hometeamID");
            $awayTeamId = mysql_result($result,0,"awayteamID");
            
            $resulthome = mysql_query("SELECT * FROM teams WHERE sportID = '{$sportID}' AND teamID = '$homeTeamId'");
            $resultaway = mysql_query("SELECT * FROM teams WHERE sportID = '{$sportID}' AND teamID = '$awayTeamId'");
            $homeTeam = mysql_result($resulthome,0,"name");
            $awayTeam = mysql_result($resultaway,0,"name");
            
            
            $resultArray = array();
            $resultArray['homeTeamId'] = $homeTeamId;
            $resultArray['homeTeam'] = $homeTeam;
            $resultArray['awayTeamId'] = $awayTeamId;
            $resultArray['awayTeam'] = $awayTeam;
        }
        
        else {
            die("An error occured: Invalid URL ID");
        }
        
    }
    
    return $resultArray;
    db_close(); 
    
}
/*
function getGames() {
    db_connect();
    
    // Prepare the query and number results
    $result = mysql_query("SELECT * FROM gameslist");
    $num = mysql_numrows($result);
    $i = 0;
    
    // No Games = error and kill script
    if ($num == 0) {
        die('Database error: Game Database contains no games');
    }
    
    // If there are games, continue..
    else {
        // Create the array that the function will return
        $resultArray = array();
        
        // The loop that will fill the array with the video ID's
        for ($i = 0; $i < $num; $i++) {
            $resultArray[$i] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
        
        return $resultArray;
    }
    
    db_close();
}
 */
function getPlayers($teamID = "none") {
  /*  if ($teamID == "none") {
        db_connect();
        
        $query = "SELECT * FROM users WHERE userLevel = 'player' AND firstName != '' ";
        $result = mysql_query($query);
        $num = mysql_num_rows($result);
        
        $playerArray = array();
        
        for ($i = 0; $i < $num; $i++) {
            $playerArray[$i] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
        return $playerArray;
    }
    
    else {  
  */      db_connect();
        
        $result = mysql_query("SELECT * FROM userlog WHERE userLevel = 'player' AND firstName != '' ORDER BY lastName DESC");
        $num = mysql_num_rows($result);
        
        $playerArray = array();
        
        for ($i = 0; $i < $num; $i++) {
            $firstName = mysql_result($result,$i,"firstName");
            $lastName = mysql_result($result,$i,"lastName");
            $userID = mysql_result($result,$i,"userID");
            $fullName = $firstName . " " . $lastName;
            
            $playerArray[] = $userID;
            $playerArray[] = $fullName;            
        }
        
        return $playerArray;
    
        db_close;
  //  }
}

 function writeTag($time, $player, $what, $userID, $gameURL, $team) {
    // Tag Writing Code will go here
    $current_userid = $_SESSION['userid'];
    
    db_connect();
    date_default_timezone_set('America/Chicago');
    $date = date('Y-m-d H:i:s');
    //lookup gameID
    $get_gameID = mysql_query("SELECT gameID, sportID FROM games WHERE ytchar = '{$gameURL}' ");
    $array_gameID = mysql_fetch_array($get_gameID);
    //lookup teamID
    //currently loading the tid on the fly when loading tags...
    
    if($_SESSION['userid'] >= '1'){
        //                          tagID,        sportID,            playerID,       gameID,            teamID,     taggerID,   playerName, teamName, gameURL, eventName, tagTime, tagDate
    $query = "INSERT INTO tags VALUES('','{$array_gameID['sportID']}','$userID','{$array_gameID['gameID']}','0','$current_userid','$player','$team','$gameURL','$what','$time','$date')";
    mysql_query($query);
    }else{
        $ipaddress = $_SERVER["REMOTE_ADDR"];
        $file = fopen("log.txt","a");
        fwrite($file,"Bad Tag Prevented, $date, from $ipaddress \n");
        fclose($file);
    }
    
    
    
    db_close();
}

function getTags($gameURL) {
    $sportID = $_SESSION['sportID'];
    if (!$gameURL) {
        echo "No game URL passed!";
    } else {
        db_connect();
        
        $result = mysql_query("SELECT * FROM tags WHERE gameURL='$gameURL' ORDER BY 0+tagTime ASC");
        //$result = "SELECT * FROM tags WHERE gameURL='$gameURL' ORDER BY 0+tagTime ASC";
        //$result2 = mysql_query($result) or die($result."<br/><br/>".mysql_error());
        $num = mysql_num_rows($result);
        
        if ($num == 0) {
            echo "<center><span style=\"font-family:sans-serif;\">No Tags Available</span></center>";
        } else {
            echo "<table id=\"upcomingTagsTable\" style=\"text-align:left;max-width:330px;width:330px;font-family:sans-serif;\" border=\"0\">";
            for ($i=0; $i < $num; ++$i) {
                
                $pid = mysql_result($result,$i,"playerID");
                $time = mysql_result($result,$i,"tagTime");
                $player = mysql_result($result,$i,"playerName");
                $team = mysql_result($result,$i,"teamName");
                $event = mysql_result($result,$i,"eventName");
                $tagID = mysql_result($result,$i,"tagID");
                $taggerID = mysql_result($result,$i,"taggerID");
                //$tid = mysql_result($result,$i,"teamID");
                //get teamID for link
                $get_teamID = mysql_query("SELECT sportID, teamName FROM tags WHERE tagID = '{$tagID}' ");
                $array_teamID = mysql_fetch_array($get_teamID);
                $get_tid = mysql_query("SELECT teamID FROM teams WHERE sportID = '{$array_teamID['sportID']}' AND name = '{$array_teamID['teamName']}' ");
                $array_tid = mysql_fetch_array($get_tid);
                $tid = $array_tid['teamID'];
                
                
                if ($time < 3600) {
                    $formattedTime = gmdate("i:s", $time);
                } else {
                    $formattedTime = gmdate("H:i:s", $time);
                }
                
                //prep to echo flag button or delete button
                $userid = $_SESSION['userid'];
                if($pid == $userid){  //The current user is the player in the tag, deliver delete button
                    $button = "<input type=\"submit\" name=\"removeTag\" value=\"Delete\" title=\"Remove this tag\" style=\"font-size:10px;border:solid thin grey;background-color:maroon;color:white;cursor:pointer\">";
                }elseif($taggerID == $userid){  //The current user is the tag creator, deliver delete button
                    $button = "<input type=\"submit\" name=\"removeTag\" value=\"Delete\" title=\"Remove this tag\" style=\"font-size:10px;border:solid thin grey;background-color:maroon;color:white;cursor:pointer\">";
                }else{  //The current user is not involved with this tag, deliver flag button
                    $button = "<input type=\"submit\" name=\"flagTag\" value=\"Flag\" title=\"Flag this tag as incorrect\" style=\"font-size:10px;border:solid thin grey;background-color:black;color:white;cursor:pointer\">";
                }
                
                //If this user has already flagged this tag, print a message instead of a button
                $count_flags = mysql_query("SELECT flagID FROM flags WHERE tagID = '{$tagID}' AND flaggerID = '{$userid}' ");
                $flagCount = mysql_num_rows($count_flags);
                if($flagCount >= 1){
                    $button = "<span style=\"font-size:10px;\">FLAGGED</span>";
                }elseif($userid == "xxv"){
                    $button = " ";
                }else{
                    //nothing
                }
                
                if($userid == "xxv"){
                    $playerLink = $player;
                    $teamLink = $team;
                }else{
                    $playerLink = "<a href=\"player.php?pid=$pid\">$player</a>";
                    $teamLink = "<a href=\"team.php?tid=$tid\">$team</a>";
                }
                
                echo "<tr align=\"left\" onclick=\"seekTo($time)\" style=\"cursor:pointer\">
                        <td style=\"padding:2px;width:25px;max-width:25px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow: ellipsis;color:blue;\" title=\"$formattedTime\"><u>$formattedTime</u></td>
                        <td style=\"padding:2px;width:50px;max-width:50px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow: ellipsis;\" title=\"$player\">$playerLink</td>
                        <td style=\"padding:2px;width:40px;max-width:40px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow: ellipsis;\" title=\"$team\">$teamLink</td>
                        <td style=\"padding:2px;width:45px;max-width:45px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow: ellipsis;\" title=\"$event\">$event</td>
                        <td style=\"padding:2px;width:35px;max-width:35px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow: ellipsis;\">
                            <form action=\"video.php\" method=\"post\">
                            $button
                            <input type=\"hidden\" name=\"tagID\" value=\"$tagID\">
                            </form>
                        </td>
                     ";
            }
            echo "</table>";
        }
        db_close();
    }
}

?>