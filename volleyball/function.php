<?php require_once("includes/session.php"); ?>
<?php

date_default_timezone_set('UTC');

function createUser($pass) {
    db_connect();
    
    $pass = md5($pass);
    
    $query = "INSERT INTO users VALUES ('','demoadmin','$pass','0','9','2012-07-26','2016-07-26','None','0','0','Demo','Admin','Admin','99','22','99','75','200','4.0','220','400','22.5','96.5','NONE','')";
    $result = mysql_query($query);
    
    if ($result) {
        return "SUCCESS";
    }
    
    else {
        return "FAIL";
    }
    
    db_close();
}

function db_connect() {
    //DB Connection stuff will go here
    /*
   
    //--local
    $server="localhost";
    $user="root";
    $pass="green2011";
    */
    //--live
    $server="ptvolleyball.db.8540667.hostedresource.com";
    $user="ptvolleyball";
    $pass="Green!2011";
  
    $connect = mysql_connect($server, $user, $pass);
    
    
    if (!$connect) {
        die('Could not connect: ' . mysql_error());
    }
    
    @mysql_select_db("ptvolleyball") or die("Unable to select database");
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
    
    if ($urlID == "none") {
       // Prepare query and number results
       $result = mysql_query("SELECT * FROM teams");
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
        $result = mysql_query("SELECT * FROM gameslist WHERE ytchar = '$urlID'");
        $num = mysql_numrows($result);
        
        if ($num == 1) {
            $homeTeam = mysql_result($result,0,"homeTeam");
            $awayTeam = mysql_result($result,0,"awayTeam");
            $homeTeamId = mysql_result($result,0,"homeTeamId");
            $awayTeamId = mysql_result($result,0,"awayTeamId");
            
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
        
        $result = mysql_query("SELECT * FROM users WHERE userLevel = 'player' AND firstName != '' ORDER BY lastName DESC");
        $num = mysql_num_rows($result);
        
        $playerArray = array();
        
        for ($i = 0; $i < $num; $i++) {
            $firstName = mysql_result($result,$i,"firstName");
            $lastName = mysql_result($result,$i,"lastName");
            $userID = mysql_result($result,$i,"id");
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
    $date = date('Y-m-d H:m:s');
    
    $query = "INSERT INTO tags VALUES('','$gameURL','$what','$userID','$time','$player','$team','$date','$current_userid')";
    mysql_query($query);
    
    db_close();
}

function getTags($gameURL) {
    if (!$gameURL) {
        echo "No game URL passed!";
    } else {
        db_connect();
        
        $result = mysql_query("SELECT * FROM tags WHERE gameURL='$gameURL' ORDER BY 0+time ASC");
        $num = mysql_num_rows($result);
        
        if ($num == 0) {
            echo "<center>No Tags Available</center>";
        } else {
            echo "<table id=\"upcomingTagsTable\" width=\"370\" border=\"0\">";
            for ($i=0; $i < $num; ++$i) {
                
                $pid = mysql_result($result,$i,"userID");
                $time = mysql_result($result,$i,"time");
                $player = mysql_result($result,$i,"player");
                $team = mysql_result($result,$i,"team");
                $event = mysql_result($result,$i,"type");
                $tagID = mysql_result($result,$i,"tagID");
                $taggerID = mysql_result($result,$i,"taggerID");
                
                $get_teamid = mysql_query("SELECT teamID from teams WHERE name = '{$team}' ");
                $array_teamid = mysql_fetch_array($get_teamid);
                $tid = $array_teamid['teamID'];
                
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
                $count_flags = mysql_query("SELECT ID FROM flags WHERE tagID = '{$tagID}' AND flaggerID = '{$userid}' ");
                $flagCount = mysql_num_rows($count_flags);
                if($flagCount >= 1){
                    $button = "<span style=\"font-size:10px;\">FLAGGED</span>";
                }else{
                    //nothing
                }
                
                echo "<tr align=\"center\" onclick=\"seekTo($time)\" style=\"cursor:pointer\">
                        <td style=\"color:blue;\"><u>$formattedTime</u></td>
                        <td><a href=\"player.php?pid=$pid\">$player</a></td>
                        <td><a href=\"team.php?tid=$tid\">$team</a></td>
                        <td>$event</td>
                        <td>
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

function writeLog($event, $info ="n/a", $user = "anonymous") {
    
    db_connect();
    
    $date = date('Y-m-d H:m:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $query = "INSERT INTO logs VALUES('', '$event', '$info', '$user', '$ip', UTC_TIMESTAMP())";
    mysql_query($query);
    
    db_close();

}

?>