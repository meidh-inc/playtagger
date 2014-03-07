<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php	

// Get info from session
    $uid = $_SESSION['userid'];
		
		$query_oldpass = "SELECT userLevel, hashed_password FROM users
					WHERE id = '$uid' ";
		$pass_settings_set = mysql_query($query_oldpass);
		confirm_query($pass_settings_set);
		$old_hash_pass = mysql_fetch_array($pass_settings_set);
		$old_hashed_password = $old_hash_pass['hashed_password'];
		$userLevel = $old_hash_pass['userLevel'];
	
	
// START FORM PROCESSING: updates the user data in the database
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
	
	// prep for confirming old and new passwords plus
		$old_password = trim(mysql_prep($_POST['old_password']));
		$hashed_password = sha1($old_password);
		
		$new_password = trim(mysql_prep($_POST['new_password']));
		$new_hashed_password = sha1($new_password);
		
		$new_password_confirm = trim(mysql_prep($_POST['new_password_confirm']));
		
	// initialize an array to hold our errors
		$errors = array();
		
	// perform validations on the form data
		$required_fields = array('old_password', 'new_password', 'new_password_confirm');
		$errors = array_merge($errors, check_required_fields($required_fields));
		
		$fields_with_lengths = array('new_password' => 30);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
		
	// Database submission only proceeds if there were NO errors.
		if($old_hashed_password != $hashed_password) {
			$message = "Old Password is incorrect.";
		} else {
			if($new_password != $new_password_confirm) {
				$message = "The two lines for your new password did not match.";
			} else {
				if (empty($errors)) {
					$uid = $_SESSION['userid'];
					$query = 	"UPDATE users SET
									hashed_password = '{$new_hashed_password}'
								WHERE id = $uid ";
					$result = mysql_query($query);
					// test to see if the update occurred
					if (mysql_affected_rows() == 1) {
						// Success!
						$message = "<center><p style=\"color:blue;\">Your password was successfully changed. You may close this browser window or tab.</p></center>";
						//$message .= "<br />" . mysql_error() . mysql_affected_rows();
						/*
						if($userLevel == "player"){
							redirect_to("player_edit.php?passwordchanged=1");
						}else{
							redirect_to("coach_edit.php?passwordchanged=1");
						}
						*/
					} else {
						$message = "The page could not be updated.";
						$message .= "<br />" . mysql_error() . mysql_affected_rows();
						//redirect_to("account_settings.php?passchange=3");
					}
				} else {
					if (count($errors) == 1) {
						$message = "There was 1 error in the form.";
					} else {
						$message = "There were " . count($errors) . " errors in the form.";
					}
				}
			}
		// END FORM PROCESSING
			}
	} else { // Form has not been submitted.
		$old_password = "";
		$new_password = "";
		$new_password_confirm = "";
	}

?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		
<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.10.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
<script type="text/javascript">
	$(function() {
		$( "#loginpop" )
			.button()
			.click(function() {
				
			});
	});
</script>

<center>
<form action="change_password.php" method="post">
<table border='0' style="margin-top: 50px;font-family: sans-serif;">
	<tr>
		<td colspan='2'>
			<?php if (!empty($message)) {echo "<p style=\"color:red;\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
		</td>
	</tr>
	<tr>
		<td colspan='2' style="padding: 10px;">
			<h4>Set your new password</h4>
		</td>
	</tr>
	<tr>
		<td class="signup_rowspace" style="padding: 10px;">
			*Old Password:
		</td>
		<td  class="signup_rowspace" style="padding: 10px;">
			<input type="password" name="old_password" maxlength="30" value="<?php echo htmlentities($old_password); ?>" />
		</td>
	</tr>
	<tr>
		<td class="signup_rowspace" style="padding: 10px;">
			*New Password:
		</td>
		<td class="signup_rowspace" style="padding: 10px;">
			<input type="password" name="new_password" maxlength="30" value="<?php echo htmlentities($new_password); ?>" />
		</td>
	</tr>
	<tr>
		<td class="signup_rowspace" style="padding: 10px;">
			*Confirm New Password:
		</td>
		<td class="signup_rowspace" style="padding: 10px;">
			<input type="password" name="new_password_confirm" maxlength="30" value="<?php echo htmlentities($new_password_confirm); ?>" />
		</td>
	</tr>
	<tr>
		<td class="signup_rowspace" colspan='2' style="padding-top:20px;text-align: center;">
			<input type="submit" alt="submit" name="submit" value="Set New Password" id="loginpop" >
			<br><br>
			<span style="font-size: 10px;">(close this browser window or tab to cancel)</span>
		</td>
	</tr>
</table>
</form>
</center>
<br>
<?php include("includes/footer.php"); ?>