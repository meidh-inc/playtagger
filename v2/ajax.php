<?php

// Shut's off notice reporting; Allows us to have unused variables
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require("function.php");
$action=$_GET["action"];
$t=$_GET["t"];
$q=$_GET["q"];
$p=$_GET["p"];
$pid=$_GET["pid"];
$u=$_GET["u"];
$team=$_GET["team"];
$w=$_GET["w"];



if ($action == "getPlayers") {
    echo "<div class=\"ui-widget\"><select id=\"tagPlayers\">";
    if ($t == 0) {
        echo "<option value=\"00\">Team not in system</option>";
    }
    
    else {
        $players = getPlayers($t);
        $n = 0;
        
        for ($i=0; $i < count($players); ++$i){
            if ($n == 0) {
                $userID = $players[$i];
                $n++;
            }
            else {
                $name = $players[$i];
                echo "<option value=\"$userID\">$name</option>";
                $n = 0;
            }
            
        }
    }
    echo "</select></div>";

}

else if ($action == "addplayer") {
    require_once("includes/connection.php");
    require_once("includes/functions.php");
    include_once("includes/form_functions.php");
    
    $uid = trim(mysql_prep($_GET["uid"]));
    $fn = trim(mysql_prep($_GET["fn"]));
    $sportid = trim(mysql_prep($_GET["s"]));
    $ln = trim(mysql_prep($_GET["ln"]));
    $ea = trim(mysql_prep($_GET["ea"]));
    
    $timestamp_add = date("Y-m-d h:i:s a");
    
    // check for unique email address
    $query_verify_email = mysql_query("SELECT userID FROM userlog WHERE email = '$ea' ");
    if(mysql_num_rows($query_verify_email) == 0){
        
        
    
    $insertNewPlayer = "INSERT INTO userlog (
			    email, verified, userLevel, firstName, lastName, added_by, invited_on
			    ) VALUES (
			    '{$ea}', 'invited', 'player', '{$fn}', '{$ln}', '{$uid}', '{$timestamp_add}'
                            ) ";
    $result_insertNP = mysql_query($insertNewPlayer);
    
    // test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    echo "Success";
            
            //send invite email
//get user_id and generate key
$get_uidNew = mysql_query("SELECT userID FROM userlog WHERE email = '{$ea}' AND invited_on = '{$timestamp_add}' ");
$array_uidNew = mysql_fetch_array($get_uidNew);
$uidNew = $array_uidNew['userID'];

//insert entry into userinfo for sport
$insert_player_info = "INSERT INTO userinfo(userID, sportID)VALUES('{$uidNew}','{$sportid}')";
$result_player_info = mysql_query($insert_player_info);

//establish invite link
if($sportid == 1){  //rugby
    $inviteLink = "http://www.worldrugbyshop.com/playtagger.html";
}elseif($sportid == 2){ //volleyball
    $inviteLink = "http://volleyball.playtagger.com/";
}elseif($sportid == 3){  //football
    $inviteLink = "http://www.sportsspotlight.com/playtagger.cfm";
}elseif($sportid == 4){  //basketball
    $inviteLink = "http://basketball.playtagger.com/";
}

//Send the email
require_once("phpmailer/class.phpmailer.php");
$mail = new PHPMailer();

$body = "
	<h2>$fn, you've been invited to join PlayTagger!</h2>
        PlayTagger is your hassle-free, online highlight reel.  Whether you are looking to play in college, the Pros, or a National team, PlayTagger is the fastest, easiest way to get seen!
        Try PlayTagger FREE for a limited time.
	Please follow the link to <a href=\"$inviteLink\">SIGNUP</a>.
	<br>
	<h4>Thank You for joining!</h4>
	-the PlayTagger Team
";

$mail->Host       = "smtpout.secureserver.net";  // this relay server was recommended by godaddy via an email on 9/20/12
$mail->Port       = 25; // set the SMTP port for the server, godaddy says port 25

$mail->SetFrom("info@playtagger.com");
$mail->AddReplyTo("eric.nelson@meidh.com","Eric Nelson");
$mail->Subject    = "Confirm Player Connect Signup and Verify Email";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress($ea);
if(!$mail->Send()) {  //Email Failed
    echo "Mailer Error: " . $mail->ErrorInfo ."/n";
} else {  //Email Success
    //echo "Message sent! to: Someone at ".$email_to." \n";
    //redirect_to('index.php');
}
            
            
            
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		//do nothing
	    }else{
		echo "ERROR";
	    }
	}
    }
}


else if ($action == "getTeams") {
    echo "<select id=\"tagTeams\" onchange=\"updateTagPlayers();\">";
    if (!$q) {
        echo "<option value=\"00\">No URL ID Passed</option>";
    }
    
    else {
        $teams = getTeams($q);
        echo "<option value=\"$teams[homeTeamId]\">$teams[homeTeam]</option>";
        echo "<option value=\"$teams[awayTeamId]\">$teams[awayTeam]</option>";
    }
    echo "</select>";
    
}

// This is the write tag AJAX script, it calls the Function writeTag from function.php;
// NOTE: the 0 is just a place holder for the User ID (CHANGE ONCE SESSIONS ARE UTILIZED)
else if ($action == "writeTag") {
    if (!$t || !$p || !$team || !$w || !$pid || !$u) {
        echo "ERROR";
    }
    else {
        writeTag($t, $p, $w, $pid, $u, $team);
        echo "Success";
    }
}

else if ($action == "getTags") {
    if (!$q) {
        echo "<tr colspan=\"4\" align=\"center\">Error no URL Passed</tr>";
    }
    else {
        getTags($q);
    }
}

else if ($action == "getstates") {
    require_once("includes/connection.php");
    //look for $sv
    $sv=$_GET["sv"];
    if($sv == "xx"){
        $sv="";
    }elseif($sv == "all"){
        echo "<select id=\"searchState\" name=\"searchState\">";
        echo "<option value=\"all\">All States</option>";
        $get_states = mysql_query("SELECT state FROM games GROUP BY state ");
        while($array_states = mysql_fetch_array($get_states)){
            echo "<option value=\"".$array_states['state']."\">".$array_states['state']."</option>";
        }
        echo"</select>";
    }else{
        echo"<select id=\"searchState\" name=\"searchState\">";
        echo"<option value=\"all\">All States</option>";
        $get_statesb = mysql_query("SELECT state FROM games WHERE country = '{$sv}' GROUP BY state");
        while($array_statesb = mysql_fetch_array($get_statesb)){
            echo "<option value=\"".$array_statesb['state']."\">".$array_statesb['state']."</option>";
        }
        echo"</select>";
    }
}

else {
    echo "hello";
}

?>