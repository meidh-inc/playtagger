<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php //confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];

// Get userLevel
    $get_userLevel = mysql_query("SELECT userLevel FROM users WHERE id = '{$userid}' ");
    $array_userLevel = mysql_fetch_array($get_userLevel);
    $userLevel = $array_userLevel['userLevel'];

    
//Check for data
if(isset($_GET['tid'])){
    
    $tid = $_GET['tid'];
    
    // Is Current User the Admin?
    $check_admin = mysql_query("SELECT admin FROM teams WHERE teamID = '{$_GET['tid']}' ");
    $array_admin = mysql_fetch_array($check_admin);
    if($array_admin['admin'] == $userid){
	$teamAdmin = "yes";
    }else{
	$teamAdmin = "no";
    }
        
    //get Team info from teams table
    $get_teaminfo = mysql_query("SELECT name, teamURL, city, state, teamLevel, aboutText FROM teams WHERE teamID = '{$_GET['tid']}' ");
    $array_teaminfo = mysql_fetch_array($get_teaminfo);
    $teamName = $array_teaminfo['name'];
    $teamCity = $array_teaminfo['city'];
    $teamState = $array_teaminfo['state'];
    $teamLevel = $array_teaminfo['teamLevel'];
    $teamURL = $array_teaminfo['teamURL'];
    $aboutText = $array_teaminfo['aboutText'];
    

    //look up viewer info for messaging
    $get_Vinfo = mysql_query("SELECT email, firstName, lastName FROM users
				      WHERE id = '{$userid}' ");
    $array_Vinfo = mysql_fetch_array($get_Vinfo);
    
    if($array_Vinfo['firstName'] != ""){
        $Vemail = $array_Vinfo['email'];
	$VfirstName = $array_Vinfo['firstName'];
        $VlastName = $array_Vinfo['lastName'];
	
    }
    
    

}else{
    $teamName = "Team Not Available";
    $teamCity = "City";
    $teamState = "State";
    $teamLevel = "";
    $teamURL = "N/A";
    $aboutText = "Team Not Available";
    $teamAdmin = "no";
    $tid = 0;
}

// button to send message clicked
if(isset($_POST['send'])){
    //get variables
	$tid = trim(mysql_prep($_POST['tid']));
	$toTeamID = trim(mysql_prep($_POST['toTeamID']));
	$fromID = trim(mysql_prep($_POST['fromID']));
	$timestamp_send = date("Y-m-d h:i:s a");
    
    //insert notification into database
	$query_send = mysql_query("INSERT INTO messages ( toTeamID, fromID, sentDate
				    ) VALUES ( '{$toTeamID}', '{$fromID}', '{$timestamp_send}')");
    
    //close window and reload the team's page
	redirect_to("team.php?tid=$tid");
}

// button to navigate to edit the team information
if(isset($_POST['editTeam'])){
    $tid_edit = trim(mysql_prep($_POST['tid_edit']));
    redirect_to("team_edit.php?tid=$tid_edit");
}
// button to navigate to videos page
if(isset($_POST['toVideos'])){ redirect_to("video.php"); }

// button to navigate to team finder page
if(isset($_POST['BackToTeams'])){ redirect_to("find_team.php"); }

//Code for Logging Out
if (isset($_POST['logout'])) {redirect_to('logout.php');}

?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
<style>
.ui-dialog .ui-dialog-titlebar-close { display: none; }
</style>
<script type="text/javascript" src="jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $( "#dialog-pay" ).dialog({
	<?php
	    if(($_SESSION['paid'] != 'paid') && (isset($_GET['tid']))){
		echo "autoOpen: true,";
	    }else{
		echo "autoOpen: false,";
	    }
	?>
	position: ["center",50],
	width: 750,
	resizable: false,
	draggable: false,
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
    $( "button", ".back" ).button({
        icons: {
            primary: "ui-icon-arrowreturnthick-1-w"
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
<div id="dialog-contact" title="Send <?php echo $teamName; ?> the following message:">
    <div id="contact"><center>
        <table border='0' style="width:400;height:200px;">
            <tr>
                <td style="text-align:left;padding:5px;">
		    Hi <?php echo $teamName; ?>,<br>
		    Check out my videos and feel free to contact me at <a href="mailto:<?php echo $Vemail; ?>?Subject=Contacted%20on%20PlayTagger"><?php echo $Vemail; ?></a>.<br>
		    <br>
		    - <?php echo $VfirstName." ".$VlastName; ?><br>
		</td>
            </tr>
            <tr>
                <td colspan='2' style="text-align:center;height:50px;">
		    <form action="team.php" method="post">
			<input type="hidden" name='tid' value="<?php echo $_GET['tid']; ?>">
			<input type="hidden" name='toTeamID' value="<?php echo $_GET['tid']; ?>">
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



<center>
<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;font-family:sans-serif;">
    <tr>
	<td colspan='3' style="height:50px;text-align:center;">
	    <form action="team.php" method="post">
	    <table border='0' style="width:750px;">
		<tr>
		    <td>
			<!--<input type="submit" name="toVideos" value="Go To PlayTagger">-->
			<div class="video"><button type="submit" name="toVideos" value="PlayTagger" style="cursor:pointer;height:25px;font-size:12px;">Go To PlayTagger</button></div>
		    </td>
		    <td>
			<?php
			    if(isset($_GET['f'])){
			    echo "<div class=\"back\"><button type=\"submit\" name=\"BackToTeams\" value=\"Back\" style=\"cursor:pointer;height:25px;font-size:12px;\">Back to Teams List</button></div>";
			    }
			?>
		    </td>
		    <td>
			<!--<input type="submit" name="editMe" value="Edit my profile">-->
			<input type="hidden" name='tid_edit' value="<?php echo $tid; ?>">
			<?php
			    if($teamAdmin == "yes"){
				echo "<div class=\"edit\"><button type=\"submit\" name=\"editTeam\" value=\"EditProfile\" style=\"cursor:pointer;height:25px;font-size:12px;\">Edit Team Profile</button></div>";
			    }
			?>
		    </td>
		    <td style="height:50px;text-align:right;">
			<div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
		    </td>
		</tr>
	    </table>
	    </form>
	</td>
    </tr>
    <tr><td colspan='3' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="height:50px;text-align:left;padding:20px">
	    <span style="font-size:26px;"><?php echo $teamName; ?></span>
	    <br><br>
	    <?php echo $teamCity.", ".$teamState; ?>
	    <br>
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
			    if(isset($_GET['tid'])){  //Viewing another Player's page
				//echo "<a href=\"video.php?pid=".$_GET['pid']."\" style=\"text-decoration:none\"><div class=\"video\"><button type=\"button\" name=\"toMyGames\" value=\"MyGames\" style=\"cursor:pointer;height:25px;font-size:12px;\">Watch My Games</button></div></a>";
				//echo "<br>";
				//Show Contact Me Button
				
				echo "<div class=\"contact\"><button id=\"contactpop\" name=\"contactme\" value=\"Contact Me\" style=\"cursor:pointer;height:25px;font-size:12px;\">Contact Us</button></div>";
				
			    }elseif(!isset($_GET['tid'])){
				//Team ID not identified, no team loaded
				
			    }else{ //Viewing my own page
				//echo "<a href=\"video.php?pid=".$userid."\" style=\"text-decoration:none\"><div class=\"video\"><button type=\"button\" name=\"toMyGames\" value=\"MyGames\" style=\"cursor:pointer;height:25px;font-size:12px;\">Watch My Games</button></div></a>";
				//echo "<br>";
				
				//Count Unread Messages
				    $get_messageCount = mysql_query("SELECT id FROM messages WHERE toTeamID = '{$tid}' AND isRead != 'read' ");
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
	<td colspan='3' style="height:100px;text-align:left;vertical-align:text-top;padding:20px;">
	    About the team: <br>
	    <?php echo nl2br($aboutText); ?>
	</td>
    </tr>
    <tr>
	<td colspan='3' style="height:50px;text-align:left;vertical-align:text-top;padding:20px;">
	    Coaches<br><br>
	    <?php
		$get_coachList = mysql_query("SELECT id, firstName, lastName, teamGender FROM users WHERE teamName = '{$teamName}' AND teamName != '' AND userLevel = 'coach' ");
		while($array_coachList = mysql_fetch_array($get_coachList)){
		    if($array_coachList['teamGender'] == "male"){
			$teamGen = " coaches the male team";
		    }elseif($array_coachList['teamGender'] == "female"){
			$teamGen = " coaches the female team)";
		    }elseif($array_coachList['teamGender'] == "both"){
			$teamGen = " coaches the male and female teams";
		    }
		    echo "<a href=\"coach.php?pid=".$array_coachList['id']."\">".$array_coachList['firstName']." ".$array_coachList['lastName']."</a>, ".$teamGen."<br>";
		}
		
	    ?>
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