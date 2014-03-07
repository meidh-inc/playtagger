<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];
    $sportID = $_SESSION['sport'];
    $sportName = $_SESSION['sportName'];



// button to navigate to videos page
if(isset($_POST['toVideos'])){redirect_to("video.php");}

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
<table border='0' style="width:740px;padding:0px;margin-top:0px;font-family:sans-serif;">
    <tr>
	<td style="height:50px;text-align:right;">
	    <form action="find_player.php" method="post">
	    <div class="video"><button type="submit" name="toVideos" value="PlayTagger" style="cursor:pointer;height:25px;font-size:12px;">Go To PlayTagger</button></div>
	    <!--<input type="submit" name="toVideos" value="Go To PlayTagger">-->
	    </form>
	</td>
	<td style="height:50px;text-align:right;">
	    <form action="find_player.php" method="post">
	    <div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
	    </form>
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:gray thin solid;"></td></tr>
    <tr>
	<td colspan='2' style="height:150px;text-align:left;">
	    <?php if($sportID == 3){echo "<div style=\"width:780px;overflow-x: scroll;\">";} ?>
	    <table border='0' style="width:100%;text-align:center;font-size:14px;">
		<tr>
		    <td <?php if($sportID == 3){echo "style=\"min-width: 80px;\"";} ?>>
			Player
		    </td>
		    <td <?php if($sportID == 3){echo "style=\"min-width: 60px;\"";} ?>>
			HS Grad
		    </td>
		    <td <?php if($sportID == 3){echo "style=\"min-width: 60px;\"";} ?>>
			Coll Grad
		    </td>
		    <td <?php if($sportID == 3){echo "style=\"min-width: 60px;\"";} ?>>
			Games
		    </td>
		    <td style="min-width: 75px;">
			<?php
			    if($sportID == 2){
				$event1="Service Ace";
			    }elseif($sportID == 3){
				$event1="TD Run";
			    }elseif($sportID == 4){
				$event1="Basket";
			    }else{
				$event1="Score";
			    }
			    echo $event1."s<br>per Game";
			?>
		    </td>
		    <td style="min-width: 75px;">
			<?php
			    if($sportID == 2){
				$event2="Kill";
			    }elseif($sportID == 3){
				$event2="TD Passe";
			    }elseif($sportID == 4){
				$event2="3 Pointer";
			    }else{
				$event2="Assist";
			    }
			    echo $event2."s<br>per Game";
			?>
		    </td>
		    <td style="min-width: 75px;">
			<?php
			    if($sportID == 2){
				$event3="Single Block";
			    }elseif($sportID == 3){
				$event3="TD Catche";
			    }elseif($sportID == 4){
				$event3="Free Throw";
			    }else{
				$event3="Tackle";
			    }
			    echo $event3."s<br>per Game";
			?>
		    </td>
		    <td style="min-width: 75px;">
			<?php
			    if($sportID == 2){
				$event4="Block Assist";
			    }elseif($sportID == 3){
				$event4="Nice Passe";
			    }elseif($sportID == 4){
				$event4="Rebound";
			    }else{
				$event4="Nice Run";
			    }
			    echo $event4."s<br>per Game";
			?>
		    </td>
		    <td style="min-width: 75px;">
			<?php
			    if($sportID == 2){
				$event5="Dig";
			    }elseif($sportID == 3){
				$event5="Nice Catche";
			    }elseif($sportID == 4){
				$event5="Assist";
			    }else{
				$event5="Won Possession";
			    }
			    echo $event5."s<br>per Game";
			?>
		    </td>
		    <td style="min-width: 75px;">
			<?php
			    if($sportID == 2){
				$event6="Set Assist";
			    }elseif($sportID == 3){
				$event6="Good Run";
			    }elseif($sportID == 4){
				$event6="Steal";
			    }else{
				$event6="Kick";
			    }
			    echo $event6."s<br>per Game";
			?>
		    </td>
		    <?php if($sportID == 3){echo "
		    <td style=\"min-width: 75px;\">
			Tackles<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			Good Blocks<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			INTs<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			Fumble Recoveries<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			Forced Fumbles<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			Kick-Off Returns<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			Punt Returns<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			Field Goals<br>per Game
		    </td>
		    <td style=\"min-width: 75px;\">
			PATs<br>per Game
		    </td>
		    ";} ?>
		</tr>
		<tr><td colspan='<?php if($sportID != 3){echo "10";}else{echo "19";} ?>' style="height:1px;border-bottom:solid gray thin;"></td></tr>
		<?php
		    if($sportID == 3){
			$event7 = "Tackle";
			$event8 = "Good Block";
			$event9 = "INT";
			$event10 = "Fumble Recovery";
			$event11 = "Forced Fumble";
			$event12 = "Kick-Off Return";
			$event13 = "Punt Return";
			$event14 = "Field Goal";
			$event15 = "PAT";
		    }
		    
		    
		    $get_players = mysql_query("SELECT userID, firstName, lastName FROM userlog
					       WHERE userLevel = 'player'
					       AND firstName != ''
					       ORDER BY lastName ");
		    while($array_players = mysql_fetch_array($get_players)){
			
			$get_sportPlayers = mysql_query("SELECT * FROM userinfo WHERE sportID = '{$sportID}' AND userID = '{$array_players['userID']}' ");
			while($array_sportPlayers = mysql_fetch_array($get_sportPlayers)){
			
			$get_playersinfo = mysql_query("SELECT hsGrad, colGrad FROM userinfo
					       WHERE userID = '{$array_sportPlayers['userID']}'
					       ");
			$array_playersinfo = mysql_fetch_array($get_playersinfo);
			
			$player = $array_players['firstName']." ".$array_players['lastName'];
			$pid = $array_sportPlayers['userID'];
			
			$get_gameCount = mysql_query("SELECT tagID, gameURL FROM tags
						    WHERE playerID = '{$array_sportPlayers['userID']}' AND sportID = '{$sportID}' GROUP BY gameURL ");
			$gameCount = mysql_num_rows($get_gameCount);
			if($gameCount == 0){
			    $gameCountb = 1;
			}else{
			    $gameCountb = $gameCount;
			}
			$get_scoreCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event1}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			$scoreCount = mysql_num_rows($get_scoreCount);
			$scoreper = round($scoreCount/$gameCountb, 1);
			$get_assistCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event2}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			$assistCount = mysql_num_rows($get_assistCount);
			$assistper = round($assistCount/$gameCountb, 1);
			$get_tackleCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event3}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			$tackleCount = mysql_num_rows($get_tackleCount);
			$tackleper = round($tackleCount/$gameCountb, 1);
			$get_nicerunCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event4}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			$nicerunCount = mysql_num_rows($get_nicerunCount);
			$nicerunper = round($nicerunCount/$gameCountb, 1);
			$get_wonpossCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event5}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			$wonpossCount = mysql_num_rows($get_wonpossCount);
			$wonpossper = round($wonpossCount/$gameCountb, 1);
			$get_kickCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event6}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			$kickCount = mysql_num_rows($get_kickCount);
			$kickper = round($kickCount/$gameCountb, 1);
			
			if($sportID == 3){
			    $get_TCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event7}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $TCount = mysql_num_rows($get_TCount);
			    $Tper = round($TCount/$gameCountb, 1);
			    $get_GBCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event8}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $GBCount = mysql_num_rows($get_GBCount);
			    $GBper = round($GBCount/$gameCountb, 1);
			    $get_INTCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event9}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $INTCount = mysql_num_rows($get_INTCount);
			    $INTper = round($INTCount/$gameCountb, 1);
			    $get_FRCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event10}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $FRCount = mysql_num_rows($get_FRCount);
			    $FRper = round($FRCount/$gameCountb, 1);
			    $get_FFCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event11}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $FFCount = mysql_num_rows($get_FFCount);
			    $FFper = round($FFCount/$gameCountb, 1);
			    $get_KORCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event12}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $KORCount = mysql_num_rows($get_KORCount);
			    $KORper = round($KORCount/$gameCountb, 1);
			    $get_PRCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event13}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $PRCount = mysql_num_rows($get_PRCount);
			    $PRper = round($PRCount/$gameCountb, 1);
			    $get_FGCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event14}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $FGCount = mysql_num_rows($get_FGCount);
			    $FGper = round($FGCount/$gameCountb, 1);
			    $get_PATCount = mysql_query("SELECT eventName FROM tags WHERE eventName = '{$event15}' AND sportID = '{$sportID}' AND playerID = '{$array_sportPlayers['userID']}' ");
			    $PATCount = mysql_num_rows($get_PATCount);
			    $PATper = round($PATCount/$gameCountb, 1);
			}
			
			echo "<tr>";
			    echo "<td style=\"text-align:left;\">";
				echo "<a href=\"player.php?pid=$pid\">".$player."</a>";
			    echo "</td>";
			    echo "<td>";
				echo $array_playersinfo['hsGrad'];
			    echo "</td>";
			    echo "<td>";
				echo $array_playersinfo['colGrad'];
			    echo "</td>";
			    echo "<td>";
				echo $gameCount;
			    echo "</td>";
			    echo "<td>";
				echo $scoreper;
			    echo "</td>";
			    echo "<td>";
				echo $assistper;
			    echo "</td>";
			    echo "<td>";
				echo $tackleper;
			    echo "</td>";
			    echo "<td>";
				echo $nicerunper;
			    echo "</td>";
			    echo "<td>";
				echo $wonpossper;
			    echo "</td>";
			    echo "<td>";
				echo $kickper;
			    echo "</td>";
			    if($sportID == 3){
				echo "<td>";
				    echo $Tper;
				echo "</td>";
				echo "<td>";
				    echo $GBper;
				echo "</td>";
				echo "<td>";
				    echo $INTper;
				echo "</td>";
				echo "<td>";
				    echo $FRper;
				echo "</td>";
				echo "<td>";
				    echo $FFper;
				echo "</td>";
				echo "<td>";
				    echo $KORper;
				echo "</td>";
				echo "<td>";
				    echo $PRper;
				echo "</td>";
				echo "<td>";
				    echo $FGper;
				echo "</td>";
				echo "<td>";
				    echo $PATper;
				echo "</td>";
			    }
			echo "</tr>";
		    }
		    }
		?>
	    </table>
	    <?php if($sportID == 3){echo "</div>";} ?>
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