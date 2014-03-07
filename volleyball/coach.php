<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];

// Get userLevel
    $get_userLevel = mysql_query("SELECT userLevel FROM users WHERE id = '{$userid}' ");
    $array_userLevel = mysql_fetch_array($get_userLevel);
    $userLevel = $array_userLevel['userLevel'];
    
//Check for data
if(!isset($_GET['pid'])){
    $get_coachinfo = mysql_query("SELECT firstName, lastName, teamName, teamGender, teamLevel, aboutText FROM users
			      WHERE id = '{$userid}' ");
    $array_coachinfo = mysql_fetch_array($get_coachinfo);
    
    $firstName = $array_coachinfo['firstName'];
    $lastName = $array_coachinfo['lastName'];
    $teamNamec = $array_coachinfo['teamName'];
    $teamGender = $array_coachinfo['teamGender'];
    $teamLevel = $array_coachinfo['teamLevel'];
    $aboutText = $array_coachinfo['aboutText'];
    
    //get Team info from teams table
    $get_teaminfo = mysql_query("SELECT teamURL FROM teams WHERE name = '{$array_coachinfo['teamName']}' ");
    $array_teaminfo = mysql_fetch_array($get_teaminfo);
    
    $teamURL = $array_teaminfo['teamURL'];
    
}elseif(isset($_GET['pid'])){
    $Vpid = $_GET['pid'];
    $get_coachinfo = mysql_query("SELECT firstName, lastName, teamName, teamGender, teamLevel, aboutText FROM users
			      WHERE id = '{$_GET['pid']}' ");
    $array_coachinfo = mysql_fetch_array($get_coachinfo);
    
    $firstName = $array_coachinfo['firstName'];
    $lastName = $array_coachinfo['lastName'];
    $teamNamec = $array_coachinfo['teamName'];
    $teamGender = $array_coachinfo['teamGender'];
    $teamLevel = $array_coachinfo['teamLevel'];
    $aboutText = $array_coachinfo['aboutText'];
    
    //get Team info from teams table
    $get_teaminfo = mysql_query("SELECT teamURL FROM teams WHERE name = '{$array_coachinfo['teamName']}' ");
    $array_teaminfo = mysql_fetch_array($get_teaminfo);
    
    $teamURL = $array_teaminfo['teamURL'];
    
    //look up viewer info
    $get_Vinfo = mysql_query("SELECT email, firstName, lastName FROM users
				      WHERE id = '{$userid}' ");
    $array_Vinfo = mysql_fetch_array($get_Vinfo);
    
    if($array_Vinfo['firstName'] != ""){
        $Vemail = $array_Vinfo['email'];
	$VfirstName = $array_Vinfo['firstName'];
        $VlastName = $array_Vinfo['lastName'];
	
    }
    

}else{
    $firstName = "";
    $lastName = "";
    $teamNamec = "";
    $teamURL = "";
    $aboutText = "";
}


// button to send message clicked
if(isset($_POST['send'])){
    //get variables
	$Vpid = trim(mysql_prep($_POST['pid']));
	$toID = trim(mysql_prep($_POST['toID']));
	$fromID = trim(mysql_prep($_POST['fromID']));
	$timestamp_send = date("Y-m-d h:i:s a");
    
    //insert notification into database
	$query_send = mysql_query("INSERT INTO messages ( toID, fromID, sentDate
				    ) VALUES ( '{$toID}', '{$fromID}', '{$timestamp_send}')");
    
    //close window and reload the player's page
	redirect_to("coach.php?pid=$Vpid");
}

//button to close and save message viewer -- closes viewer and updates 
if(isset($_POST['messageclose'])){
    
    //gather a messages array to update through
    $get_messagesList = mysql_query("SELECT * from messages WHERE toID = '{$userid}' ");
    
    while($array_messagesList = mysql_fetch_array($get_messagesList)){
	
	$readNAM = $array_messagesList['id'];
	
	if(isset($_POST['read'.$readNAM])){  //mark it read
	    $readVAL = trim(mysql_prep($_POST['read'.$readNAM]));
	    $update_messages = mysql_query("UPDATE messages set isRead = 'read' WHERE id = {$readVAL}");
	}elseif((!isset($_POST['read'.$readNAM]))&&($array_messagesList['isRead'] == 'read')){  //mark it unread
	    $update_messages2 = mysql_query("UPDATE messages set isRead = '' WHERE id = {$readNAM}");
	}
	
    }
    
    // close viewer
	redirect_to("coach.php");
}


// button to navigate to videos page
if(isset($_POST['editMe'])){
    redirect_to("coach_edit.php");
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
<style>
.ui-dialog .ui-dialog-titlebar-close { display: none; }
</style>
<script type="text/javascript" src="jquery/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $( "#dialog-pay" ).dialog({
	<?php
	    if(($_SESSION['paid'] != 'paid') && (isset($_GET['pid']))){
		echo "autoOpen: true,";
	    }else{
		echo "autoOpen: false,";
	    }
	?>
	position: ["center",50],
	width: 750,
	resizable: false,
	modal: true,
    });
    $( "#payback" )
	.button()
	.click(function() {
	    //$( "#dialog-pay" ).dialog( "close" );
	});
    $( "#dialog-contact" ).dialog({
	autoOpen: false,
	position: ["center",50],
	width: 750,
	resizable: false,
	modal: true,
    });
    $( "#contactpop" )
	.button()
	.click(function() {
	    $( "#dialog-contact" ).dialog( "open" );
	});
    $( "#contactcancel" )
	.button()
	.click(function() {
	    $( "#dialog-contact" ).dialog( "close" );
	});
    $( "#dialog-messages" ).dialog({
	autoOpen: false,
	position: ["center",50],
	width: 750,
	resizable: false,
	modal: true,
    });
    $( "#messagespop" )
	.button()
	.click(function() {
	    $( "#dialog-messages" ).dialog( "open" );
	});
    $( "#messagesclose" )
	.button()
	.click(function() {
	    //$( "#dialog-messages" ).dialog( "close" );
	});
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
    $( "button", ".edit" ).button({
        icons: {
            primary: "ui-icon-pencil"
        }
    });
    $( "button", ".contact" ).button({
        icons: {
            primary: "ui-icon-mail-closed"
        }
    });
    $( "button", ".messages" ).button({
        icons: {
            primary: "ui-icon-mail-open"
        }
    });
});
$(function() {
    $( "a" )
    .button()
});
</script>

<!-- BEGIN ADD PAY POP UP BOX -->
<div id="dialog-pay" title="Subscription Required">
    <div id="pay"><center>
        <table border='0' style="width:450;height:400px;">
            <tr>
                <td style="text-align:right;padding:5px;">Please note that this feature requires a subscription to Playtagger.</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <?php
			if($userLevel == "player"){
			    $payLink = "http://www.worldrugbyshop.com/52302.html";
			}elseif($userLevel == "coach"){
			    $payLink = "http://www.worldrugbyshop.com/52303.html";
			}
		    ?>
		    <a href="<?php echo $payLink; ?>" target="_blank">Subscribe</a>
		    <a href="video.php" style="text-decoration:none">Back</a>
		    <!--<a href="video.php" style="text-decoration:none"><button id="payback" type="button" value="Back" >Back</button></a>-->
		</td>
            </tr>
        </table>
    </center></div>
</div>
<!-- END ADD PAY POP UP BOX -->
<!-- BEGIN SEND CONTACT POP UP BOX -->
<div id="dialog-contact" title="Send <?php echo $firstName; ?> the following message:">
    <div id="contact"><center>
        <table border='0' style="width:400;height:200px;">
            <tr>
                <td style="text-align:left;padding:5px;">
		    Hi <?php echo $firstName; ?>,<br>
		    Check out my videos and feel free to contact me at <a href="mailto:<?php echo $Vemail; ?>?Subject=Contacted%20on%20PlayTagger"><?php echo $Vemail; ?></a>.<br>
		    <br>
		    - <?php echo $VfirstName." ".$VlastName; ?><br>
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <form action="coach.php" method="post">
			<input type="hidden" name='pid' value="<?php echo $Vpid; ?>">
			<input type="hidden" name='toID' value="<?php echo $Vpid; ?>">
			<input type="hidden" name='fromID' value="<?php echo $userid; ?>">
		    <button id="payback" type="submit" name="send" value="Send" style="cursor:pointer;height:40px;font-size:18px;vertical-align:middle;">Send</button>
		    <button id="contactcancel" type="button" value="Cancel" >Cancel</button>
		    </form>
		</td>
            </tr>
        </table>
    </center></div>
</div>
<!-- END SEND CONTACT POP UP BOX -->
<!-- BEGIN ADD MESSAGES POP UP BOX -->
<div id="dialog-messages" title="Message Viewer">
    <div id="messages"><center>
    <form action="coach.php" method="post">
        <table border='0' style="width:400;height:300px;">
            <tr>
                <td>
		    <div style="width:650px;height:200px;padding:5px;border:solid thin white;overflow-y:auto;">
		    <table style="width:100%;">
			<tr>
			    <td style="text-align:center;">Viewed</td>
			    <td>Name</td>
			    <td>Email</td>
			    <td>Date</td>
			</tr>
		    <?php
			$get_messages = mysql_query("SELECT * from messages WHERE toID = '{$userid}' ORDER BY sentDate ASC ");
			
			while($array_messages = mysql_fetch_array($get_messages)){
			    //look up sender name and email address
				$get_sender = mysql_query("SELECT firstName, lastName, email from users WHERE id = '{$array_messages['fromID']}'");
				$array_sender = mysql_fetch_array($get_sender);
			    
			    //mark checkbox checked or unchecked
				if($array_messages['isRead'] == 'read'){
				    $read = "checked=\"checked\"";
				}else{
				    $read = "";
				}
			    
			    //Get message id
				$messageID = $array_messages['id'];
			    
			    echo "<tr>";
			    echo "<td style=\"text-align:center;\"> <input type=\"checkbox\" value=\"$messageID\" name=\"read$messageID\" $read /> </td>";
			    echo "<td>".$array_sender['firstName']." ".$array_sender['lastName']."</td>";
			    echo "<td>".$array_sender['email']."</td>";
			    echo "<td>".$array_messages['sentDate']."</td>";
			    echo "</tr>";
			    
			}
		    ?>
		    </table>
		    </div>
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <button id="messagesclose" type="submit" name="messageclose" value="Close" >Close</button>
		</td>
            </tr>
        </table>
	</form>
    </center></div>
</div>
<!-- END ADD MESSAGES POP UP BOX -->


<center>

<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;font-family:sans-serif;">
    <tr>
	<td colspan='3' style="height:50px;text-align:center;">
	    <form action="coach.php" method="post">
	    <table border='0' style="width:750px;">
		<tr>
		    <td>
			<!--<input type="submit" name="toVideos" value="Go To PlayTagger">-->
			<div class="video"><button type="submit" name="toVideos" value="PlayTagger" style="cursor:pointer;height:25px;font-size:12px;">Go To PlayTagger</button></div>
		    </td>
		    <td>
			<!--<input type="submit" name="editMe" value="Edit my profile">-->
			<?php
			    if(!isset($_GET['pid'])){
			    echo "<div class=\"edit\"><button type=\"submit\" name=\"editMe\" value=\"EditProfile\" style=\"cursor:pointer;height:25px;font-size:12px;\">Edit my profile</button></div>";
			    }
			?>
		    </td>
		    <td style="height:50px;text-align:right;">
			<div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
		</tr>
	    </table>
	    </form>
	</td>
    </tr>
    <tr><td colspan='3' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td style="width:250px;height:50px;text-align:center;">
	    <img src="images/defaultuser.jpg" alt="default user image" >
	</td>
	<td style="height:50px;text-align:left;">
	    <span style="font-size:26px;">
	    <?php echo $firstName; ?> <?php echo $lastName; ?></span><br>
	    <?php echo $teamNamec; ?><br>
	    Coaches<?php 
		if($teamGender == "male"){
		    echo " the male team";
		}elseif($teamGender == "female"){
		    echo " the female team ";
		}elseif($teamGender == "both"){
		    echo " the male and female teams";
		}
	    ?><br>
	    Team Level: <?php
			    if($teamLevel != "xx"){
				echo $teamLevel;
			    }else{
				echo "--";
			    }
			?><br>
	    Website: <?php echo $teamURL; ?><br>
    	</td>
	<td style="width:250px;height:50px;text-align:center;">
	    <table>
		<tr>
		    <td style="height:180px;width:220px;">
			<?php
			    if(isset($_GET['pid'])){  //Viewing another Player's page
				//echo "<a href=\"video.php?pid=".$_GET['pid']."\" style=\"text-decoration:none\"><div class=\"video\"><button type=\"button\" name=\"toMyGames\" value=\"MyGames\" style=\"cursor:pointer;height:25px;font-size:12px;\">Watch My Games</button></div></a>";
				//echo "<br>";
				//Show Contact Me Button
				
				echo "<div class=\"contact\"><button id=\"contactpop\" name=\"contactme\" value=\"Contact Me\" style=\"cursor:pointer;height:25px;font-size:12px;\">Contact Me</button></div>";
				
			    }else{ //Viewing my own page
				//echo "<a href=\"video.php?pid=".$userid."\" style=\"text-decoration:none\"><div class=\"video\"><button type=\"button\" name=\"toMyGames\" value=\"MyGames\" style=\"cursor:pointer;height:25px;font-size:12px;\">Watch My Games</button></div></a>";
				//echo "<br>";
				
				//Count Unread Messages
				    $get_messageCount = mysql_query("SELECT id FROM messages WHERE toID = '{$userid}' AND isRead != 'read' ");
				    $messageCount = mysql_num_rows($get_messageCount);
				    if($messageCount == 1){$notice = "Message";}else{$notice = "Messages";}
				
				//Show Messages Button
				    echo "<div class=\"messages\"><button id=\"messagespop\" name=\"messages\" value=\"View Messages\" style=\"cursor:pointer;height:25px;font-size:12px;\">$messageCount new $notice</button></div>";
			    }
			?>
		    </td>
		</tr>
	    </table>
	</td>
    </tr>
    <tr>
	<td colspan='3' style="height:100px;text-align:left;vertical-align:text-top;padding-top:10px;padding-left:50px;">
	    About the team:<br> <?php echo nl2br($aboutText); ?>
	</td>
    </tr>
    <tr>
	<td colspan='3' style="height:50px;text-align:center;padding-top:10px;">
	    
	</td>
    </tr>
    <tr><td colspan='3' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='3' style="text-align:center;">
	    
	</td>
    </tr>
</table>
</center>
<?php include("includes/footer.php");?>