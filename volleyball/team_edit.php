<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];

if(isset($_GET['tid'])){
//Check for data
    //get Team info from teams table
    $get_teaminfo = mysql_query("SELECT name, teamURL, city, state, teamLevel, aboutText FROM teams WHERE teamID = '{$_GET['tid']}' ");
    $array_teaminfo = mysql_fetch_array($get_teaminfo);
    $teamName = $array_teaminfo['name'];
    $teamCity = $array_teaminfo['city'];
    $teamState = $array_teaminfo['state'];
    $teamLevel = $array_teaminfo['teamLevel'];
    $teamURL = $array_teaminfo['teamURL'];
    $aboutText = $array_teaminfo['aboutText'];
    
    $tid = $_GET['tid'];
    $message_edit = "";
    
}else{
    $teamName = "Team Not Available";
    $teamCity = "City";
    $teamState = "State";
    $teamLevel = "";
    $teamURL = "N/A";
    $aboutText = "Team Not Available";
    $teamAdmin = "no";
    $tid = 0;
    $message_edit = "";
}


//Save the player details to the database
if (isset($_POST['saveTeam'])) { // Form has been submitted.
    $errors_edit = array();

    // perform validations on the form data
    $required_fields = array('teamName', 'teamCity', 'teamState' );
    $errors_edit = array_merge($errors_edit, check_required_fields($required_fields, $_POST));
    $fields_with_lengths = array('teamName' => 250, 'teamCity' => 250, 'teamState' => 2);
    $errors_edit = array_merge($errors_edit, check_max_field_lengths($fields_with_lengths, $_POST));
    
    // clean up the form data before putting it in the database
    $tid = trim(mysql_prep($_POST['tid']));
    $teamName = trim(mysql_prep($_POST['teamName']));
    $teamCity = trim(mysql_prep($_POST['teamCity']));
    $teamState = trim(mysql_prep($_POST['teamState']));
    $teamLevel = trim(mysql_prep($_POST['teamLevel']));
    $teamURL = trim(mysql_prep($_POST['teamURL']));
    $aboutText = trim(mysql_prep($_POST['aboutText']));

    // Proceed if there are no errors, else print the errors
    if (empty($errors_edit)) {
	//Put new info into database
	$update_team = "UPDATE teams SET
			    name = '{$teamName}',
			    teamURL = '{$teamURL}',
			    city = '{$teamCity}',
			    state = '{$teamState}',
			    teamLevel = '{$teamLevel}',
			    aboutText = '{$aboutText}'
			    WHERE teamID = {$tid}";
	$result_team = mysql_query($update_team);
	
	// test to see if the update occurred
	if (mysql_affected_rows() <= 1) {
	    // Success!
	    redirect_to("team.php?tid=$tid");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		
		redirect_to("team_edit.php?tid=$tid");
		
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


// button to navigate to videos page
if(isset($_POST['toVideos'])){ redirect_to("video.php"); }

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
    $( "button", ".logout" ).button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });
    $( "button", ".video" ).button({
        icons: {
            primary: "ui-icon-video"
        }
    });
});
</script>





<center>
<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;">
    <tr>
	<td colspan='2' style="height:50px;">
	    <table style="width:100%;text-align:center;">
		<tr>
		    <td style="height:50px;text-align:left;">
			<form action="team_edit.php" method="post">
			    <div class="video"><button type="submit" name="toVideos" value="PlayTagger" style="cursor:pointer;height:25px;font-size:12px;">Go To PlayTagger</button></div>
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
    <form action="team_edit.php" method="post">
    <table border='0' style="width:100%;">
    <tr>
	<td>
	    Team Name:
	    <input type="hidden" name='tid' value="<?php echo $tid; ?>">
	</td>
	<td>
	    <input type="text" name="teamName" value="<?php echo htmlentities($teamName); ?>" style="margin-bottom:5px;width:420px;" />
	</td>
    </tr>
    <tr>
	<td>
	    Team City:
	</td>
	<td>
	    <input type="text" name="teamCity" value="<?php echo htmlentities($teamCity); ?>" style="margin-bottom:5px;width:420px;" />
	</td>
    </tr>
    <tr>
	<td>
	    Team State:
	</td>
	<td>
	    <input type="text" name="teamState" value="<?php echo htmlentities($teamState); ?>" style="margin-bottom:5px;width:420px;" />
	</td>
    </tr>
    <tr>
	<td>
	    Team Website:
	</td>
	<td>
	    <input type="text" name="teamURL" value="<?php echo htmlentities($teamURL); ?>" style="margin-bottom:5px;width:420px;" />
	</td>
    </tr>
    <tr>
	<td>
	    Team Level:
	</td>
	<td>
	    <select name="teamLevel" style="width:425px;">
		<option value="xx" <?php if($teamLevel == 'xx'){echo "selected=\"selected\"";} ?> >non-selected</option>
		<option value="High School" <?php if($teamLevel == 'High School'){echo "selected=\"selected\"";} ?> >High School</option>
		<option value="High School Travel" <?php if($teamLevel == 'High School Travel'){echo "selected=\"selected\"";} ?> >High School Travel</option>
		<option value="College" <?php if($teamLevel == 'College'){echo "selected=\"selected\"";} ?> >College</option>
		<option value="Rep Team" <?php if($teamLevel == 'Rep Team'){echo "selected=\"selected\"";} ?> >Rep Team</option>
		<option value="Semi-Pro Team" <?php if($teamLevel == 'Semi-Pro Team'){echo "selected=\"selected\"";} ?> >Semi-Pro Team</option>
		<option value="Pro Team" <?php if($teamLevel == 'Pro Team'){echo "selected=\"selected\"";} ?> >Pro Team</option>
		<option value="National Team" <?php if($teamLevel == 'National Team'){echo "selected=\"selected\"";} ?> >National Team</option>
	    </select>
	</td>
    </tr>
    <tr>
	<td style="vertical-align:text-top;">
	    About the Team:
	</td>
	<td>
	    <textarea name="aboutText" cols=50 rows=7 ><?php echo $aboutText; ?></textarea>
	</td>
    </tr>
    <tr>
	<td colspan='2' style="height:50px;text-align:center;padding-top:10px;">
	    <input type="submit" name="saveTeam" value="Save Settings">
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