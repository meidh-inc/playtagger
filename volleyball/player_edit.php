<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];

//Check for data
$get_playerinfo = mysql_query("SELECT firstName, lastName, birthday, hsGrad, colGrad, aboutText FROM users
			      WHERE id = '{$userid}' ");
$array_playerinfo = mysql_fetch_array($get_playerinfo);

if($array_playerinfo['firstName'] != ""){
    $firstName = $array_playerinfo['firstName'];
    $lastName = $array_playerinfo['lastName'];
    $birthday = $array_playerinfo['birthday'];
    $hsGrad = $array_playerinfo['hsGrad'];
    $colGrad = $array_playerinfo['colGrad'];
    $aboutText = $array_playerinfo['aboutText'];
}else{
    $firstName = "";
    $lastName = "";
    $birthday = "";
    $hsGrad = "";
    $colGrad = "";
    $aboutText = "";
}


//Save the player details to the database
if (isset($_POST['savePlayer'])) { // Form has been submitted.
    $errors = array();

    // perform validations on the form data
    $required_fields = array('firstName', 'lastName' );
    $errors = array_merge($errors, check_required_fields($required_fields, $_POST));
    $fields_with_lengths = array('firstName' => 250, 'lastName' => 250);
    $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
    
    // clean up the form data before putting it in the database
    $firstName = trim(mysql_prep($_POST['firstName']));
    $lastName = trim(mysql_prep($_POST['lastName']));
    $birthday = trim(mysql_prep($_POST['birthday']));
    $hsGrad = trim(mysql_prep($_POST['hsGrad']));
    $colGrad = trim(mysql_prep($_POST['colGrad']));
    $aboutText = trim(mysql_prep($_POST['aboutText']));

    // Proceed if there are no errors, else print the errors
    if (empty($errors)) {
	//Put new info into database
	$update_user = "UPDATE users SET
			    firstName = '{$firstName}',
			    lastName = '{$lastName}',
			    birthday = '{$birthday}',
			    hsGrad = '{$hsGrad}',
			    colGrad = '{$colGrad}',
			    aboutText = '{$aboutText}'
			    WHERE id = {$userid}";
	$result_update = mysql_query($update_user);
	
	// test to see if the update occurred
	if (mysql_affected_rows() == 1) {
	    // Success!
	    redirect_to("player.php");
	} else {
	    if(mysql_error() == NULL){  // there are no affected rows and no errors (triggered when someone hits save without making changes)
		redirect_to("player_edit.php");
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
    $( "#bday" ).datepicker({
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
	yearRange: '1950:2012'
    });
    $( "button", ".logout" ).button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });
});
</script>

<!--<center>-->
<form action="player_edit.php" method="post">
<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;font-family: sans-serif;">
    <tr>
	<td style="height:50px;text-align:right;">
	    <input type="submit" name="toVideos" value="Go To PlayTagger">
	</td>
	<td style="height:50px;text-align:right;">
	    <div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td style="width:300px;height:50px;text-align:center;">
	    <img src="images/defaultuser.jpg" alt="default user image" >
	</td>
	<td style="height:50px;text-align:left;">
	    First Name: <input type="text" name="firstName" value="<?php echo htmlentities($firstName); ?>" style="margin-bottom:5px;" /><br>
	    Last Name: <input type="text" name="lastName" value="<?php echo htmlentities($lastName); ?>" style="margin-bottom:5px;" /><br>
	    Birthday: <input type="text" id="bday" name="birthday" value="<?php echo htmlentities($birthday); ?>" placeholder="click for calendar" style="width:120px;font-size:12px;margin-bottom:5px;"><br>
	    
	    High School Grad Year:
	    <select name="hsGrad" style="margin-bottom:5px;">
	    <?php
		$get_selectedhs = mysql_query("SELECT hsGrad FROM users WHERE id = '{$userid}' ");
		$array_selectedhs = mysql_fetch_array($get_selectedhs);
		$selectedhs = $array_selectedhs['hsGrad'];
		for($iHS=1980; $iHS<=2030; $iHS++){
		    if($selectedhs == $iHS){
			$select = "selected=\"selected\"";
		    }else{
			$select = "";
		    }
		    echo "<option value=\"".$iHS."\" ".$select.">".$iHS."</option>";
		}
	    ?>
	    </select>
	    <br>
	    College Grad Year: 
	    <select name="colGrad" style="margin-bottom:5px;">
		<option value="0" >N/A</option>
	    <?php
		$get_selectedcol = mysql_query("SELECT colGrad FROM users WHERE id = '{$userid}' ");
		$array_selectedcol = mysql_fetch_array($get_selectedcol);
		$selectedcol = $array_selectedcol['colGrad'];
		for($iCOL=1980; $iCOL<=2030; $iCOL++){
		    if($selectedcol == $iCOL){
			$select = "selected=\"selected\"";
		    }else{
			$select = "";
		    }
		    
		    echo "<option value=\"".$iCOL."\" ".$select.">".$iCOL."</option>";
		}
	    ?>
	    </select>
	    <br>
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
	    <input type="submit" name="savePlayer" value="Save Settings">
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="text-align:center;">
	    
	</td>
    </tr>
</table>
</form>
<!--</center>-->
<?php include("includes/footer.php");?>