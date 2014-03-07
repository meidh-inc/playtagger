<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php	
	
	require_once("phpmailer/class.phpmailer.php");
	
// START FORM PROCESSING: sets and sends new password

	if (isset($_POST['submit'])) {
		
	//grab submitted email address
		$email_address = $_POST['email_address'];
		
	//Check to make sure email address is in the system, continue only if it is.  If not deliver error message.
		$check_email = mysql_query("SELECT email FROM userlog WHERE email = '{$email_address}'");
		
		if(mysql_num_rows($check_email) != 0){
			
			//assign a new password to send
				$new_password = rand(10000,99999);
				$new_hashed_password = sha1($new_password);
				
			//initialize an array to hold errors
				$errors = array();
				
			//perform validations on the form data
				$required_fields = array('email_address');
				$errors = array_merge($errors, check_required_fields($required_fields));
				
				$fields_with_lengths = array('email_address' => 100);
				$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
				
			//Database submission only proceeds if there were NO errors.
				if (empty($errors)) {
					$query = "UPDATE userlog SET
							hashed_password = '{$new_hashed_password}'
						WHERE email = '{$email_address}' ";
					$result = mysql_query($query);
					// test to see if the update occurred
					if (mysql_affected_rows() == 1) {
						// Success! Prepare and send the email
							
							$query_user_data = mysql_query("SELECT firstName, lastName FROM userlog
										       WHERE email = '{$email_address}' ");
							$user_data_array = mysql_fetch_array($query_user_data);
							$first_name = $user_data_array['firstName'];
							$last_name = $user_data_array['lastName'];
							$username = $email_address;
							
						$mail = new PHPMailer();
						$body = "<h2>Hi $first_name,</h2>
							<h2>Your password has been reset per your request:</h2>
							<br>
							Your username is: $username
							<br>
							Your password is: $new_password
							<h4>Change your password from your Profile Edit page.</h4>
							<br>
							If you did not request a password change, log in using the above password to change it.<br>
							Once you have changed your password, reply to this email to report the incident.
							<br>
							-the PlayTagger Team
							";
							
							$mail->Host       = "smtpout.secureserver.net";  // this relay server was recommended by godaddy via an email on 9/20/12
							$mail->Port       = 25; // set the SMTP port for the server, godaddy says port 25
							
							$mail->SetFrom("help@playtagger.com");
							$mail->AddReplyTo("eric.nelson@meidh.com","Eric Nelson");
							$mail->Subject    = "Temporary PlayTagger Password";
							$mail->AltBody    = "To view the message, please use an HTML compatible email viewer"; // optional, comment out and test
							$mail->MsgHTML($body);
							$mail->AddAddress($email_address);
							if(!$mail->Send()) {
								$message = "Mailer Error: " . $mail->ErrorInfo;
							} else {
								//Success, email away
								redirect_to("index.php");
								}
					} else {
						$message = "The page could not be updated.";
						$message .= "<br />" . mysql_error() . mysql_affected_rows();
					}
				} else {
					if (count($errors) == 1) {
						$message = "There was 1 error in the form.";
					} else {
						$message = "There were " . count($errors) . " errors in the form.";
					}
				}
				
			//End Form Processing 
				
		}else{
			//email address not found
			$message = "Email address not found.  Please try again.";
		}
	} else { // Form has not been submitted.
		$email_address = "";
	}

?>
<?php
		if (isset($_POST['cancel'])) {
			redirect_to("index.php");
		}
?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		
<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript">
	$(function() {
		$( "#loginpop" )
			.button()
			.click(function() {
				
			});
	});
</script>



<form action="forgot.php" method="post">
<center>
<table border='0' style="width:795px;background-color:#E6E6E6;margin:0px;margin-top:0px;padding:0px;">
    <tr>
	<td rowspan='2' style="width:450px;">
	    <img src="images/playtagger_logo.png" alt="Playtagger Logo" >
	</td>
	<td style="height:90px;text-align:center;padding:5px;vertical-align:bottom;font-family:sans-serif;">
		<?php if (!empty($message)) {echo "<p style=\"color:red;\">" . $message . "</p>";} ?>
		<?php if (!empty($errors)) { display_errors($errors); } ?>
		<br>
		You will receive an email containing<br>
		your temporary password.
	</td>
    </tr>
    <tr>
        <td style="text-align: center;vertical-align:top;padding-top:10px;padding-left:10px;font-family:sans-serif;font-size:15px;">
            Email Address: <input type="text" name="email_address" maxlength="100" style="width:200px;"
				value="<?php echo htmlentities($email_address); ?>" />
		<br><br>
		<input type="submit" alt="submit" name="submit" value="Request Temporary Password" id="loginpop" >
		<br><br>
		<span style="font-size: 10px;">(click back button to cancel)</span>
	</td>
    </tr>
</table>
</center>
</form>
<?php include("includes/footer_index.php"); ?>