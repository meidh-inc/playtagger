<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 
// Get info from session
    $userid = $_SESSION['userid'];
    
//check to see if they are an admin
    $allowed_users = array(1, 4, 5, 12, 42);
    // 1 = eric-localhost
    // 4 = eric-live
    // 5 = chris-live-iahsra account
    // 12 = Brandi-live
    // 42 = John O'brien-live
    if(!in_array($userid,$allowed_users)){
	redirect_to('video.php');
    }

//Save the game details to the database
if (isset($_POST['saveGame'])) { // Form has been submitted.
    $errors = array();

    // perform validations on the form data
    $required_fields = array('homeTeam', 'homeScore', 'awayTeam', 'awayScore',
			    'gameGender', 'gameCity', 'gameState', 'gameCountry',
			    'gameDay', 'koHR', 'koMIN', 'koAP', 'gameURL' );
    $errors = array_merge($errors, check_required_fields($required_fields, $_POST));
    $fields_with_lengths = array('homeTeam' => 100, 'awayTeam' => 100, 'gameURL' => 255);
    $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
    
    //Get team names from the passed Team ID's
    $homeID = trim(mysql_prep($_POST['homeTeam']));
    $awayID = trim(mysql_prep($_POST['awayTeam']));
    
    $get_homeName = mysql_query("SELECT name FROM teams WHERE teamID = '{$homeID}' ");
    $array_homeName = mysql_fetch_array($get_homeName);
    $homeTeam = $array_homeName['name'];
    
    $get_awayName = mysql_query("SELECT name FROM teams WHERE teamID = '{$awayID}' ");
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
    
    
    // Proceed if there are no errors, else print the errors
    if (empty($errors)) {
	//Put new game into database
	$insertGame = "INSERT INTO gameslist (
					    homeTeam, homeTeamId, homeScore, awayTeam, awayTeamId, awayScore, gameGender, gameCity,
					    gameState, gameCountry, gameDay, koHR, koMIN, koAP, gameURL, ytchar
					    ) VALUES (
					    '{$homeTeam}', '{$homeID}', '{$homeScore}', '{$awayTeam}', '{$awayID}', '{$awayScore}', '{$gameGender}',
					    '{$gameCity}', '{$gameState}', '{$gameCountry}', '{$gameDay}', '{$koHR}',
					    '{$koMIN}', '{$koAP}', '{$gameURL}','{$ytchar}'
					    ) ";
	$result_insert = mysql_query($insertGame);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    redirect_to("admin.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		redirect_to("admin.php");
	    }else{
		$message = "The information could not be updated.";
		$message .= "<br />" . mysql_error() . mysql_affected_rows();
	    }
	}
    } else {
	if (count($errors) == 1) {
	    $message = "There was 1 error in the form.";
	} else {
	    $message = "There were " . count($errors) . " errors in the form.";
	}
    }
}

//Load blank form if editGame was not clicked
if(!isset($_POST['editGame'])){
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
    $message = "";
}


//Save the game details to the database
if (isset($_POST['addTeam'])) { // Form has been submitted.
    $errors = array();

    // perform validations on the form data
    $required_fields = array('teamName', 'teamCity' );
    $errors = array_merge($errors, check_required_fields($required_fields, $_POST));
    $fields_with_lengths = array('teamName' => 255, 'teamCity' => 255, 'teamState' => 2, 'teamCountry' => 100);
    $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
    
    // clean up the form data before putting it in the database
    $teamName = trim(mysql_prep($_POST['teamName']));
    $teamCity = trim(mysql_prep($_POST['teamCity']));
    $teamState = trim(mysql_prep($_POST['teamState']));
    $teamCountry = trim(mysql_prep($_POST['teamCountry']));
    
    
    // Proceed if there are no errors, else print the errors
    if (empty($errors)) {
	//Put new game into database
	$insertTeam = "INSERT INTO teams (
					    name, city, state, country
					    ) VALUES (
					    '{$teamName}', '{$teamCity}', '{$teamState}', '{$teamCountry}'
					    ) ";
	$result_insertTeam = mysql_query($insertTeam);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    redirect_to("admin.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		redirect_to("admin.php");
	    }else{
		$message_edit = "The information could not be updated.";
		$message_edit .= "<br />" . mysql_error() . mysql_affected_rows();
	    }
	}
    } else {
	if (count($errors_edit) == 1) {
	    $message_edit = "There was 1 error in the form.";
	} else {
	    $message_edit = "There were " . count($errors_edit) . " errors in the form.";
	}
    }
}

//Load blank form if editGame was not clicked
if(!isset($_POST['addTeam'])){
    $teamName = "";
    $teamCity = "";
    $teamState = "";
    $teamCountry = "";
}


// button to save coach changes
if(isset($_POST['saveCoaches'])){
    $get_teamList = mysql_query("SELECT teamID, name, admin FROM teams");
    
    while($array_teamList = mysql_fetch_array($get_teamList)){
	$team_id = $array_teamList['teamID'];
	
	$selected_admin = trim(mysql_prep($_POST[$team_id]));
	
	$update_admin = "UPDATE teams SET
			    admin = '{$selected_admin}'
			    WHERE teamID = {$team_id}";
	$update_result = mysql_query($update_admin);
	
    }
    
    
    //redirect_to("admin.php");
}


// button to navigate to videos page
if(isset($_POST['toVideos'])){
    redirect_to("video.php");
}

//Code for Logging Out
if (isset($_POST['logout'])) {redirect_to('logout.php');}

?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.10.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $( "#tabs" ).tabs({
	heightStyle: "fill"
    });
    $( "#from" ).datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
	onSelect: function( selectedDate ) {
	    $( "#to" ).datepicker( "option", "minDate", selectedDate );
	}
    });
    $( "button", ".logout" ).button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });
});
</script>

<center>
<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;">
    <tr>
	<td style="height:50px;text-align:right;">
	    <input type="submit" name="toVideos" value="Videos">
	</td>
	<td style="height:50px;text-align:right;">
	    <form action="admin.php" method="post">
	    <div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
	    </form>
	</td>
    </tr>
    <tr>
	<td colspan='2' style="text-align:left;">
	    <div id="tabs" style="font-size:14px;height:600px;">
		<ul>
		    <li><a href="#tabs-1">Team Admin</a></li>
		    <li><a href="#tabs-2">Game Admin</a></li>
		    <li><a href="#tabs-3">Tag Admin</a></li>
		    <li><a href="#tabs-4">User Admin</a></li>
		    <li><a href="#tabs-5">Coach Admin</a></li>
		</ul>
		<div id="tabs-1">
		    <form action="admin.php" method="post">
		    <table style="width:100%;border:thin solid gray;margin-bottom:50px;">
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
				<input type="submit" name="addTeam" value="Save Team">
			    </td>
			</tr>
		    </table>
		    </form>
		    <table border='0' style="width:100%;margin-top:20px;margin-bottom:20px;">
			<tr>
			    <td colspan='3' style="border-bottom:thin solid gray;">
				Teams
			    </td>
			</tr>
			<?php
			    $get_teams = mysql_query("SELECT name, city, state FROM teams");
			    $count_teams = mysql_num_rows($get_teams);
			    if($count_teams >= 1){
			    while($array_teams = mysql_fetch_array($get_teams)){
				echo "<tr>";
				    echo "<td>".$array_teams['name']."</td>";
				    echo "<td>".$array_teams['city'].", ".$array_teams['state']."</td>";
				    echo "<td> (view) (edit) </td>";
				echo "</tr>";
			    }
			    }else{
				echo "<tr><td>Games Not Found</td></tr>";
			    }
			?>
		    </table>
		</div>
		<div id="tabs-2">
		    <form action="admin.php" method="post">
		    <table style="width:100%;border:thin solid gray;margin-bottom:50px;">
			<tr>
			    <td style="text-align:center;height:85px;">
				
				<select name="homeTeam">
				    <?php
					$get_homeTeam = mysql_query("SELECT teamID, name FROM teams");
					
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
				<select name="awayTeam">
				    <?php
					$get_awayTeam = mysql_query("SELECT teamID, name FROM teams");
					
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
					$checkedMen = "";
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
				<input type="text" id="from" name="gameDay" value="<?php echo htmlentities($gameDay); ?>" value="" placeholder="click for calendar" style="width:120px;font-size:12px;margin-bottom:5px;">
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
				<input type="submit" name="saveGame" value="Save Game" style="height:50px;" />
			    </td>
			</tr>
			<tr>
			    <td colspan='4' style="text-align:center;font-size:9px;color:red;">
				*all fields required
			    </td>
			</tr>
		    </table>
		    </form>
		    <!--
			    NOTES TO PRINT
			    -Print List of Games in the System-
				($homeTeam VS $awayTeam | $gameDay/$gameTime | -View- -Edit-)
				--flagged
				--awaiting approval
				--the rest
		    -->
		    <table border='0' style="width:100%;margin-top:20px;margin-bottom:20px;">
			<tr>
			    <td colspan='3' style="border-bottom:thin solid gray;">
				Games
			    </td>
			</tr>
			<?php
			    $get_games = mysql_query("SELECT homeTeam, awayTeam, gameGender, gameDay, koHR, koMIN, koAP FROM gameslist");
			    $count_games = mysql_num_rows($get_games);
			    if($count_games >= 1){
			    while($array_games = mysql_fetch_array($get_games)){
				echo "<tr>";
				    echo "<td>".$array_games['homeTeam']." VS ".$array_games['awayTeam']." (".$array_games['gameGender'].")</td>";
				    echo "<td>Game Day: ".$array_games['gameDay'].", KO: ".$array_games['koHR'].":".$array_games['koMIN']." ".$array_games['koAP']."</td>";
				    echo "<td> (view) (edit) </td>";
				echo "</tr>";
			    }
			    }else{
				echo "<tr><td>Games Not Found</td></tr>";
			    }
			?>
		    </table>
		</div>
		<div id="tabs-3">
		    Tag Tab
		    <!--     NOTES TO PRINT
			    -Print List of Games in the System-
				($homeTeam VS $awayTeam | $gameDay/$gameTime | -View- -Edit-)
				--flagged
				--awaiting approval
				--the rest
		    -->
		    <table style="width:100%;margin-bottom:50px;">
			<tr>
			    <td>
				
			    </td>
			</tr>
		    </table>
		</div>
		<div id="tabs-4">
		    User Tab
		</div>
		<div id="tabs-5">
		    <form action="admin.php" method="post">
		    <table style="width:700px;margin-bottom:50px;">
			<tr>
			    <td colspan='3' style="padding:20px;">
				<input type="submit" name="saveCoaches" value="Save Settings" >
			    </td>
			</tr>
			<tr>
			    <td><u>Team</u></td>
			    <td><u>Location</u></td>
			    <td><u>Team Page Admin Coach</u></td>
			</tr>
			<?php
			    $get_teamsList = mysql_query("SELECT teamID, name, city, state, admin FROM teams");
			    
			    while($array_teamsList = mysql_fetch_array($get_teamsList)){
				
				echo "<tr>";
				echo "<td>".$array_teamsList['name']."</td>";
				echo "<td>".$array_teamsList['city'].", ".$array_teamsList['state']."</td>";
				echo "<td><select id=\"".$array_teamsList['teamID']."\" name=\"".$array_teamsList['teamID']."\"><option value=\"0\">none</option>";
				    $get_coachList = mysql_query("SELECT id, firstName, lastName FROM users WHERE teamName = '{$array_teamsList['name']}' ");
				    while($array_coachList = mysql_fetch_array($get_coachList)){
					if($array_teamsList['admin'] == $array_coachList['id']){
					    $s1 = "selected=\"selected\"";
					}else{
					    $s1 = "";
					}
					
					echo "<option value=\"".$array_coachList['id']."\" ".$s1.">".$array_coachList['firstName']." ".$array_coachList['lastName']."</option>";
				    }
				echo "</select></td>";
				echo "</tr>";
			    }
			?>
		    </table>
		    </form>
		</div>
	    </div>
	</td>
    </tr>
</table>
</center>
<?php include("includes/footer.php");?>