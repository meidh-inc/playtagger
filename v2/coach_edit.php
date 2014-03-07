<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];

//Check for data
    $get_coachinfo1 = mysql_query("SELECT firstName, lastName FROM userlog
			      WHERE userID = '{$userid}' ");
    $array_coachinfo1 = mysql_fetch_array($get_coachinfo1);
    $get_coachinfo2 = mysql_query("SELECT teamID, teamGender, aboutText FROM userinfo
			      WHERE userID = '{$userid}' ");
    $array_coachinfo2 = mysql_fetch_array($get_coachinfo2);
    $get_coachinfo3 = mysql_query("SELECT name, link, level FROM teams
			      WHERE teamID = '{$array_coachinfo2['teamID']}' ");
    $array_coachinfo3 = mysql_fetch_array($get_coachinfo3);

if($array_coachinfo1['firstName'] != ""){
    $firstName = $array_coachinfo1['firstName'];
    $lastName = $array_coachinfo1['lastName'];
    $teamNamec = $array_coachinfo3['name'];
    $teamGender = $array_coachinfo2['teamGender'];
    $teamLevel = $array_coachinfo3['level'];
    $aboutText = $array_coachinfo2['aboutText'];
    $teamURL = $array_coachinfo3['link'];
    
    $message_edit = "";
    
}else{
    $firstName = "";
    $lastName = "";
    $teamNamec = "";
    $teamURL = "";
    $aboutText = "";
    $message_edit = "";
}


//Save the player details to the database
if (isset($_POST['saveCoach'])) { // Form has been submitted.
    $errors_edit = array();

    // perform validations on the form data
    $required_fields = array('firstName', 'lastName' );
    $errors_edit = array_merge($errors_edit, check_required_fields($required_fields, $_POST));
    $fields_with_lengths = array('firstName' => 250, 'lastName' => 250);
    $errors_edit = array_merge($errors_edit, check_max_field_lengths($fields_with_lengths, $_POST));
    
    // clean up the form data before putting it in the database
    $firstName = trim(mysql_prep($_POST['firstName']));
    $lastName = trim(mysql_prep($_POST['lastName']));
    $teamNamec = trim(mysql_prep($_POST['teamNamec']));
    $teamGender = trim(mysql_prep($_POST['teamGender']));
    //$teamURL = trim(mysql_prep($_POST['teamURL']));
    //$teamLevel = trim(mysql_prep($_POST['teamLevel']));
    $aboutText = trim(mysql_prep($_POST['aboutText']));

    // Proceed if there are no errors, else print the errors
    if (empty($errors_edit)) {
	//Put new info into database
	$update_coach1 = "UPDATE userlog SET
			    firstName = '{$firstName}',
			    lastName = '{$lastName}'
			    WHERE userID = {$userid}";
	$result_coach1 = mysql_query($update_coach1);
	$update_coach2 = "UPDATE userinfo SET
			    teamID = '{$teamNamec}',
			    teamGender = '{$teamGender}',
			    aboutText = '{$aboutText}'
			    WHERE userID = {$userid}";
	$result_coach = mysql_query($update_coach2);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    //$update_team = mysql_query("UPDATE teams SET teamURL = '{$teamURL}' WHERE name = '{$teamNamec}' ");
	    redirect_to("coach.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		
		redirect_to("coach.php");
		
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
					    name, city, state, country
					    ) VALUES (
					    '{$teamName}', '{$teamCity}', '{$teamState}', '{$teamCountry}'
					    ) ";
	$result_insertTeam = mysql_query($insertTeam);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    redirect_to("coach_edit.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		redirect_to("coach_edit.php");
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

// button to navigate to videos page
if(isset($_POST['toVideos'])){
    redirect_to("video.php");
}

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
    $( "button", ".logout" ).button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });
});
</script>


<!-- BEGIN ADD TEAM POP UP BOX -->
<div id="dialog-addteam" title="Add Team">
    <div id="addteam"><center>
		    <form action="coach_edit.php" method="post">
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


<!--<center>-->

<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;font-family: sans-serif;">
    <tr>
	<td colspan='2' style="height:50px;">
	    <table style="width:100%;text-align:center;">
		<tr>
	<td>
	    <button id="addteampop" alt="addteam" name="addteam" style="border-color:#555555;font-size:12px;">Add Team</button>
	</td>
	<td style="height:50px;text-align:right;">
	    <form action="coach_edit.php" method="post">
	    <input type="submit" name="toVideos" value="Go To PlayTagger">
	    </form>
	</td>
	<td style="height:50px;text-align:right;">
	    <div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
	</td>
		</tr>
	    </table>
	</td>    
    </tr>
    <tr><td colspan='2' style="height:1px;border-top:solid gray thin;"><?php echo $message_edit; ?></td></tr>
    <tr><td colspan='2'>
    <form action="coach_edit.php" method="post">
    <table border='0' style="width:100%;">
    <tr>
	<td style="width:300px;height:50px;text-align:center;">
	    <img src="images/defaultuser.jpg" alt="default user image" >
	</td>
	<td style="height:50px;text-align:left;">
	    First Name: <input type="text" name="firstName" value="<?php echo htmlentities($firstName); ?>" style="margin-bottom:5px;" /><br>
	    Last Name: <input type="text" name="lastName" value="<?php echo htmlentities($lastName); ?>" style="margin-bottom:5px;" /><br>
	    Team Name: 	    
	    <select name="teamNamec">
		<?php
		    $get_teamName = mysql_query("SELECT teamID, name FROM teams");
		    
		    while($array_teamName = mysql_fetch_array($get_teamName)){
			$get_selectedTeam = mysql_query("SELECT teamID FROM userinfo WHERE userID = '{$userid}' ");
			$array_selectedTeam = mysql_fetch_array($get_selectedTeam);
			if($array_selectedTeam['teamID'] == $array_teamName['teamID']){
			    $selectedTeama = "selected=\"selected\"";
			    $selectedTeamb = "";
			}else{
			    $selectedTeama = "";
			    $selectedTeamb = "selected=\"selected\"";
			}
			
			echo "<option value=\"".$array_teamName['teamID']."\" ".$selectedTeama.">".$array_teamName['name']."</option>";
		    }
		    echo "<option value=\"xx\" ".$selectedTeamb.">team not selected</option>";
		?>
	    </select>
	    <br>
	    <span style="font-size:12px;color:orange;">
		Didn't see your team listed?  Add them via the Add Team button above.
	    </span>
	    <br><br>
	    
	    <input type="radio" name="teamGender" value="male">Coach for male team<br>
	    <input type="radio" name="teamGender" value="female">Coach for female team<br>
	    <input type="radio" name="teamGender" value="both" checked="checked">Coach for male and female teams<br>
	    <br>
	    Website: <?php echo $teamURL; ?>
	    <span style="font-size: 10px;">(updated via Team Admin page)</span><br>
	    
	    Team Level: <?php
			    if($teamLevel != "xx"){
				echo $teamLevel;
			    }else{
				echo "--";
			    }
			?> <span style="font-size: 10px;">(updated via Team Admin page)</span>
	    <br><br>
	    <a href="change_password.php" target="_blank">Change Password</a>
	</td>
    </tr>
    <tr>
	<td style="height:100px;text-align:center;vertical-align:text-top;padding-top:10px;">
	    About Me:
	</td>
	<td style="height:100px;text-align:left;padding-top:10px;">
	    <textarea name="aboutText" cols=50 rows=7 ><?php echo $aboutText; ?></textarea>
	</td>
    </tr>
    <tr>
	<td colspan='2' style="height:50px;text-align:center;padding-top:10px;">
	    <input type="submit" name="saveCoach" value="Save Settings">
	</td>
    </tr>
    
    </td></tr></table>
    </form>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="text-align:center;">
	    
	</td>
    </tr>
</table>
<!--</center>-->
<?php include("includes/footer.php");?>