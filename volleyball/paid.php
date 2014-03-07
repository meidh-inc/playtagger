<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];
    
// Establish timezone and today's date
    date_default_timezone_set('America/Chicago');
    $today = date("Y-m-d");
    
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

    
if (isset($_POST['paid'])) {
    
    //create array to place any errors
	$errors = array();  
    
    // perform validations on the form data
        $required_fields = array('email');
	$errors = array_merge($errors, check_required_fields($required_fields, $_POST));
        $fields_with_lengths = array('email' => 255);
        $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
	
    // get the content from the variables
        $email = trim(mysql_prep($_POST['email']));
	
	
    // no errors detected so far, continue
        if ( empty($errors) ) {
	    
	    //look up if email belongs to coach or player
	    $query_userLevel = mysql_query("SELECT id, email, userLevel FROM users WHERE email = '{$email}' ");
	    $array_userLevel = mysql_fetch_array($query_userLevel);
	    
	    $userLevel = $array_userLevel['userLevel'];
	    
	    
	    //establish correct expDate
	    $paidDate = $today;
	    
	    if($userLevel == "player"){
		$expDate = date("Y-m-d", strtotime('+10 years'));
		
	    }elseif($userLevel == "coach"){
		$expDate = date("Y-m-d", strtotime('+1 year'));
		
	    }
	    
	    
	    //update the users table with new paidDate and expDate
	    $update_subDates = mysql_query("UPDATE users SET
							    paidDate = '{$paidDate}',
							    expDate = '{$expDate}'
					    WHERE email = '{$email}' ");
	    $link = "http://www.worldrugbyshop.com/playtagger.html";
	    
	    //send a confirmation email
//############################################################################

//Send the email
require_once("phpmailer/class.phpmailer.php");
$mail = new PHPMailer();

$body = "
	<h2>Thank you for subscribing to PlayTagger!</h2>
	The next time you <a href=\"".$link."\">login to PlayTagger</a>, you will have full access for the duration of your subscription.
	<br>
	Please feel free to let us know if you have any questions or concerns about PlayTagger.
	<br>
	<h4>Thank You!</h4>
	-the PlayTagger Team
";

$mail->Host       = "smtpout.secureserver.net";  // this relay server was recommended by godaddy via an email on 9/20/12
$mail->Port       = 25; // set the SMTP port for the server, godaddy says port 25

$mail->SetFrom("info@playtagger.com");
$mail->AddReplyTo("eric.nelson@meidh.com","Eric Nelson");
$mail->Subject    = "PlayTagger Subscription Processed";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress($email);
if(!$mail->Send()) {  //Email Failed
    echo "Mailer Error: " . $mail->ErrorInfo ."/n";
} else {  //Email Success
    //do nothing special
}

//############################################################################
	    
	    
	    //Deliver Success Message
	    $message = "<span style=\"color:green;\">
			The user account for " . $email . " (" . $userLevel . " account)
			has been marked paid. A confirmation email has been sent.</span>";
	
	
    // errors detected, relay them to the user
	} else {
	    if (count($errors) == 1) {
		$message = "There was 1 error in the form.";
	    } else {
		$message = "There were " . count($errors) . " errors in the form.";
	    }
	}
}else{
    
    //Initial load message should be blank    
    $message = "";

}

// button to navigate to videos page
if(isset($_POST['toVideos'])){redirect_to("video.php");}

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
$(function() {
		$( "input:submit, a, button", ".demo" ).button();
		$( "a", ".demo" ).click(function() { return false; });
	});
$(function() {
    $( "a" )
    .button()
});
</script>

<center>
<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;font-family:sans-serif;">
    <tr>
	<td style="text-align:center;padding:0px;margin:0px;height:10px;font-weight:bold;">
	    <?php if (!empty($message)) {echo "<p style=\"color:#C20000; font-size: 12px;\">" . $message . "</p>";} ?>
	    <?php if (!empty($errors)) { display_errors($errors); } ?>
	</td>
    </tr>
    <tr>
	<td style="height:50px;text-align:right;">
	    <form action="paid.php" method="post">
	    <div class="video"><button type="submit" name="toVideos" value="PlayTagger" style="cursor:pointer;height:25px;font-size:12px;">Go To PlayTagger</button></div>
	    </form>
	</td>
	<td style="height:50px;text-align:right;">
	    
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:gray thin solid;"></td></tr>
    <tr>
	<td colspan='2' style="height:150px;text-align:center;">
	    <form action="paid.php" method="post">
	    <input type="text" name="email" maxlength="100" style="width:160px;color:grey;margin-right: 90px;"
		   value="Purchaser Email Address" onfocus="if (this.value == 'Purchaser Email Address') this.value=''" onblur="if (this.value == '') this.value='Purchaser Email Address'"
		   title="Enter the purchaser's email address" />
	    <input type="submit" alt="mark paid" name="paid" value="Mark Paid" >
	    </form>
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="text-align:left;">
	    
	</td>
    </tr>
</table>
</center>
<?php include("includes/footer.php");?>