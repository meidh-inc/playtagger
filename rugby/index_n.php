<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php //Process Log In Action and set up an email queue

if(isset($_GET['n'])){ //easter egg
    echo "Hi Everyone!";
}

// Set Timezone
    date_default_timezone_set('America/Chicago');

if(isset($_GET['kid'])){ //user might be logging in for the first time, this is signup verification check
    $userid = $_GET['uid'];
    $signup_key = $_GET['kid'];
    $check_verification = mysql_query("SELECT verified FROM users WHERE id = '{$userid}' ");
    $array_verification = mysql_fetch_array($check_verification);
    if($array_verification['verified'] != 'yes'){ // not yet verified, perform verification
	if($array_verification['verified'] == $signup_key){  // account verified!
	    $update_verified = "UPDATE users SET verified = 'yes' WHERE id = '{$userid}' ";
            $result_verified = mysql_query($update_verified);
            redirect_to('http://www.worldrugbyshop.com/playtagger.html');
	}else{ // verification keys do not match
	    $message_log = "Account Verification Failed. Please email help@playtagger.com for assistance.";
	}
    }else{  // account has already been verified
	// do nothing
    }
}

if (isset($_POST['login'])) { // The user clicked 'login'
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
	
    // translate the entered password to hashed
        $hashed_password = sha1($password_log);
	
    // no errors detected so far, continue
        if ( empty($errors_log) ) {
		
	    // Check database to see if username and the hashed password exist there.
		$query_login = "SELECT id, email, verified, userLevel, firstName, paidDate, expDate FROM users WHERE email = '{$email_log}'
			    AND hashed_password = '{$hashed_password}' LIMIT 1";
		$result_login = mysql_query($query_login);
		confirm_query($result_login);
		$found_user = mysql_fetch_array($result_login);
		
	    // Pass or not pass
	    if($found_user['verified'] == 'yes'){
		if (mysql_num_rows($result_login) == 1) {  // Success
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
			$_SESSION['userid'] = $found_user['id'];
			$_SESSION['useremail'] = $found_user['email'];
                        $_SESSION['paid'] = $payStatus;
			
		    // get timestamp for this login
			$timestamp_login = date("Y-m-d h:i:s a");
			
		    // update the user info with new last_login stamp
			$query_timestamp = mysql_query("UPDATE users SET last_login = '{$timestamp_login}'
							WHERE id = {$found_user['id']}");
			
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
	
}elseif (isset($_POST['signup'])) {  // user clicked 'signup'
	$errors_sign = array();  //create array to place any errors

    // perform validations on the form data
	$required_fields = array('email_sign', 'confirm_email', 'password_sign', 'confirm_password', 'userLevel' );
	$errors_sign = array_merge($errors_sign, check_required_fields($required_fields, $_POST));
	$fields_with_lengths = array('email_sign' => 250, 'confirm_email' => 250,
					'password_sign' => 30, 'confirm_password' => 30);
	$errors_sign = array_merge($errors_sign, check_max_field_lengths($fields_with_lengths, $_POST));
    
    // get content from the variables and clean up
	$email_sign = trim(mysql_prep($_POST['email_sign']));
	$confirm_email = trim(mysql_prep($_POST['confirm_email']));
	$password_sign = trim(mysql_prep($_POST['password_sign']));
	$confirm_password = trim(mysql_prep($_POST['confirm_password']));
        $userLevel = trim(mysql_prep($_POST['userLevel']));
    
    // translate the entered password to hashed
	$hashed_password = sha1($password_sign);
    
    // check for unique email address
	$query_verify_email = mysql_query("SELECT * FROM users WHERE email = '$email_sign' AND verified != 'invited' ");

    // verify password match
        if($password_sign != $confirm_password) {  // passwords do not match, deliver error message
			$message_sign = "Oops, your passwords don't match. Please re-enter them.";
    
    // passwords good, verify email address
	} elseif(filter_var($email_sign, FILTER_VALIDATE_EMAIL) != TRUE){  // incorrect email format, deliver error message
			$message_sign = "Oops! Please double check your email address. (format example: joe@example.com)";
    
    // email format good, verify email match
	} elseif($email_sign != $confirm_email){  // emails do not match, deliver error message
			$message_sign = "Oops, your email addresses don't match. Please re-enter them.";
    
    // emails and passwords match, email format passed, verify the email is unique/new
	} elseif(mysql_num_rows($query_verify_email) != 0){  // email is already in the system, deliver error message
	    $message_sign = "It looks like you already have an account.  Please try logging in.";
    
    // email and password are matching and verified
	} else {
	    
	    if ( empty($errors_sign) ) {  // no errors found, sign the user up
		
		// Generate the confirmation code to be emailed
		    $conf_code = substr(md5(uniqid(rand(), true)), -16, 16);
		
		// Get timestamp for signup
		    $timestamp_sign = date("Y-m-d h:i:s a");
		    $key = md5(uniqid(rand(), TRUE));
		    
		// Enter user info into DB
                $check_invite = mysql_query("SELECT verified FROM users WHERE email = '{$email_sign}' ");
                $array_invite = mysql_fetch_array($check_invite);
                if($array_invite['verified'] == 'invited'){   //new user was invited, update their invite temp profile
                    $query_sign = "UPDATE users SET
                                        verified = '{$key}',
                                        userLevel = '{$userLevel}',
                                        hashed_password = '{$hashed_password}',
                                        signup_date = '{$timestamp_sign}'
                                        WHERE email = '{$email_sign}'
                                        ";
                }else{   //new user was not invited, add them as new
                    $query_sign = "INSERT INTO users ( email, verified, userLevel, hashed_password, signup_date
				) VALUES ( '{$email_sign}', '{$key}', '{$userLevel}', '{$hashed_password}', '{$timestamp_sign}')";
                }
                $result_sign = mysql_query($query_sign, $connection);
		
		if ($result_sign) { //user was successfully created, send them a confirmation email
				
		    


//############################################################################

//get user_id and generate key
$get_uid = mysql_query("SELECT id FROM users WHERE email = '{$email_sign}' AND hashed_password = '{$hashed_password}' AND signup_date = '{$timestamp_sign}' ");
$array_uid = mysql_fetch_array($get_uid);
$uid = $array_uid['id'];

//Send the email
require_once("phpmailer/class.phpmailer.php");
$mail = new PHPMailer();

$body = "
	<h2>Welcome to PlayTagger! Thank you for signing up.</h2>
	Please follow the link to <a href=\"http://playtagger.com/rugby/index.php?uid=$uid&kid=$key\">VERIFY</a> your email and log in for the first time.
	<br>
	<h4>Thank You for joining!</h4>
	-the PlayTagger Dev team
";

$mail->Host       = "smtpout.secureserver.net";  // this relay server was recommended by godaddy via an email on 9/20/12
$mail->Port       = 25; // set the SMTP port for the server, godaddy says port 25

$mail->SetFrom("info@playtagger.com");
$mail->AddReplyTo("eric.nelson@meidh.com","Eric Nelson");
$mail->Subject    = "Confirm PlayTagger Signup and Verify Email";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress($email_sign);
if(!$mail->Send()) {  //Email Failed
    echo "Mailer Error: " . $mail->ErrorInfo ."/n";
} else {  //Email Success
    //echo "Message sent! to: Someone at ".$email_to." \n";
    redirect_to('index.php');
}

//############################################################################

    


		} else {  // creating user failed
		    $message_sign = "The user could not be created. Please try again.";
		    $message_sign .= "<br />" . mysql_error();
		}
	
	// errors detected, relay them to the user
	    } else {  // errors were found
		if (count($errors) == 1) {
		    $message = "There was 1 error in the form.";
		} else {
		    $message = "There were " . count($errors) . " errors in the form.";
		}
	    }
	}


    //blank login related variables
	$message_log = "";
	$email_log = "";
	$password_log = "";
	
	
}elseif (isset($_POST['forgot_password'])) {  // user has forgotten password
    
    
    
    
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
	    
	    // check for incoming messages
		if (isset($_GET['logout']) && $_GET['logout'] == 1) {  // user logged out
			$message = "<center><p style=\"color:blue; font-size: 12px;\">You are now logged out.</p></center>";
		}
		if (isset($_GET['newuser']) && $_GET['newuser'] == 1) {  // new user coming in
			$message = "<center>
				<p style=\"color:blue; font-size: 12px;\">Log in to complete the process.</p></center>";
		}
		
	}
?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript">
	$(function() {
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$( "#dialog-login" ).dialog({
			<?php 
			    if (isset($_POST['login'])) {
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
		$( "#loginpop" )
			.button()
			.click(function() {
				$( "#dialog-login" ).dialog( "open" );
			});
		$( "#dialog-signup" ).dialog({
			<?php 
			    if (isset($_POST['signup'])) {
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
		$( "#signuppop" )
			.button()
			.click(function() {
				$( "#dialog-signup" ).dialog( "open" );
			});
                $( "#dialog-video" ).dialog({
                        autoOpen: false,
			position: ["center",50],
			width: 600,
			resizable: false,
			modal: true,
		});
		$( "#vidpop" )
			.button()
			.click(function() {
				$( "#dialog-video" ).dialog( "open" );
			});
	});
	$(function() {
		$( "input:submit, a, button", ".demo" ).button();
		$( "a", ".demo" ).click(function() { return false; });
	});
</script>

<!-- BEGIN LOGIN POP UP BOX -->
<div id="dialog-login" title="Login">
<form id="loginform" action="index.php" method="post">
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
	<td><input type="text" name="email_log" maxlength="100" class="login_textboxes" style="width:160px;"
		value="<?php echo htmlentities($email_log); ?>" /></td>
    </tr>
    <tr>
	<td>Password:</td>
	<td><input type="password" name="password_log" maxlength="30" class="login_textboxes" style="width:160px;"
		value="<?php echo htmlentities($password_log); ?>" /></td>
    </tr>
    <tr>
        <td colspan='2' style="padding-top:20px;text-align:center;">
	    <div class="demo"><input type="submit" alt="login" name="login" value="Login" ></div>
        </td>
    </tr>
</table>
</form>
</center>
</div>
<!-- END LOGIN POP UP BOX -->
<!-- BEGIN SIGNUP POP UP BOX -->
<div id="dialog-signup" title="Signup">
<form id="signupform" action="index.php" method="post">
<center>
<table border='0'>
    <tr>
	<td colspan="2" align="center">
	    <?php if (!empty($message_sign)) {echo "<p style=\"color:#C20000; font-size: 12px;\">" . $message_sign . "</p>";} ?>
	    <?php if (!empty($errors_sign)) { display_errors($errors_sign); } ?>
	</td>
    </tr>
    <tr>
	<td>Email:</td>
	<td><input type="text" name="email_sign" maxlength="100" class="login_textboxes" style="width:200px;"
		value="<?php echo htmlentities($email_sign); ?>" /></td>
    </tr>
    <tr>
	<td>Confirm Email:</td>
	<td><input type="text" name="confirm_email" maxlength="100" class="login_textboxes" style="width:200px;"
		value="<?php echo htmlentities($confirm_email); ?>" /></td>
    </tr>
    <tr>
	<td>Password:</td>
	<td><input type="password" name="password_sign" maxlength="30" class="login_textboxes" style="width:200px;"
		value="<?php echo htmlentities($password_sign); ?>" /></td>
    </tr>
    <tr>
	<td>Confirm Password:</td>
	<td><input type="password" name="confirm_password" maxlength="30" class="login_textboxes" style="width:200px;"
		value="<?php echo htmlentities($confirm_password); ?>" /></td>
    </tr>
    <tr>
	<td style="text-align:center;padding-top:30px;padding-bottom:30px;">
            I am a:
            
        </td>
        <td style="text-align:left;padding-top:30px;padding-bottom:30px;">
            <input type="radio" name="userLevel" value="player" >Player
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="userLevel" value="coach" >Coach
	</td>
    </tr>
    <tr>
	<td colspan='2'>
            <!--
	    <table border='0' style="width:100%;padding:20px;">
		<tr>
		    <td style="text-align:right;">
			<input type="checkbox" name="terms" value="agree" />
		    </td>
		    <td style="width:300px;font-size:12px;">
			I have read and agree to the <a href="terms.php" target="_blank">Terms and Conditions</a>.
		    </td>
		</tr>
	    </table>
            -->
	</td>
    </tr>
    <tr>
	<td colspan='2' style="color:#f4911e;font-size:10px;text-align:center;">
	    Required for first login: Check your email for verification link after signup
	</td>
    </tr>
    <tr>
        <td colspan='2' style="padding-top:20px;text-align:center;">
	    <div class="demo"><input type="submit" alt="signup" name="signup" value="Signup"></div>
        </td>
    </tr>
</table>
</form>
</center>
</div>
<!-- END SIGNUP POP UP BOX -->
<!-- BEGIN Video POP UP BOX -->
<div id="dialog-video" title="Welcome to PlayTagger">
<center>
<table>
    <tr>
        <td colspan='2' style="padding-top:20px;text-align:center;">
	    <iframe width="480" height="360" src="http://www.youtube.com/embed/Kli2X8rvzxA?rel=0" frameborder="0" allowfullscreen></iframe>
        </td>
    </tr>
</table>
</center>
</div>
<!-- END Video POP UP BOX -->

<center>
<table border='0' style="width:795px;background-color:#E6E6E6;margin:0px;margin-top:0px;padding:0px;">
    <tr>
	<td rowspan='2' style="width:450px;">
	    <img src="images/playtagger_logop.png" alt="Playtagger Logo" id="vidpop" style="border:none;border-top-right-radius:0px;border-top-left-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;">
            
        </td>
	<td style="height:90px;text-align:center;padding:5px;padding-right:40px;vertical-align:bottom;">
	    <input type="submit" alt="loginpop" name="loginpop" value="Login" id="loginpop" >
	    <input type="submit" alt="signuppop" name="signuppop" value="Signup" id="signuppop" >
	</td>
    </tr>
    <tr>
        <td style="vertical-align:top;padding-top:10px;padding-left:10px;padding-right:25px;font-family:sans-serif;font-size:15px;">
            <span style="color:#9BC21B;font-weight:bold;">PlayTagger</span> is your hassle-free, online highlight reel.
            <br><br>
            Tag game highlights of you and your friends - and college, National team, and professional
            scouts can instantly see your online highlight reel!
            <br><br>
            No matter where you want to go, <span style="color:#9BC21B;font-weight:bold;">PlayTagger</span> is the fastest, easiest way to be seen!
        </td>
    </tr>
    <tr>
        <td colspan='2' style="text-align:center;">
            <img src="images/how.png" alt="Playtagger How" >
        </td>
    </tr>
</table>
</center>
</div>
<?php include("includes/footer_index.php");?>