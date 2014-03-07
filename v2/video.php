<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php require_once("function.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];  //if user is not logged in: $userid = "xxv"
    $sportID = $_SESSION['sport'];
    $sportName = $_SESSION['sportName'];

// Get userLevel if logged in
if($userid != "xxv"){
    $get_userLevel = mysql_query("SELECT userLevel FROM userlog WHERE userID = '{$userid}' ");
    $array_userLevel = mysql_fetch_array($get_userLevel);
    $userLevel = $array_userLevel['userLevel'];
}else{
    $userLevel = "notloggedin";
}
if(isset($_POST['search'])){
    
//DETERMINE USE OF 'WHERE'  !!NOTE: I use 'WHERE' all the time for establishing the sport I'm searching through
    //check to see whether or not I print the 'WHERE' into the mysql query below (all or narrowed down?)
    if( ($_POST['searchCountry'] == "all") && (!isset($_POST['searchState'])) && ($_POST['teamName'] == "") && ($_POST['playerName'] == "") && ($_POST['minday'] == "") && ($_POST['maxday'] == "") && (!isset($_POST['eventTypes'])) && (!isset($_POST['gameGender'])) ){
	//nothing has been narrowed down, get all games
	//$where = "";
	$where = "WHERE sportID = '".$sportID."'";
    }else{
	//the list is narrowed by something, print 'WHERE' in query
	//$where = "WHERE";
	$where = "WHERE sportID = '".$sportID."' AND";
    }

    

//BY COUNTRY
    $searchCountry = $_POST['searchCountry'];  	//value is either 'all' or specified
	
	//DOES NOT NEED 'AND'
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($searchCountry == "all"){
	    $s_country = "";
	}else{
	    $s_country = "country = '".$searchCountry."'";
	}
    
    
//BY STATE
    if(!isset($_POST['searchState'])){		//value may not be set, set to 'all' if not set
	$searchState = "all";
    }else{
	$searchState = $_POST['searchState'];
    }						//now, value is either 'all' or specified
	
	//Determine 'AND' requirement
	if($s_country == ""){
	    $and_state = "";
	}else{
	    $and_state = "AND";
	}
	
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($searchState == "all"){
	    $s_state = "";
	    $and_state = "";
	}else{
	    $s_state = "state = '".$searchState."'";
	}
    
    
//BY TEAM NAME
    $teamName = $_POST['teamName'];		//value is either blank or specified
	
	
	//Determine 'AND' requirement
	if($s_state == ""){
	    $and_teamName = "";
		if($s_country == ""){
		    $and_teamName = "";
		}else{
		    $and_teamName = "AND";
		} 
	}else{
	    $and_teamName = "AND";
	}
	
	
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($teamName == ""){
	    $s_teamName = "";
	    $and_teamName = "";
	}else{
	    $s_teamName = "( hometeamID = '".$teamName."' OR awayteamID = '".$teamName."' )";
	}
    
    
//BY PLAYER NAME
    $playerName = $_POST['playerName'];  	//value is either blank or raw(firstName_lastName)
	
	//Determine 'AND' requirement
	if($s_teamName == ""){
	    $and_playerName = "";
		if($s_state == ""){
		    $and_playerName = "";
			if($s_country == ""){
			    $and_playerName = "";
			}else{
			    $and_playerName = "AND";
			}
		}else{
		    $and_playerName = "AND";
		}
	}else{
	    $and_playerName = "AND";
	}
	
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($playerName == ""){
	    $s_playerName = "";
	    $and_playerName = "";
	}else{
	    $playerTeamQuery = "SELECT gameURL FROM tags WHERE sportID = '{$sportID}' AND playerName = '".$playerName."'";
	    $s_playerName = "ytchar IN(".$playerTeamQuery.") ";
	}
    
    
//BY MIN DATE
    $minday = $_POST['minday'];			//value is either blank or specified
	
	//Determine 'AND' requirement
	if($s_playerName == ""){
	    $and_minday = "";
		if($s_teamName == ""){
		    $and_minday = "";
			if($s_state == ""){
			    $and_minday = "";
				if($s_country == ""){
				    $and_minday = "";
				}else{
				    $and_minday = "AND";
				}
			}else{
			    $and_minday = "AND";
			}
		}else{
		    $and_minday = "AND";
		}
	}else{
	    $and_minday = "AND";
	}
	
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($minday == ""){
	    $s_minday = "";
	    $and_minday = "";
	}else{
	    $s_minday = "date >= '".$minday."'";
	}
    
    
//BY MAX DATE
    $maxday = $_POST['maxday'];			//value is either blank or specified
	
	//Determine 'AND' requirement
	if($s_minday == ""){
	    $and_maxday = "";
		if($s_playerName == ""){
		    $and_maxday = "";
			if($s_teamName == ""){
			    $and_maxday = "";
				if($s_state == ""){
				    $and_maxday = "";
					if($s_country == ""){
					    $and_maxday = "";
					}else{
					    $and_maxday = "AND";
					}
				}else{
				    $and_maxday = "AND";
				}
			}else{
			    $and_maxday = "AND";
			}
		}else{
		    $and_maxday = "AND";
		}
	}else{
	    $and_maxday = "AND";
	}
	
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($maxday == ""){
	    $s_maxday = "";
	    $and_maxday = "";
	}else{
	    $s_maxday = "date <= '".$maxday."'";
	}
    
    
//BY EVENT TYPE
    if(!isset($_POST['eventTypes'])){		//value may not be set, set to 'all' if not set
	$array_eventTypes = "all";
    }else{
	$array_eventTypes = implode("','",$_POST['eventTypes']);  //prep array for query below
    }						
	
	//Determine 'AND' requirement
	if($s_maxday == ""){
	    $and_eventTypes = "";
		if($s_minday == ""){
		    $and_eventTypes = "";
			if($s_playerName == ""){
			    $and_eventTypes = "";
				if($s_teamName == ""){
				    $and_eventTypes = "";
					if($s_state == ""){
					    $and_eventTypes = "";
						if($s_country == ""){
						    $and_eventTypes = "";
						}else{
						    $and_eventTypes = "AND";
						}
					}else{
					    $and_eventTypes = "AND";
					}
				}else{
				    $and_eventTypes = "AND";
				}
			}else{
			    $and_eventTypes= "AND";
			}
		}else{
		    $and_eventTypes = "AND";
		}
	}else{
	    $and_eventTypes = "AND";
	}
	
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($array_eventTypes == "all"){
	    $s_eventTypes = "";
	    $and_eventTypes = "";
	}else{
	    $eventTypesQuery = "SELECT gameURL FROM tags WHERE sportID = '{$sportID}' AND eventName IN('".$array_eventTypes."') ";
	    $s_eventTypes = "ytchar IN(".$eventTypesQuery.") ";
	}
	
    
    //echo $array_eventTypes;
    
    
    
//BY GAME GENDER
    if(!isset($_POST['gameGender'])){		//value may not be set, set to 'all' if not set
	$gameGender = "all";
    }else{
	$gameGender = $_POST['gameGender'];  //prep array for query below
    }						
	
	//Determine 'AND' requirement
	if($s_eventTypes == ""){
	    $and_gameGender = "";
		if($s_maxday == ""){
		    $and_gameGender = "";
			if($s_minday == ""){
			    $and_gameGender = "";
				if($s_playerName == ""){
				    $and_gameGender = "";
					if($s_teamName == ""){
					    $and_gameGender = "";
						if($s_state == ""){
						    $and_gameGender = "";
							if($s_country == ""){
							    $and_gameGender = "";
							}else{
							    $and_gameGender = "AND";
							}
						}else{
						    $and_gameGender = "AND";
						}
					}else{
					    $and_gameGender = "AND";
					}
				}else{
				    $and_gameGender = "AND";
				}
			}else{
			    $and_gameGender = "AND";
			}
		}else{
		    $and_gameGender = "AND";
		}
	}else{
	    $and_gameGender = "AND";
	}
	
	//DETERMINE WHAT GOES INTO THE QUERY
	if($gameGender == "all"){
	    $s_gameGender = "";
	    $and_gameGender = "";
	}else{
	    $s_gameGender = " gender = '".$gameGender."' ";
	}
	
    
    
    
    // Get games list for echoing to page According to search parameters
    $get_gameslist = mysql_query("SELECT gameID, hometeamID, awayteamID, gender, date, ytchar FROM games
				  ".$where." ".$s_country."
				  ".$and_state." ".$s_state."
				  ".$and_teamName." ".$s_teamName."
				  ".$and_playerName." ".$s_playerName."
				  ".$and_minday." ".$s_minday."
				  ".$and_maxday." ".$s_maxday."
				  ".$and_eventTypes." ".$s_eventTypes."
				  ".$and_gameGender." ".$s_gameGender."
				  ");
    //next two lines used for error reporting for the above query
    //$result2 = mysql_query($get_gameslist) or die($get_gameslist."<br/><br/>".mysql_error());
    //echo $result2;
    
    // Load starter ytchar
    $get_NEWdefaultYTCHAR = mysql_query("SELECT gameID, hometeamID, awayteamID, gender, date, ytchar FROM games
				  ".$where." ".$s_country."
				  ".$and_state." ".$s_state."
				  ".$and_teamName." ".$s_teamName."
				  ".$and_playerName." ".$s_playerName."
				  ".$and_minday." ".$s_minday."
				  ".$and_maxday." ".$s_maxday."
				  ".$and_eventTypes." ".$s_eventTypes."
				  ".$and_gameGender." ".$s_gameGender."
				  ORDER BY gameID LIMIT 1");
    $array_NEWdefaultYTCHAR = mysql_fetch_array($get_NEWdefaultYTCHAR);
    $defaultYTCHAR = $array_NEWdefaultYTCHAR['ytchar'];
    
    
}elseif(isset($_GET['pid'])){  //shows this player's games  (should be just their tags in the future)
    
    //get game list for just this player
    $playerQuery = "SELECT gameURL FROM tags WHERE sportID = '{$sportID}' AND playerID = '".$_GET['pid']."'";
    $s_playerName = "ytchar IN(".$playerQuery.") ";
    
    $get_gameslist = mysql_query("SELECT gameID, hometeamID, awayteamID, gender, date, ytchar FROM games
				WHERE sportID = '{$sportID}' AND ".$s_playerName." ");
    
    // Get starter ytchar
    $get_playerdefaultYTCHAR = mysql_query("SELECT gameID, ytchar FROM games WHERE sportID = '{$sportID}' AND ".$s_playerName." ORDER BY RAND() LIMIT 1");
    $array_playerdefaultYTCHAR = mysql_fetch_array($get_playerdefaultYTCHAR);
    $defaultYTCHAR = $array_playerdefaultYTCHAR['ytchar'];
    
}else{
    // Get starter ytchar
    $get_defaultYTCHAR = mysql_query("SELECT gameID, ytchar FROM games WHERE sportID = '{$sportID}' ORDER BY RAND() LIMIT 1");
    $array_defaultYTCHAR = mysql_fetch_array($get_defaultYTCHAR);
    $defaultYTCHAR = $array_defaultYTCHAR['ytchar'];
    
    // Get games list for echoing to page
    $get_gameslist = mysql_query("SELECT gameID, hometeamID, awayteamID, gender, date, ytchar FROM games WHERE sportID = '{$sportID}'");    
}


//Save the game details to the database
if (isset($_POST['saveNewGame'])) { // Form has been submitted.
    $errors_newGame = array();

    // perform validations on the form data
    $required_fields_newGame = array('homeTeam', 'homeScore', 'awayTeam', 'awayScore',
			    'gameGender', 'gameCity', 'gameState', 'gameCountry',
			    'gameDay', 'koHR', 'koMIN', 'koAP', 'gameURL' );
    $errors_newGame = array_merge($errors_newGame, check_required_fields($required_fields_newGame, $_POST));
    $fields_with_lengths_newGame = array('homeTeam' => 100, 'awayTeam' => 100, 'gameURL' => 255);
    $errors_newGame = array_merge($errors_newGame, check_max_field_lengths($fields_with_lengths_newGame, $_POST));
    
    //Get team names from the passed Team ID's
    $homeID = trim(mysql_prep($_POST['homeTeam']));
    $awayID = trim(mysql_prep($_POST['awayTeam']));
    
    $get_homeName = mysql_query("SELECT name FROM teams WHERE sportID = '{$sportID}' AND teamID = '{$homeID}' ");
    $array_homeName = mysql_fetch_array($get_homeName);
    $homeTeam = $array_homeName['name'];
    
    $get_awayName = mysql_query("SELECT name FROM teams WHERE sportID = '{$sportID}' AND teamID = '{$awayID}' ");
    $array_awayName = mysql_fetch_array($get_awayName);
    $awayTeam = $array_awayName['name'];
    
    // clean up the other form data before putting it in the database
    $homeScore = trim(mysql_prep($_POST['homeScore']));
    $awayScore = trim(mysql_prep($_POST['awayScore']));
    $gameGender = trim(mysql_prep($_POST['gameGender']));
    $gameCity = trim(mysql_prep($_POST['gameCity']));
    //$gameState = trim(mysql_prep($_POST['gameState']));
    $gameState = "IA";
    $gameCountry = "USA";
    //$gameCountry = trim(mysql_prep($_POST['gameCountry']));
    $gameDay = trim(mysql_prep($_POST['gameDay']));
    $koHR = trim(mysql_prep($_POST['koHR']));
    $koMIN = trim(mysql_prep($_POST['koMIN']));
    $koAP = trim(mysql_prep($_POST['koAP']));
    $gameURL = $_POST['gameURL'];
    
    
    if($gameURL != ""){
    //generate the ytchar
    $url = $gameURL;
    function youtube_id_from_url($url) {
       $pattern =
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | .*v=        # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        ($|&).*         # if additional parameters are also in query string after video id.
        $%x'
        ;
        $result = preg_match($pattern, $url, $matches);
        if (false !== $result) {
          return $matches[1];
        }
        return false;
    }
    $ytchar = youtube_id_from_url($url);
    }
    
    
    
    // Proceed if there are no errors, else print the errors
    if (empty($errors_newGame)) {
	//Put new game into database
	$insertGame = "INSERT INTO games (
					    sportID, hometeamID, hometeamScore, awayteamID, awayteamScore, gender, city,
					    state, country, date, koHR, koMIN, koAP, link, ytchar
					    ) VALUES (
					    '{$sportID}', '{$homeID}', '{$homeScore}', '{$awayID}', '{$awayScore}', '{$gameGender}',
					    '{$gameCity}', '{$gameState}', '{$gameCountry}', '{$gameDay}', '{$koHR}',
					    '{$koMIN}', '{$koAP}', '{$gameURL}','{$ytchar}'
					    ) ";
	$result_insert = mysql_query($insertGame);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    redirect_to("video.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		redirect_to("video.php");
	    }else{
		$message_newGame = "The information could not be updated.";
		$message_newGame .= "<br />" . mysql_error() . mysql_affected_rows();
	    }
	}
    } else {
	if (count($errors_newGame) == 1) {
	    $message_newGame = "There was 1 error in the form.";
	} else {
	    $message_newGame = "There were " . count($errors_newGame) . " errors in the form.";
	}
    }
}

if(!isset($_POST['saveNewGame'])){
    $homeTeam = "";
    $homeScore = "";
    $awayTeam = "";
    $awayScore = "";
    $gameGender = "";
    $gameCity = "";
    $gameState = "IA";
    $gameCountry = "USA";
    $gameDay = "";
    $koHR = "";
    $koMIN = "";
    $koAP = "";
    $gameURL = "";
    $message_newGame = "";
}


//Save the game details to the database
if (isset($_POST['addNewTeam'])) { // Form has been submitted.
    $errors_newTeam = array();

    // perform validations on the form data
    $required_fields_newTeam = array('teamName', 'teamCity' );
    $errors_newTeam = array_merge($errors_newTeam, check_required_fields($required_fields_newTeam, $_POST));
    $fields_with_lengths_newTeam = array('teamName' => 255, 'teamCity' => 255, 'teamState' => 2, 'teamCountry' => 100);
    $errors_newTeam = array_merge($errors_newTeam, check_max_field_lengths($fields_with_lengths_newTeam, $_POST));
    
    // clean up the form data before putting it in the database
    $teamName = trim(mysql_prep($_POST['teamName']));
    $teamCity = trim(mysql_prep($_POST['teamCity']));
    $teamState = trim(mysql_prep($_POST['teamState']));
    $teamCountry = trim(mysql_prep($_POST['teamCountry']));
    
    
    // Proceed if there are no errors, else print the errors
    if (empty($errors_newTeam)) {
	//Put new game into database
	$insertTeam = "INSERT INTO teams (
					    sportID, name, city, state, country
					    ) VALUES (
					    '{$sportID}', '{$teamName}', '{$teamCity}', '{$teamState}', '{$teamCountry}'
					    ) ";
	$result_insertTeam = mysql_query($insertTeam);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    redirect_to("video.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		redirect_to("video.php");
	    }else{
		$message_newTeam = "The information could not be updated.";
		$message_newTeam .= "<br />" . mysql_error() . mysql_affected_rows();
	    }
	}
    } else {
	if (count($errors_newTeam) == 1) {
	    $message_newTeam = "There was 1 error in the form.";
	} else {
	    $message_newTeam = "There were " . count($errors_newTeam) . " errors in the form.";
	}
    }
}


if(!isset($_POST['addNewTeam'])){
    $teamName = "";
    $teamCity = "";
    $teamState = "";
    $teamCountry = "";
    $message_newTeam = "";
}

if(isset($_POST['addNewCoach'])){
    
    $uid = $userid;
    $fn = trim(mysql_prep($_POST["newFirstName_coach"]));
    $ln = trim(mysql_prep($_POST["newLastName_coach"]));
    $ea = trim(mysql_prep($_POST["newEmail_coach"]));
    
    $timestamp_add = date("Y-m-d h:i:s a");
    
    // check for unique email address
    $query_verify_email = mysql_query("SELECT userID FROM userlog WHERE email = '$ea' ");
    if(mysql_num_rows($query_verify_email) == 0){
        
        
    
    $insertNewPlayer = "INSERT INTO userlog (
			    email, verified, userLevel, firstName, lastName, added_by, invited_on
			    ) VALUES (
			    '{$ea}', 'invited', 'coach', '{$fn}', '{$ln}', '{$uid}', '{$timestamp_add}'
                            ) ";
    $result_insertNP = mysql_query($insertNewPlayer);
    
    // test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    //echo "Success";
            
            //send invite email
//get user_id and generate key
$get_uidNew = mysql_query("SELECT userID FROM userlog WHERE email = '{$ea}' AND invited_on = '{$timestamp_add}' ");
$array_uidNew = mysql_fetch_array($get_uidNew);
$uidNew = $array_uidNew['userID'];

//Send the email
require_once("phpmailer/class.phpmailer.php");
$mail = new PHPMailer();

$body = "
	<h2>$fn, you've been invited to join PlayTagger!</h2>
	PlayTagger is your hassle-free, online highlight reel. Whether you're looking for your next great recruit, or wanting them to find your team, PlayTagger is the fastest, easiest way to get seen!
	Try PlayTagger FREE for a limited time.
	Please follow the link to <a href=\"http://www.worldrugbyshop.com/playtagger.html\">SIGNUP</a>.
	<br>
	<h4>Thank You for joining!</h4>
	-the PlayTagger team
";

$mail->Host       = "smtpout.secureserver.net";  // this relay server was recommended by godaddy via an email on 9/20/12
$mail->Port       = 25; // set the SMTP port for the server, godaddy says port 25

$mail->SetFrom("info@playtagger.com");
$mail->AddReplyTo("eric.nelson@meidh.com","Eric Nelson");
$mail->Subject    = "Confirm PlayTagger Signup and Verify Email";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress($ea);
if(!$mail->Send()) {  //Email Failed
    //echo "Mailer Error: " . $mail->ErrorInfo ."/n";
} else {  //Email Success
    //echo "Message sent! to: Someone at ".$email_to." \n";
    //redirect_to('index.php');
}
            
            
            
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		//do nothing
	    }else{
		//echo "ERROR";
	    }
	}
    }
}

//Code for taking user to player list - Find Player
if(isset($_POST['findPlayer'])){redirect_to("find_player.php");}

//Code for taking user to player list - Find Team
if(isset($_POST['findTeam'])){redirect_to("find_team.php");}

//Code for taking user to their page
if(isset($_POST['myProfile'])){
    
    if($userLevel == "admin"){
        redirect_to("player.php");
    }elseif($userLevel == "player"){
	redirect_to("player.php");
    }elseif($userLevel == "coach"){
	redirect_to("coach.php");
    }else{
	redirect_to("index.php?s=$sportID");
    }
}

if(isset($_POST['flagTag'])){  //The Flag button on a tag has been clicked
    //establish which tag we are working with
    $tagID = $_POST['tagID'];
    $timestamp_flag = date("Y-m-d h:i:s a");
    
    //gather information for flags table
    $query_flag = mysql_query("SELECT tagID, playerID, taggerID, gameURL FROM tags WHERE tagID = '{$tagID}' ");
    $array_flag = mysql_fetch_array($query_flag);
    $flag_gameURL = $array_flag['gameURL'];
    $flag_playerID = $array_flag['playerID'];
    $flag_taggerID = $array_flag['taggerID'];
    
    //Count the number of flags for this tag in the flags table now
    $count_flags = mysql_query("SELECT flagID FROM flags WHERE tagID = '{$tagID}' AND flagType = 'flagged' ");
    $flagCount = mysql_num_rows($count_flags);
    
    //Delete the tag if this is flag 3
    if($flagCount >= 2){ //delete the tag and register the action in the flags table
	$remove_tag = mysql_query("DELETE FROM tags WHERE tagID = '{$tagID}' ");
	$register_update = mysql_query("INSERT INTO flags ( tagID, flaggerID, taggerID, playerID, flagTime, flagType
				    )VALUES( '{$tagID}', '{$userid}', '{$flag_taggerID}', '{$flag_playerID}', '{$timestamp_flag}', 'deleted' )");
	
    }else{  //this tag has not been flagged enough times, register the flag in the flags table
	$register_update = mysql_query("INSERT INTO flags ( tagID, flaggerID, taggerID, playerID, flagTime, flagType
				    )VALUES( '{$tagID}', '{$userid}', '{$flag_taggerID}', '{$flag_playerID}', '{$timestamp_flag}', 'flagged' )");
	
    }
    
}
if(isset($_POST['removeTag'])){  //The Delete button on a tag has been clicked
    //establish which tag we are working with
    $tagID = $_POST['tagID'];
    $timestamp_flag = date("Y-m-d h:i:s a");
    
    //gather information for flags table
    $query_flag = mysql_query("SELECT * FROM tags WHERE tagID = '{$tagID}' ");
    $array_flag = mysql_fetch_array($query_flag);
    $flag_gameURL = $array_flag['gameURL'];
    $flag_playerID = $array_flag['userID'];
    
    //register this action in the flags table
    $register_update = mysql_query("INSERT INTO flags ( tagID, flaggerID, taggerID, playerID, flagTime, flagType
				    )VALUES( '{$tagID}', '{$userid}', '{$flag_taggerID}', '{$flag_playerID}', '{$timestamp_flag}', 'deleted' )");
    
    //Delete the tag
    $remove_tag = mysql_query("DELETE FROM tags WHERE tagID = '{$tagID}' ");
    
}

//Code for Logging In
if (isset($_POST['loginV'])) { // The user clicked 'loginV'
    //create array to place any errors
	$errors_log = array();  
    
    // perform validations on the form data
        $required_fields = array('email_log', 'password_log');
	$errors_log = array_merge($errors_log, check_required_fields($required_fields, $_POST));
        $fields_with_lengths = array('email_log' => 100, 'password_log' => 30);
        $errors_log = array_merge($errors_log, check_max_field_lengths($fields_with_lengths, $_POST));
	
    // get the content from the variables
        $email_log = trim(mysql_prep($_POST['email_log']));
        $password_log = trim(mysql_prep($_POST['password_log']));
	$sportID = trim(mysql_prep($_POST['sportID']));
	$sportName = trim(mysql_prep($_POST['sportName']));
	
    // translate the entered password to hashed
        $hashed_password = sha1($password_log);
	
    // no errors detected so far, continue
        if ( empty($errors_log) ) {
		
	    // Check database to see if username and the hashed password exist there.
		$query_loginV = "SELECT userID, email, verified, userLevel, firstName, paidDate, expDate FROM userlog WHERE email = '{$email_log}'
			    AND hashed_password = '{$hashed_password}' LIMIT 1";
		$result_loginV = mysql_query($query_loginV);
		confirm_query($result_loginV);
		$found_user = mysql_fetch_array($result_loginV);
		
	    // Pass or not pass
	    if($found_user['verified'] == 'yes'){
		if (mysql_num_rows($result_loginV) == 1) {  // Success
		    // username/password authenticated
		    // and only 1 match
			
                    //Determine paid, expired, or not paid
                        $today = date("Y-m-d");
                        
                        if(($found_user['paidDate'] != '0000-00-00') AND ($found_user['paidDate'] <= $today) AND ($found_user['expDate'] > $today)){
                            $payStatus = "paid";
                        }else{
                            $payStatus = "notpaid";
                        }
			
		    // set session id's
			$_SESSION['userid'] = $found_user['userID'];
			$_SESSION['useremail'] = $found_user['email'];
                        $_SESSION['paid'] = $payStatus;
			$_SESSION['sport'] = $sportID;
			$_SESSION['sportName'] = $sportName;
			
		    // get timestamp for this login
			$timestamp_login = date("Y-m-d h:i:s a");
			
		    // update the user info with new last_login stamp
			$query_timestamp = mysql_query("UPDATE userlog SET last_login = '{$timestamp_login}'
							WHERE userID = {$found_user['userID']}");
			
		    // if the user is logging into a new sport, insert new userinfo line
			$get_usersport = mysql_query("SELECT * FROM userinfo WHERE userID = '{$found_user['userID']}' AND sportID = '{$sportID}' ");
			$count_usersport = mysql_num_rows($get_usersport);
			if($count_usersport < 1){
			    $insert_newSport = mysql_query("INSERT INTO userinfo (userID, sportID) VALUES ('{$found_user['userID']}', '{$sportID}') ");
			}
			
			
		    // Login Successful, send them in
			if($found_user['userLevel'] == 'admin'){
                            redirect_to("admin.php");
                        }elseif($found_user['userLevel'] == 'player'){
                            if($found_user['firstName'] != ""){
                                redirect_to("video.php");
                            }else{
                                redirect_to("player_edit.php");
                            }
                        }elseif($found_user['userLevel'] == 'coach'){
                            if($found_user['firstName'] != ""){
                                redirect_to("video.php");
                            }else{
                                redirect_to("coach_edit.php");
                            }
                        }else{
                            redirect_to("video.php");
                        }
		} else {  // Login Failed, username and password not found
		    $message_log = "Incorrect username and/or password. If you were invited, please sign up. <a href=\"forgot.php\" style=\"color:red;\">I forgot my password.</a>";
		}
	    }else{  // Login Failed, email not verified
		$message_log = "Account not verified. Please follow the link in the Signup Confirmation email. <a href=\"forgot.php\" style=\"color:red;\">I forgot my password.</a>";
	    }
	
    // errors detected, relay them to the user
	} else {
	    if (count($errors_log) == 1) {
		$message_log = "There was 1 error in the form.";
	    } else {
		$message_log = "There were " . count($errors_log) . " errors in the form.";
	    }
	}
	
    //blank signup related variables
	$message_sign = "";
	$email_sign = "";
	$confirm_email = "";
	$password_sign = "";
	$confirm_password = "";
	
}else { // nothing has been picked, page is loading without interference
	    
	    // blank out all variables
		$message = "";
		$message_log = "";
		$email_log = "";
		$password_log = "";
		$email_sign = "";
		$confirm_email = "";
		$password_sign = "";
		$confirm_password = "";
}

//Code for Learn More and Sign Up (sets SESSION for userid and redirects to index.php)
$signupURL = "index.php?s=".$sportID."&l=1";
if (isset($_POST['signup'])) {redirect_to($signupURL);} 

//Code for Logging Out
if (isset($_POST['logout'])) {redirect_to('logout.php');}

?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $( "#addMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#addMenu" ).mouseleave( function(){
	$( "#addMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#searchMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#searchMenu" ).mouseleave( function(){
	$( "#searchMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#profileMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#profileMenu" ).mouseleave( function(){
	$( "#profileMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#helpMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#helpMenu" ).mouseleave( function(){
	$( "#helpMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#logoutMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#logoutMenu" ).mouseleave( function(){
	$( "#logoutMenu" ).menu( "collapseAll", null, true );
    });
});
</script>
<script type="text/javascript">
//Player autocomplete
$(function() {
    var playerNameSearch = [
	<?php
	    $get_playerName = mysql_query("SELECT playerID, playerName FROM tags WHERE sportID = '{$sportID}' GROUP BY playerID");
	    while($array_playerName = mysql_fetch_array($get_playerName)){
		echo '"'.$array_playerName['playerName'].'",';
	    }
	?>
    ];
    $( "#playerName" ).autocomplete({source: playerNameSearch});
});

//Player Tagging autocomplete
$(function() {
    var playerNameTag = [
	<?php
	    $get_playerNameTag = mysql_query("SELECT playerID, playerName FROM tags WHERE sportID = '{$sportID}' GROUP BY playerID");
	    while($array_playerNameTag = mysql_fetch_array($get_playerNameTag)){
		echo '"'.$array_playerNameTag['playerName'].'",';
	    }
	?>
    ];
    $( "#playerNameTag" ).autocomplete({source: playerNameTag});
});

//Team autocomplete
$(function() {
    var teamNameSearch = [
	<?php
	    $get_teamName = mysql_query("SELECT teamID, name FROM teams WHERE sportID = '{$sportID}' ");
	    while($array_teamName = mysql_fetch_array($get_teamName)){
		echo '"'.$array_teamName['name'].'",';
	    }
	?>
    ];
    $( "#teamName" ).autocomplete({source: teamNameSearch});
});
$(function() {
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-tag" ).dialog({
	autoOpen: false,
	position: ["center",300],
	width: 460,
	resizable: false,
	modal: true,
	closeOnEscape: false,
    });
    $( "#tagpop" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "open" );
	});
    $( "#tagdone" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "close" );
	});
    $( "#tagcancel" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "close" );
	});
    $( "#dialog-addp" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 460,
	resizable: false,
	modal: true,
	closeOnEscape: false,
    });
    $( "#addppop" )
	.button()
	.click(function() {
	    $( "#dialog-addp" ).dialog( "open" );
	});
    $( "#addpdone" )
	.button()
	.click(function() {
	    $( "#dialog-addp" ).dialog( "close" );
	});
    $( "#addpcancel" )
	.button()
	.click(function() {
	    $( "#dialog-addp" ).dialog( "close" );
	});
    $( "#dialog-addgame" ).dialog({
	<?php 
	    if (isset($_POST['saveNewGame'])) {
		if(empty($errors_newGame)){
		    echo "autoOpen: false,";
		}else{
		    echo "autoOpen: true,";
		}
	    }else{
		echo "autoOpen: false,";
	    }
	?>
	position: ["center",100],
	width: 700,
	resizable: false,
	modal: true,
    });
    $( "#addgamepop" )
	.button()
	.click(function() {
	    $( "#dialog-addgame" ).dialog( "open" );
	});
    $( "#addgamecancel" )
	.button()
	.click(function() {
	    $( "#dialog-addgame" ).dialog( "close" );
	});
    $( "#gday" ).datepicker({
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
	yearRange: '1980:2015'
    });
    $( "#dialog-addteam" ).dialog({
	<?php 
	    if (isset($_POST['addNewTeam'])) {
		if(empty($errors_newTeam)){
		    echo "autoOpen: false,";
		}else{
		    echo "autoOpen: true,";
		}
	    }else{
		echo "autoOpen: false,";
	    }
	?>
	position: ["center",100],
	width: 500,
	resizable: false,
	modal: true,
    });
    $( "#addteampop" )
	.button()
	.click(function() {
	    $( "#dialog-addteam" ).dialog( "open" );
	});
    $( "#addteamcancel" )
	.button()
	.click(function() {
	    $( "#dialog-addteam" ).dialog( "close" );
	});
    $( "#dialog-addc" ).dialog({
	<?php 
	    if (isset($_POST['addNewCoach'])) {
		if(empty($errors_newCoach)){
		    echo "autoOpen: false,";
		}else{
		    echo "autoOpen: true,";
		}
	    }else{
		echo "autoOpen: false,";
	    }
	?>
	position: ["center",100],
	width: 500,
	resizable: false,
	modal: true,
    });
    $( "#addcpop" )
	.button()
	.click(function() {
	    $( "#dialog-addc" ).dialog( "open" );
	});
    $( "#addccancel" )
	.button()
	.click(function() {
	    $( "#dialog-addc" ).dialog( "close" );
	});
    $( "#dialog-pay" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 700,
	resizable: false,
	modal: true,
    });
    $( "#paypop" )
	.button()
	.click(function() {
	    $( "#dialog-pay" ).dialog( "open" );
	});
    $( "#paypop2" )
	.button()
	.click(function() {
	    $( "#dialog-pay" ).dialog( "open" );
	});
    $( "#paycancel" )
	.button()
	.click(function() {
	    $( "#dialog-pay" ).dialog( "close" );
	});
    $( "#paysub" )
	.button()
	.click(function() {
	    //$( "#dialog-pay" ).dialog( "close" );
	});
    $( "#dialog-help" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 700,
	resizable: false,
	modal: true,
    });
    $( "#helppop" )
	.button()
	.click(function() {
	    $( "#dialog-help" ).dialog( "open" );
	});
    $( "#helpclose" )
	.button()
	.click(function() {
	    $( "#dialog-help" ).dialog( "close" );
	});
    $( "#dialog-loginV" ).dialog({
	<?php 
	    if (isset($_POST['loginV'])) {
		echo "autoOpen: true,";
	    }else{
		echo "autoOpen: false,";
	    }
	?>
	position: ["center",50],
	width: 460,
	resizable: false,
	modal: true,
	});
    $( "#loginVpop" )
	.button()
	.click(function() {
	    $( "#dialog-loginV" ).dialog( "open" );
	});
    $( "#loginVclose" )
	.button()
	.click(function() {
	    $( "#dialog-loginV" ).dialog( "close" );
	});
    $( "#dialog-search" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 700,
	resizable: false,
	modal: true,
    });
    $( "#searchpop" )
	.button()
	.click(function() {
	    $( "#dialog-search" ).dialog( "open" );
	});
    $( "#searchclose" )
	.button()
	.click(function() {
	    $( "#dialog-search" ).dialog( "close" );
	});
    $( "#maxday" ).datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
    });
    $( "#minday" ).datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
    });
    $( "button", ".logout" ).button({
        icons: {
            //primary: "ui-icon-circle-close"
        }
    });
    $( "button", ".profile" ).button({
        icons: {
            //primary: "ui-icon-person"
        }
    });
    $( "button", ".search" ).button({
        icons: {
            //primary: "ui-icon-search"
        }
    });
    $( "button", ".flag" ).button({
        icons: {
            primary: "ui-icon-flag"
        }
    });
});
$(function() {
    $( "a" )
    .button()
});
$(function() {
    $( "input[type=submit]" ).button()
    $( ".search" ).button({
	icons: {
	    primary: "ui-icon-search"
	}
    })
});
</script>
<style>
.ui-dialog .ui-dialog-titlebar-close { display: none; }
</style>
<style>
    .ui-menu { width: 135px; height: 50; border: none; },
    label { display: inline-block; width: 5em; },
    .nowPlaying {background-color: green;}
</style>

<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<!--<script type="text/javascript">
    google.load("swfobject", "2.1");
</script>
-->
<script src="js-functions.js"></script>
<script type="text/javascript">
    var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onPlayerReady(event) {
  event.target.playVideo();
}

var done = false;
function onPlayerStateChange(event) {
  if (event.data == YT.PlayerState.PLAYING && !done) {
    //setTimeout(stopVideo, 0);
    //done = true;
  }
}
function stopVideo() {
  player.stopVideo();
}


    var ytplayer;
    function onYouTubeIframeAPIReady() {
        ytplayer = new YT.Player('player', {
	    height: '405',
	    width: '720',
	    videoId: '<?php echo $defaultYTCHAR; ?>',
	    playerVars: {
		wmode: "opaque"
	    },
	    events: {
		'onReady': onPlayerReady,
		'onStateChange': onPlayerStateChange
	    }
        });
	var videoID = '<?php echo $defaultYTCHAR; ?>';
	updateTagTeams(videoID);
        updateUpcomingTags(videoID);
    }
    
</script>
<script type="text/javascript">

//function loadPlayer() {  // The "main method" of this sample. Called when someone clicks "Run".
//    var videoID = "<?php echo $defaultYTCHAR; ?>"  // The video to load
//    var params = { allowScriptAccess: "always" };  // Lets Flash from another domain call JavaScript
//    var atts = { id: "ytPlayer" };  // The element id of the Flash embed
    // All of the magic handled by SWFObject (http://code.google.com/p/swfobject/)
//    swfobject.embedSWF("http://www.youtube.com/v/" + videoID + 
//                           "?version=3&enablejsapi=1&playerapiid=player1", 
//                           "videoDiv", "480", "295", "9", null, null, params, atts);
//    updateTagTeams(videoID);
//    updateUpcomingTags(videoID);
//}

//GET STATES FOR SEARCH BOX
function getStates(str){
    if (str==""){
	document.getElementById("states").innerHTML="";
	return;
    } 
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
	if (xmlhttp.readyState==4 && xmlhttp.status==200) {
	    document.getElementById("states").innerHTML=xmlhttp.responseText;
	}
    }
    xmlhttp.open("GET","ajax.php?action=getstates&sv="+str,true);
    xmlhttp.send();
}
//ADD PLAYER CODE
function getAddPlayer(str){
    var userid = <?php echo "\"".$userid."\""; ?>;
    var sportid = <?php echo "\"".$sportID."\""; ?>;
    var newfirstName = document.getElementById("newFirstName").value;
    var newlastName = document.getElementById("newLastName").value;
    var emailAddress = document.getElementById("newEmail").value;
    if (newfirstName == "") {
        alert("All fields are required");
    }else if (newlastName == "") {
        alert("All fields are required");
    }else if (emailAddress == "") {
        alert("All fields are required");
    }else {
        if (window.XMLHttpRequest) {
            xmlhttp=new XMLHttpRequest();
        }else {
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
            if (xmlhttp.readyState==4 & xmlhttp.status==200) {
                /*if (xmlhttp.responseText == "Success") {
                    document.getElementById("addStatus").innerHTML="Player Added";
                }else {
                    document.getElementById("addStatus").innerHTML="Unknown Error while adding";
                }*/
            }
        }
        xmlhttp.open("GET","ajax.php?action=addplayer&uid=" + userid + "&fn=" + newfirstName + "&s=" + sportid + "&ln=" +
                         newlastName + "&ea=" + emailAddress,true);
        xmlhttp.send();
    }
}
</script>
<script language="javascript">         
    function findPlayer(event) {
        document.getElementById("paygolink").href="find_player.php";
    }
    function findTeam(event) {
        document.getElementById("paygolink").href="find_team.php";
    }
</script>




 <style>
.ui-combobox {
position: relative;
display: inline-block;
}
.ui-combobox-toggle {
position: absolute;
top: 0;
bottom: 0;
margin-left: -1px;
padding: 0;
/* adjust styles for IE 6/7 */
*height: 1.7em;
*top: 0.1em;
}
.ui-combobox-input {
margin: 0;
padding: 0.3em;
}
</style>
<script>
(function( $ ) {
$.widget( "ui.combobox", {
_create: function() {
var input,
that = this,
select = this.element.hide(),
selected = select.children( ":selected" ),
value = selected.val() ? selected.text() : "",
wrapper = this.wrapper = $( "<span>" )
.addClass( "ui-combobox" )
.insertAfter( select );
function removeIfInvalid(element) {
var value = $( element ).val(),
matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( value ) + "$", "i" ),
valid = false;
select.children( "option" ).each(function() {
if ( $( this ).text().match( matcher ) ) {
this.selected = valid = true;
return false;
}
});
if ( !valid ) {
// remove invalid value, as it didn't match anything
$( element )
.val( "" )
.attr( "title", value + " didn't match any item" )
.tooltip( "open" );
select.val( "" );
setTimeout(function() {
input.tooltip( "close" ).attr( "title", "" );
}, 2500 );
input.data( "autocomplete" ).term = "";
return false;
}
}
input = $( "<input>" )
.appendTo( wrapper )
.val( value )
.attr( "title", "" )
.addClass( "ui-state-default ui-combobox-input" )
.autocomplete({
delay: 0,
minLength: 0,
source: function( request, response ) {
var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
response( select.children( "option" ).map(function() {
var text = $( this ).text();
if ( this.value && ( !request.term || matcher.test(text) ) )
return {
label: text.replace(
new RegExp(
"(?![^&;]+;)(?!<[^<>]*)(" +
$.ui.autocomplete.escapeRegex(request.term) +
")(?![^<>]*>)(?![^&;]+;)", "gi"
), "<strong>$1</strong>" ),
value: text,
option: this
};
}) );
},
select: function( event, ui ) {
ui.item.option.selected = true;
that._trigger( "selected", event, {
item: ui.item.option
});
},
change: function( event, ui ) {
if ( !ui.item )
return removeIfInvalid( this );
}
})
.addClass( "ui-widget ui-widget-content ui-corner-left" );
input.data( "autocomplete" )._renderItem = function( ul, item ) {
return $( "<li>" )
.data( "item.autocomplete", item )
.append( "<a>" + item.label + "</a>" )
.appendTo( ul );
};
$( "<a>" )
.attr( "tabIndex", -1 )
.attr( "title", "List All" )
.tooltip()
.appendTo( wrapper )
.button({
icons: {
primary: "ui-icon-triangle-1-s"
},
text: false
})
.removeClass( "ui-corner-all" )
.addClass( "ui-corner-right ui-combobox-toggle" )
.click(function() {
// close if already visible
if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
input.autocomplete( "close" );
removeIfInvalid( input );
return;
}
// work around a bug (likely same cause as #5265)
$( this ).blur();
// pass empty string as value to search for, displaying all results
input.autocomplete( "search", "" );
input.focus();
});
input
.tooltip({
position: {
of: this.button
},
tooltipClass: "ui-state-highlight"
});
},
destroy: function() {
this.wrapper.remove();
this.element.show();
$.Widget.prototype.destroy.call( this );
}
});
})( jQuery );
$(function() {
$( "#tagPlayers" ).combobox();
$( "#awayTeam" ).combobox();
$( "#homeTeam" ).combobox();
$( "#teamSearch" ).combobox();
$( "#toggle" ).click(function() {
$( "#tagPlayers" ).toggle();
$( "#awayTeam" ).toggle();
$( "#homeTeam" ).toggle();
$( "#teamSearch" ).toggle();
});
});
</script>
<style>
    .ui-menu { width: 135px; height: 50; border: none; },
    label { display: inline-block; width: 5em; }
</style>




<!-- BEGIN TAG POP UP BOX -->
<div id="dialog-tag" title="Tag">
    <div id="tag"><center>
        <table border='0' style="width:450;">
            <tr>
                <td style="text-align:right;padding:5px;">Team:</td>
                <td style="text-align:left;padding:5px;"><select id="team" <!--onchange="updateTagPlayers();"--> ><option value="Select Team">Select Team</option><option value="1">Team 1</option><option value="Team 2">Team 2</option></select></td>
            </tr>
            <tr>
                <td style="text-align:right;padding:5px;">Player:</td>
                <td style="text-align:left;padding:5px;">
		<?php echo "<div class=\"ui-widget\"><select id=\"tagPlayers\">";
		echo "<option value=\"0\">Select Player</option>";
		
		$get_playerstag = mysql_query("SELECT userID, firstName, lastName FROM userlog WHERE userLevel = 'player' AND firstName != '' ORDER BY lastName DESC");
		while($array_playerstag = mysql_fetch_array($get_playerstag)){
		    
		    $get_playerlist = mysql_query("SELECT * FROM userinfo WHERE userID = '{$array_playerstag['userID']}' AND sportID = '{$sportID}' ");
		    while($array_playerlist = mysql_fetch_array($get_playerlist)){
			$playerID = $array_playerstag['userID'];
			$name = $array_playerstag['firstName']." ".$array_playerstag['lastName'];
			echo "<option value=\"$playerID\">$name</option>";
		    }
		}
		
		
		
		
		
    /* //OLD CODE - Not multi-sport compatible, yet
	$t="none";
        $players = getPlayers($t);
        $n = 0;
        echo "<option value=\"00\">Select Player</option>";
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
    */
    echo "</select></div>"; ?></td>
            </tr>
	    <tr>
                <td style="text-align:right;padding:5px;">Event:</td>
                <td style="text-align:left;padding:5px;">
		    <select id="whatHappened">
			<option value="Pick Item">Pick Item</option>
			<?php
			    $get_events=mysql_query("SELECT * FROM sports WHERE sportID = '{$sportID}' AND sportEvent != '' ");
			    while($array_events = mysql_fetch_array($get_events)){
				echo "<option value=\"".$array_events['sportEvent']."\" >".$array_events['sportEvent']."</option>";
			    }
			?>
		    </select>
		</td>
            </tr>
	    <tr>
		<td colspan='2' style="height:50px;font-size:12px;text-align:center;">
		    Add and Invite your teammates via the Add Player Menu
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
		    <input type="hidden" id="hiddenVid" value="<?php echo $defaultYTCHAR; ?>">
		    <button id="tagdone" type="button" value="Enter" onclick="submitTag();">Enter</button>
		    <button id="tagcancel" type="button" value="Cancel" onclick="playVideo();">Cancel</button>
		</td>
            </tr>
        </table>
    </center></div>
</center>
</div>
<!-- END TAG POP UP BOX -->
<!-- BEGIN ADD PLAYER POP UP BOX -->
<div id="dialog-addp" title="Add Player">
    <div id="addp"><center>
        <table border='0' style="width:450;">
	    <tr><td colspan='2'><div id="addplayer">New Player Details:</div></td></tr>
            <tr>
                <td style="text-align:right;padding:5px;">First Name: </td>
                <td style="text-align:left;padding:5px;"><input type="text" id="newFirstName" name="newFirstName" ></td>
            </tr>
            <tr>
                <td style="text-align:right;padding:5px;">Last Name: </td>
                <td style="text-align:left;padding:5px;"><input type="text" id="newLastName" name="newLastName" ></td>
            </tr>
            <tr>
                <td style="text-align:right;padding:5px;">Email Address: </td>
                <td style="text-align:left;padding:5px;"><input type="text" id="newEmail" name="newEmail" ></td>
            </tr>
	    <tr>
		<td colspan='2' style="height:50px;font-size:12px;text-align:center;">
		    *All fields required.  The new player will receive an email invite.
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
		    <button id="addpdone" type="button" value="AddPlayer" onclick="getAddPlayer(this.value)" >Add</button>
		    <button id="addpcancel" type="button" value="Cancel" >Cancel</button>
		</td>
            </tr>
        </table>
    </center></div>
</center>
</div>
<!-- END ADD PLAYER POP UP BOX -->
<!-- BEGIN ADD GAME POP UP BOX -->
<div id="dialog-addgame" title="Add Game">
    <div id="addgame"><center>
		    <form action="video.php" method="post">
		    <table border='0' style="width:100%;border:thin solid gray;font-size:12px;">
			<tr>
			    <td colspan='4' style="text-align:center;color:red;">
				<?php echo $message_newGame; ?>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;height:85px;width:200px;">
				
				<select id="homeTeam" name="homeTeam">
				    <option value="" selected="selected"></option>
				    <?php
					$get_homeTeam = mysql_query("SELECT teamID, name FROM teams WHERE sportID = '{$sportID}' ");
					
					while($array_homeTeam = mysql_fetch_array($get_homeTeam)){
					    echo "<option value=\"".$array_homeTeam['teamID']."\">".$array_homeTeam['name']."</option>";
					}
				    ?>
				</select>
				<br>
				<span style="font-size:12px;">Home Team</span>
			    </td>
			    <td style="text-align:center;height:85px;">
				<input type="text" name="homeScore" value="<?php echo htmlentities($homeScore); ?>" style="width:50px;margin-bottom:5px;" /><br>
				<span style="font-size:12px;">Home Final Score</span>
			    </td>
			    <td style="text-align:center;height:85px;">
				<select id="awayTeam" name="awayTeam">
				    <option value="" selected="selected"></option>
				    <?php
					$get_awayTeam = mysql_query("SELECT teamID, name FROM teams WHERE sportID = '{$sportID}' ");
					
					while($array_awayTeam = mysql_fetch_array($get_awayTeam)){
					    echo "<option value=\"".$array_awayTeam['teamID']."\">".$array_awayTeam['name']."</option>";
					}
				    ?>
				</select>
				<br>
				<span style="font-size:12px;">Away Team</span>
			    </td>
			    <td style="text-align:center;height:85px;">
				<input type="text" name="awayScore" value="<?php echo htmlentities($awayScore); ?>" style="width:50px;margin-bottom:5px;" /><br>
				<span style="font-size:12px;">Away Final Score</span>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:left;padding-left:50px;height:85px;">
				<?php
				    if($gameGender == "men"){
					$checkedMen = "checked=\"checked\"";
					$checkedWomen = "";
				    }elseif($gameGender == "women"){
					$checkedMen = "";
					$checkedWomen = "checked=\"checked\"";
				    }else{
					$checkedMen = "checked=\"checked\"";
					$checkedWomen = "";
				    }
				?>
				<input type="radio" name="gameGender" value="men" <?php echo $checkedMen; ?>><span style="font-size:12px;">Men's Game</span>
				<br>
				<input type="radio" name="gameGender" value="women" <?php echo $checkedWomen; ?>><span style="font-size:12px;">Women's Game</span>
			    </td>
			    <td colspan='3' style="text-align:center;height:85px;">
				<span style="font-size:12px;">City: </span>
				<input type="text" name="gameCity" value="<?php echo htmlentities($gameCity); ?>" style="width:130px;margin-bottom:5px;margin-right:15px;" />
				<span style="font-size:12px;">State: </span>
				<input type="text" name="gameState" value="<?php echo /*htmlentities($gameState)*/"IA"; ?>" style="width:50px;margin-bottom:5px;margin-right:15px;" />
				<span style="font-size:12px;">Country: </span>
				<input type="text" name="gameCountry" value="<?php echo /*htmlentities($gameCountry)*/"USA"; ?>" style="width:80px;margin-bottom:5px;" />
				<br>
				<span style="font-size:12px;">Game Location (currently only available in Iowa)</span>
			    </td>
			</tr>
			<tr>
			    <td colspan='2' style="text-align:center;height:85px;">
				<input type="text" id="gday" name="gameDay" value="<?php echo htmlentities($gameDay); ?>" value="" placeholder="click for calendar" style="width:120px;font-size:12px;margin-bottom:5px;">
				<br>
				<span style="font-size:12px;">Game Day</span>
			    </td>
			    <td colspan='2' style="text-align:center;height:85px;">
				<select name="koHR" style="width:50px;margin-bottom:5px;margin-right:0px;">
				    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>
				</select>
				:
				<select name="koMIN" style="width:50px;margin-bottom:5px;margin-left:0px;">
				    <option value="00">00</option><option value="05">05</option><option value="10">10</option><option value="15">15</option><option value="20">20</option><option value="25">25</option><option value="30">30</option><option value="35">35</option><option value="40">40</option><option value="45">45</option><option value="50">50</option><option value="55">55</option>
				</select>
				<select name="koAP" style="width:50px;margin-bottom:5px;margin-left:5px;">
				    <option value="PM">PM</option><option value="AM">AM</option>
				</select>
				<br>
				<span style="font-size:12px;">Kick-Off Time (hr:min)</span>
			    </td>
			</tr>
			<tr>
			    <td colspan='3' style="text-align:center;height:85px;">
				<input type="text" name="gameURL" value="<?php echo htmlentities($gameURL); ?>" style="width:550px;margin-bottom:5px;" />
				<br>
				<span style="font-size:12px;">Game Video URL</span>
			    </td>
			    <td style="text-align:center;height:85px;">
				<input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
				<input type="submit" name="saveNewGame" value="Save Game" style="height:50px;" />
				<button id="addgamecancel" type="button" value="Cancel" >Cancel</button>
			    </td>
			</tr>
			<tr>
			    <td colspan='4' style="text-align:center;font-size:9px;color:red;">
				*all fields required
			    </td>
			</tr>
		    </table>
		    </form>
    </center></div>
</center>
</div>
<!-- END ADD GAME POP UP BOX -->
<!-- BEGIN ADD TEAM POP UP BOX -->
<div id="dialog-addteam" title="Add Team">
    <div id="addteam"><center>
		    <form action="video.php" method="post">
		    <table style="width:100%;border:thin solid gray;">
			<tr>
			    <td colspan='4' style="text-align:center;color:red;">
				<?php echo $message_newTeam; ?>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;font-weight:bold;">
				Add a Team:
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;height:85px;">
				<input type="text" name="teamName" value="<?php echo htmlentities($teamName); ?>" style="margin-bottom:5px;" /><br>
				<span style="font-size:12px;">Team Name</span>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;height:85px;">
				<input type="text" name="teamCity" value="<?php echo htmlentities($teamCity); ?>" style="margin-bottom:5px;" /><br>
				<span style="font-size:12px;">Team City</span>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;height:85px;">
				<input type="text" name="teamState" value="<?php echo htmlentities($teamState); ?>" style="width:130px;margin-bottom:5px;" /><br>
				<span style="font-size:12px;">Team State</span>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;height:85px;">
				<input type="text" name="teamCountry" value="<?php echo htmlentities($teamCountry); ?>" style="margin-bottom:5px;" /><br>
				<span style="font-size:12px;">Team Country</span>
			    </td>
			</tr>
			<tr>
			    <td style="text-align:center;">
				<input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
				<input type="submit" name="addNewTeam" value="Add Team">
				<button id="addteamcancel" type="button" value="Cancel" >Cancel</button>
			    </td>
			</tr>
		    </table>
		    </form>
    </center></div>
</center>
</div>
<!-- END ADD Team POP UP BOX -->
<!-- BEGIN ADD COACH POP UP BOX -->
<div id="dialog-addc" title="Add Coach">
    <div id="addc"><center>
	<form action="video.php" method="post">
        <table border='0' style="width:450;">
	    <tr><td colspan='2'><div id="addplayer">New Coach Details:</div></td></tr>
            <tr>
                <td style="text-align:right;padding:5px;">First Name: </td>
                <td style="text-align:left;padding:5px;"><input type="text" id="newFirstName_coach" name="newFirstName_coach" ></td>
            </tr>
            <tr>
                <td style="text-align:right;padding:5px;">Last Name: </td>
                <td style="text-align:left;padding:5px;"><input type="text" id="newLastName_coach" name="newLastName_coach" ></td>
            </tr>
            <tr>
                <td style="text-align:right;padding:5px;">Email Address: </td>
                <td style="text-align:left;padding:5px;"><input type="text" id="newEmail_coach" name="newEmail_coach" ></td>
            </tr>
	    <tr>
		<td colspan='2' style="height:50px;font-size:12px;text-align:center;">
		    *All fields required.  The new coach will receive an email invite.
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
		    <input type="submit" name="addNewCoach" value="Add Coach">
		    <button id="addccancel" type="button" value="Cancel" >Cancel</button>
		</td>
            </tr>
        </table>
	</form>
    </center></div>
</center>
</div>
<!-- END ADD COACH POP UP BOX -->
<!-- BEGIN ADD PAY POP UP BOX -->
<div id="dialog-pay" title="Subscription Required">
    <div id="pay"><center>
        <table border='0' style="width:450;">
            <tr>
                <td style="text-align:center;padding:5px;">
		    <?php
			if($sportID == 1){
			    echo "Please note that this feature requires a subscription to Playtagger.";
			}else{
			    echo "This feature is free for everyone who signs up<br> before October 1. A paid subscription will<br> be required for anyone who signs up after that date.";
			}
		    ?>
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <?php
		    if($sportID == 1){
			if($userLevel == "player"){
			    $payLink = "http://www.worldrugbyshop.com/52302.html";
			}elseif($userLevel == "coach"){
			    $payLink = "http://www.worldrugbyshop.com/52303.html";
			}else{
			    $payLink = "index.php?s=$sportID";
			}
			echo "<a href=\"".$payLink."\" target=\"_blank\">Subscribe</a>
				<button id=\"paycancel\" type=\"button\" value=\"Cancel\" >Cancel</button>
			    ";
		    }else{
			echo "<a id=\"paygolink\" href=\"#\">Thank You</a>
				<button id=\"paycancel\" type=\"button\" value=\"Cancel\" >Cancel</button>
			    ";
		    }
		    ?>
		</td>
            </tr>
        </table>
    </center></div>
</div>
<!-- END ADD PAY POP UP BOX -->
<!-- BEGIN ADD Help POP UP BOX -->
<div id="dialog-help" title="How It Works">
    <div id="help"><center>
        <table>
	    <tr>
		<td style="text-align: right;padding-bottom: 30px;">
		    <button id="helpclose" type="button" value="Close" style="cursor:pointer;height:25px;font-size:10px;">Close</button>
		</td>
	    </tr>
            <tr>
                <td style="text-align:center;padding:5px;">
		    <iframe width="480" height="360" src="http://www.youtube.com/embed/I9J6TDBV0QQ?rel=0" frameborder="0" allowfullscreen></iframe>
		</td>
            </tr>
	    <tr>
                <td style="text-align:justify;padding-top:50px;padding-bottom:50px;">
		    <a href="http://www.youtube.com/watch?v=Kli2X8rvzxA&feature=youtu.be&t=0m49s" target="_blank" style="color: #A7C836;">How do I register for a PlayTagger account?</a>
		    <br><br>
		    <a href="http://www.youtube.com/watch?v=Kli2X8rvzxA&feature=youtu.be&t=1m13s" target="_blank" style="color: #A7C836;">How do I add a game into PlayTagger?</a>
		    <br><br>
		    <a href="http://www.youtube.com/watch?v=Kli2X8rvzxA&feature=youtu.be&t=1m41s" target="_blank" style="color: #A7C836;">How do I Tags videos?</a>
		    <br><br>
		    <a href="http://www.youtube.com/watch?v=Kli2X8rvzxA&feature=youtu.be&t=2m54s" target="_blank" style="color: #A7C836;">How do I use tags?</a>
		    <br><br>
		    <a href="http://www.youtube.com/watch?v=Kli2X8rvzxA&feature=youtu.be&t=3m35s" target="_blank" style="color: #A7C836;">How do I search for players or teams?</a>
		    <br><br>
		    <a href="http://www.youtube.com/watch?v=Kli2X8rvzxA&feature=youtu.be&t=4m18s" target="_blank" style="color: #A7C836;">How do I know if know if someone has contacted me?</a>	
		</td>
            </tr>
        </table>
    </center></div>
</div>
<!-- END ADD Help POP UP BOX -->
<!-- BEGIN SEARCH POP UP BOX -->
<div id="dialog-search" title="Search">
    <div id="search"><center>
        <form action="video.php" method="post">
		<table border='0' style="width:350px;height:345px;font-size:12px;text-align:left;background-color:white;border:thin solid gray;color:black;padding-left:50px;padding-right:50px;border-radius:6px;">
		    <tr>
			<td style="text-align:center;vertical-align:text-top;font-size:15px;height:25px;font-weight:bold;">
			    <!--Search Games-->
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;">
			    Country: <select id="searchCountry" name="searchCountry" onchange="getStates(this.value)">
				    <option value="all" selected="selected">All Countries</option>
			    <?php
				$get_countries = mysql_query("SELECT country FROM games GROUP BY country");
				$count = mysql_num_rows($get_countries);
				while($array_countries = mysql_fetch_array($get_countries)){
				    if($array_countries['country'] == ""){
					//skip it
				    }else{
					echo"<option value=\"".$array_countries['country']."\">".$array_countries['country']."</option>";
				    }
				    
				}
			    ?>
				    </select>
			</td>
		    </tr>
		    <tr>
			<td style="vertical-align:middle;height:25px;">
			    <div style="float:left;padding-right:5px;">State:</div><div id="states" style="float:left;"><span style="font-size:12px;">(all states - select country)</span></div>
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;">
			    Team: <!--<input id="teamName" type="text" name="teamName">-->
			    <select id="teamSearch" name="teamName">
				    <option value="" selected="selected"></option>
				    <?php
					$get_teamSearch = mysql_query("SELECT teamID, name FROM teams WHERE sportID = '{$sportID}' ");
					
					while($array_teamSearch = mysql_fetch_array($get_teamSearch)){
					    echo "<option value=\"".$array_teamSearch['teamID']."\">".$array_teamSearch['name']."</option>";
					}
				    ?>
				</select>
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;padding-left:50px;">
			    <input id="gameGender" type="radio" name="gameGender" value="men">Male
			    <input id="gameGender" type="radio" name="gameGender" value="women">Female
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;">
			    Player: <input id="playerName" type="text" name="playerName">
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;">
			    Min Date: <input type="text" id="minday" name="minday" placeholder="click for calendar" style="width:120px;font-size:12px;">
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;">
			    Max Date: <input type="text" id="maxday" name="maxday" placeholder="click for calendar" style="width:120px;font-size:12px;">
			</td>
		    </tr>
		    <tr>
			<td style="height:70px;vertical-align:text-top;">
			    <div style="float:left;padding-top:10px;padding-right:5px;">Event Types: </div>
			    <select name="eventTypes[]" multiple="multiple" style="height:45px;">
				<?php
				    $get_events=mysql_query("SELECT * FROM sports WHERE sportID = '{$sportID}' AND sportEvent != '' ");
				    while($array_events = mysql_fetch_array($get_events)){
					echo "<option value=\"".$array_events['sportEvent']."\" >".$array_events['sportEvent']."</option>";
				    }
				?>
				<!--<option value="Score">Score</option><option value="Assist">Assist</option><option value="Tackle">Tackle</option><option value="Nice Run">Nice Run</option><option value="Won Possession">Won Possession</option><option value="Kick">Kick</option>
				-->
			    </select>
			    <br><center><span style="font-size:8px;">Ctrl (windows) / Command (Mac) button to select multiple types</span></center>
			</td>
		    </tr>
		    <tr>
			<td style="height:25px;text-align:center;">
			    <input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
			    <button type="submit" class="search" name="search" value="Search" style="width:150px;">Search</button>
			    <button id="searchclose" type="button" value="Close" style="cursor:pointer;font-size:1em;">Close</button>
			</td>
		    </tr>
		</table>
	</form>
    </center></div>
</div>
<!-- END SEARCH POP UP BOX -->
<!-- BEGIN LOGINV POP UP BOX -->
<div id="dialog-loginV" title="LoginV">
<form id="loginVform" action="video.php" method="post">
<center>
<table>
    <tr>
	<td colspan="5" align="center">
	    <?php if (!empty($message_log)) {echo "<p style=\"color:#C20000; font-size: 12px;\">" . $message_log . "</p>";} ?>
	    <?php if (!empty($errors_log)) { display_errors($errors_log); } ?>
	</td>
    </tr>
    <tr>
	<td>Email:</td>
	<td><input type="text" name="email_log" maxlength="100" class="loginV_textboxes" style="width:160px;"
		value="<?php echo htmlentities($email_log); ?>" /></td>
    </tr>
    <tr>
	<td>Password:</td>
	<td><input type="password" name="password_log" maxlength="30" class="loginV_textboxes" style="width:160px;"
		value="<?php echo htmlentities($password_log); ?>" /></td>
    </tr>
    <tr>
        <td colspan='2' style="padding-top:20px;text-align:center;">
	    <input type="hidden" name="sportID" value="<?php echo $sportID; ?>">
	    <input type="hidden" name="sportName" value="<?php echo $sportName; ?>">
	    <input type="submit" alt="loginV" name="loginV" value="Login" >
	    <button id="loginVclose" type="button" value="Cancel" style="cursor:pointer;font-size:1em;">Cancel</button>
        </td>
    </tr>
</table>
</form>
</center>
</div>
<!-- END LOGINV POP UP BOX -->

<center>

<div style="width:725px;">
    <div style="<?php if($userid == "xxv"){echo "height:100px;padding-top:10px;padding-bottom:10px;";}else{echo"height:20px;";} ?>">
	<?php
	    if($userid == "xxv"){
		echo "
		    <table id=\"ui-corner-all\" style=\"width:100%;text-align:justify;border: thin solid black;border-bottom-right-radius:6px;border-bottom-left-radius:6px;border-top-right-radius:6px;border-top-left-radius:6px;\">
			<tr>
			    <td>
				<img src=\"images/playtagger_tag_logo_H100px.png\">
			    </td>
			    <td style=\"font-family:sans-serif;font-size:13px;padding:5px;\">
				<span style=\"color:#9BC21B;font-weight:bold;\">PlayTagger</span> is your hassle-free, online highlight reel.
				
				Tag game highlights of you and your friends - and college, National team, and professional scouts can instantly see your online highlight reel!
				<br>
				No matter where you want to go, <span style=\"color:#9BC21B;font-weight:bold;\">PlayTagger</span> is the fastest, easiest way to get seen! 
				<br><br>
				<span style=\"color:#9BC21B;\">Click the button below to learn how it works.</span>
			    </td>
			</tr>
		    </table>
		";
	    }
	?>
    </div>
    <div style="height:30px;text-align:center;font-family:sans-serif;">
	Have a tag code? Enter it here: <input type="text" name="tagcode"><input type="submit" name="enter_tag_code" value="Enter">
    </div>
    <div style="height:60px;">
	<div class="ui-corner-all" style="background-color:#000000;">
	<table style="margin-left: 0px;">
	    <tr>
		<td>
		    <?php
			if($userid == "xxv"){
			    
			}else{
			    echo "
				<ul id=\"profileMenu\">
				    <li>
					<a href=\"#\">Profiles</a>
					<ul>
					    <li>
						<form action=\"video.php\" method=\"post\">
						    <div class=\"profile\"><button type=\"submit\" name=\"myProfile\" value=\"My Profile\" style=\"cursor:pointer;height:46px;width:136px;border-color:#555555;font-size:.9em;font-family: Verdana,Arial,sans-serif;\">My Profile</button></div>
						</form>
					    </li>
					    <li>";
			    if(($_SESSION['paid'] != 'paid')){
				echo "<div class=\"profile\"><button id=\"paypop\" name=\"findPlayer\" value=\"Find Player\" onClick=\"findPlayer(event)\" style=\"cursor:pointer;height:46px;width:136px;border-color:#555555;font-size:.9em;font-family: Verdana,Arial,sans-serif;\">Players</button></div>";
			    }else{
				echo "<form action=\"video.php\" method=\"post\">
				<div class=\"profile\"><button type=\"submit\" name=\"findPlayer\" value=\"Find Player\" style=\"cursor:pointer;height:46px;width:136px;border-color:#555555;font-size:.9em;font-family: Verdana,Arial,sans-serif;\">Players</button></div>
				</form>";
			    }
			    echo "</li>
				<li>";
			    if(($_SESSION['paid'] != 'paid')){
				echo "<div class=\"profile\"><button id=\"paypop2\" name=\"findTeam\" value=\"Find Team\" onClick=\"findTeam(event)\" style=\"cursor:pointer;height:46px;width:136px;border-color:#555555;font-size:.9em;font-family: Verdana,Arial,sans-serif;\">Teams</button></div>";
			    }else{
				echo "<form action=\"video.php\" method=\"post\">
				<div class=\"profile\"><button type=\"submit\" name=\"findTeam\" value=\"Find Team\" style=\"cursor:pointer;height:46px;width:136px;border-color:#555555;font-size:.9em;font-family: Verdana,Arial,sans-serif;\">Teams</button></div>
				</form>";
			    }
			    echo "</li>
				</ul>
			    </li>
			</ul>
			    ";
			}
		    ?>
		</td>
		<td>
		    <?php if($userid == "xxv"){
			echo "<form action=\"video.php\" method=\"post\">";
			echo "<div class=\"logout\"><button type=\"submit\" name=\"signup\" value=\"signup\" style=\"cursor:pointer;height:46px;width:300px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Learn More and Sign Up</button></div>";
			echo "</form>";
		    }else{echo "
		    <ul id=\"addMenu\">
			<li>
			    <a href=\"#\">Add</a>
			    <ul>
				<li>
				    <button id=\"addgamepop\" alt=\"addgame\" name=\"addgame\" style=\"height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Add Game</button>
				</li>
				<li>
				    <button id=\"addteampop\" alt=\"addteam\" name=\"addteam\" style=\"height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Add Team</button>
				</li>
				<li>
				    <button id=\"addcpop\" alt=\"addcoach\" name=\"addcoach\" style=\"height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Add Coach</button>
				</li>
				<li>
				    <button id=\"addppop\" alt=\"addplayer\" name=\"addplayer\" style=\"height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Add Player</button>
				</li>
			    </ul>
			</li>
		    </ul>
		    ";}?>
		</td>
		<td>
		    <?php if($userid == "xxv"){echo " ";}else{echo "
			<button id=\"searchpop\" alt=\"searchbox\" name=\"searchbox\" style=\"height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Search</button>
		    ";}?>
		</td>
		<td>
			<?php if($userid == "xxv"){
			    echo "<button id=\"loginVpop\" alt=\"loginVpop\" name=\"loginVpop\" style=\"height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Login</button>";
			}else{
			    echo "<form action=\"video.php\" method=\"post\">";
			    echo "<div class=\"logout\"><button type=\"submit\" name=\"logout\" value=\"logout\" style=\"cursor:pointer;height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;\">Logout</button></div>";
			    echo "</form>";
			} ?>
		</td>
		<td>
		    <ul id="helpMenu">
			<button id="helppop" alt="helppop" name="helppop" style="height:46px;width:136px;border-color:#555555;font-size:1em;font-family: Verdana,Arial,sans-serif;"><?php if($userid == "xxv"){echo "About";}else{echo "Help";}?></button>
			<!--<li><a href="#">Help</a></li>-->
		    </ul>
		</td>
	    </tr>
	</table>
	</div>
    </div>
    <div class="ui-corner-all" style="height:415px;background-color: black;padding-top: 4px;">
	<div id="player"></div>
    </div>
    <div style="height:50px;padding-top: 10px;">
	<?php
	    if($userid == "xxv"){
		echo "";
	    }else{
		echo "<button id=\"tagpop\" onclick=\"pauseVideo();\" alt=\"tag\" name=\"tag\" style=\"width:720px;border-color:#555555;\">Tag Video</button>";
	    }
	?>
    </div>
    <div style="height:170px;">
	<div class="ui-corner-all" style="float: left;width: 350px;height:170px;background-color:#C5C4BD;margin-left: 5px;">
	    <div id="upcomingTags" style="width:350px;height:160px;vertical-align:top;overflow-y:auto;font-size:12px;">&nbsp;</div>
	</div>
	<div class="ui-corner-all" style="float: right;width: 350px;height:170px;background-color:#C5C4BD;margin-right: 5px;overflow-y:auto;">
	    
	    <table id="gameList" style="text-align:left;max-width:330px;width:330px;height:170px;font-family:sans-serif;font-size:12px;padding-bottom:4px;" border="0">
	    <?php
		while($array_gameslist = mysql_fetch_array($get_gameslist)){
		    $url = $array_gameslist['ytchar'];
		    $gameIDT = "game".$url;
		    //get the team names
		    $get_awayteamName = mysql_query("SELECT teamID, name FROM teams WHERE teamID = '{$array_gameslist['awayteamID']}' ");
		    $array_awayteamName = mysql_fetch_array($get_awayteamName);
		    $awayTeamID = $array_awayteamName['teamID'];
		    $awayTeam = $array_awayteamName['name'];
		    
		    $get_hometeamName = mysql_query("SELECT teamID, name FROM teams WHERE teamID = '{$array_gameslist['hometeamID']}' ");
		    $array_hometeamName = mysql_fetch_array($get_hometeamName);
		    $homeTeamID = $array_hometeamName['teamID'];
		    $homeTeam = $array_hometeamName['name'];
		    
		    //modify the date displayed
		    $date = date('M d,y', strtotime($array_gameslist['date']));
		    
		    //modify and output gender
		    if($array_gameslist['gender'] == "men"){
			$gender = "M";
		    }elseif($array_gameslist['gender'] == "women"){
			$gender = "F";
		    }
		    
		    //output video link
		    $url = $array_gameslist['ytchar'];
		    if($defaultYTCHAR == $url){
			$selectedGame = "background-color:black;";
		    }else{
			$selectedGame = "";
		    }
		    
		    
		    //If this user has already flagged this tag, print a message instead of a button
		    //$count_flagsGame = mysql_query("SELECT flagID FROM flags WHERE tagID = '{$tagID}' AND flaggerID = '{$userid}' ");
		    //$flagGameCount = mysql_num_rows($count_flagsGame);
		    $flagGameCount = 0;
		    if($flagGameCount >= 1){
			$buttong = "<span style=\"font-size:10px;\">FLAGGED</span>";
		    }elseif($userid == "xxv"){
			$buttong = " ";
		    }else{
			$buttong = "<input type=\"submit\" id=\"flagGame\" class=\"\" name=\"flagGame\" value=\"Flag\" title=\"Flag this Game as incorrect\" style=\"background:none;font-size:10px;height:22px;width:45px;padding:0px;border:solid thin grey;border-radius:0px;background-color:black;color:white;cursor:pointer\">";
		    }
		    
		    echo "<tr id=\"".$url."\" align=\"left\" onclick=\"loadVideo('".$url."')\" style=\"cursor:pointer;\">";
		    echo "<td style=\"padding-bottom:2px;\"><img src=\"images/playsmall.png\" /></td>";
		    //prep url to hometeam page
		    if($userid == "xxv"){
			$hometeamURL = $homeTeam;
			$awayteamURL = $awayTeam;
		    }else{
			$hometeamURL = "<a href=\"team.php?tid=$homeTeamID\" style=\"font-size:12px;text-decoration:underline;background:none;border:none;color:blue;\">$homeTeam</a>";
			$awayteamURL = "<a href=\"team.php?tid=$awayTeamID\" style=\"font-size:12px;text-decoration:underline;background:none;border:none;color:blue;\">$awayTeam</a>";
		    }
		    echo "<td style=\"padding-bottom:2px;width:60px;max-width:60px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis;\">$hometeamURL</td>";
		    echo "<td style=\"padding-bottom:2px;\">vs</td>";
		    echo "<td style=\"padding-bottom:2px;width:60px;max-width:60px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis;\">$awayteamURL</td>";
		    echo "<td style=\"padding-bottom:2px;\">($gender)</td>";
		    echo "<td style=\"padding-bottom:2px;width:60px;max-width:60px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis;\">$date</td>";
		    echo "<td style=\"padding-bottom:2px;width:60px;max-width:60px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis;\">
			    <form action=\"video.php\" method=\"post\">
                            $buttong
                            <input type=\"hidden\" name=\"gameID\" id=\"gameID\" value=\"$url\">
                            </form>
			    </td>";
		    echo "</tr>";
		}
	    ?>
	    </table>
	</div>
    </div>
    <div style="height:50px;padding-top:20px;font-family: sans-serif;">
	<span class='st_sharethis' displayText=''></span>Click to share the tag you're watching
    </div>
</div>

</center>
<?php include("includes/footer.php");?>