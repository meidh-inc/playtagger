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
    
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(0);

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
<table border='0' style="width:795px;background-color:#E6E6E6;padding:0px;margin-top:0px;font-family:sans-serif;">
    <tr>
	<td style="height:50px;text-align:right;">
	    <form action="find_team.php" method="post">
	    <div class="video"><button type="submit" name="toVideos" value="PlayTagger" style="cursor:pointer;height:25px;font-size:12px;">Go To PlayTagger</button></div>
	    <!--<input type="submit" name="toVideos" value="Go To PlayTagger">-->
	    </form>
	</td>
	<td style="height:50px;text-align:right;">
	    <form action="find_team.php" method="post">
	    <div class="logout"><button type="submit" name="logout" value="logout" style="cursor:pointer;height:25px;font-size:12px;">Logout</button></div>
	    </form>
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:gray thin solid;"></td></tr>
    <tr>
	<td colspan='2' style="height:150px;text-align:left;">
	    <table border='0' style="width:100%;text-align:left;font-size:14px;">
		<tr>
		    <td style="text-align:center;border-right:gray thin solid;">
			Team
		    </td>
		    <td style="text-align:center;border-right:gray thin solid;">
			Location
		    </td>
		    <td style="text-align:center;border-right:gray thin solid;">
			Level
		    </td>
		    <!--<td>
			Coach		(TAKE OUT)
		    </td>-->
		    <!--<td>
			Website
		    </td>-->
		    <td style="text-align:center;border-right:gray thin solid;">
			Games
		    </td>
		    <td style="text-align:center;">
			Scores<br>per Game
		    </td>
		    <!--<td>
			Assists<br>per Game		(TAKE OUT)
		    </td>
		    <td>
			Tackles<br>per Game		(TAKE OUT)
		    </td>
		    <td>
			Nice Runs<br>per Game		(TAKE OUT)
		    </td>
		    <td>
			Won Possessions<br>per Game		(TAKE OUT)
		    </td>
		    <td>
			Kicks<br>per Game		(TAKE OUT)
		    </td>-->
		</tr>
		<tr><td colspan='11' style="height:1px;border-bottom:solid gray thin;"></td></tr>
		<?php
		    $get_teams = mysql_query("SELECT teamID, name, link, city, state, level FROM teams WHERE sportID = '{$sportID}'
					       ORDER BY name ");
		    while($array_teams = mysql_fetch_array($get_teams)){
			$teamName = $array_teams['name'];
			
			$get_coach1 = mysql_query("SELECT userID, teamGender FROM userinfo
						 WHERE teamID = '{$array_teams['teamID']}' ");
			$array_coach1 = mysql_fetch_array($get_coach1);
			
			$get_coach = mysql_query("SELECT firstName, lastName FROM userlog
						 WHERE userLevel = 'coach' AND userID = '{$array_coach1['userID']}' ");
			$array_coach = mysql_fetch_array($get_coach);
			$coachName = $array_coach['firstName']." ".$array_coach['lastName'];
			
			$teamID = $array_teams['teamID'];
			$teamLocation = $array_teams['city'].", ".$array_teams['state'];
			
			$get_totalgames = mysql_query("SELECT gameID FROM games WHERE hometeamID = '{$array_teams['teamID']}' OR awayteamID = '{$array_teams['teamID']}' ");
			$totalgames = mysql_num_rows($get_totalgames);
			
			$get_gameCount = mysql_query("SELECT tagID, gameURL FROM tags
						    WHERE teamID = '{$array_teams['teamID']}' GROUP BY gameURL ");
			$gameCount = mysql_num_rows($get_gameCount);
			if($gameCount == 0){
			    $gameCountb = 1;
			}else{
			    $gameCountb = $gameCount;
			}
			$get_scoreCount = mysql_query("SELECT eventName FROM tags WHERE eventName = 'Score' AND teamID = '{$array_teams['teamID']}' ");
			$scoreCount = mysql_num_rows($get_scoreCount);
			$scoreper = round($scoreCount/$gameCountb, 1);
			
			/*
			$get_assistCount = mysql_query("SELECT type FROM tags WHERE type = 'Assist' AND team = '{$teamName}' ");
			$assistCount = mysql_num_rows($get_assistCount);
			$assistper = round($assistCount/$gameCountb, 1);
			$get_tackleCount = mysql_query("SELECT type FROM tags WHERE type = 'Tackle' AND team = '{$teamName}' ");
			$tackleCount = mysql_num_rows($get_tackleCount);
			$tackleper = round($tackleCount/$gameCountb, 1);
			$get_nicerunCount = mysql_query("SELECT type FROM tags WHERE type = 'Nice Run' AND team = '{$teamName}' ");
			$nicerunCount = mysql_num_rows($get_nicerunCount);
			$nicerunper = round($nicerunCount/$gameCountb, 1);
			$get_wonpossCount = mysql_query("SELECT type FROM tags WHERE type = 'Won Possession' AND team = '{$teamName}' ");
			$wonpossCount = mysql_num_rows($get_wonpossCount);
			$wonpossper = round($wonpossCount/$gameCountb, 1);
			$get_kickCount = mysql_query("SELECT type FROM tags WHERE type = 'Kick' AND team = '{$teamName}' ");
			$kickCount = mysql_num_rows($get_kickCount);
			$kickper = round($kickCount/$gameCountb, 1);
			*/
			echo "<tr>";
			    echo "<td style=\"text-align:left;padding-left:15px;\">";
				echo "<a href=\"team.php?tid=".$teamID."&&f=t\">".$teamName."</a>";
			    echo "</td>";
			    echo "<td style=\"padding-left:15px;\">";
				echo $teamLocation;
			    echo "</td>";
			    echo "<td style=\"padding-left:15px;\">";
				echo $array_teams['level'];
			    echo "</td>";
			    //echo "<td>";
				//echo $coachName;
			    //echo "</td>";
			    //echo "<td>";
				//echo $array_teams['teamURL'];
			    //echo "</td>";
			    echo "<td style=\"text-align:center;\">";
				echo $totalgames;
			    echo "</td>";
			    echo "<td style=\"text-align:center;\">";
				echo $scoreper;
			    echo "</td>";
			    /*echo "<td>";
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
			    echo "</td>";*/
			echo "</tr>";
		    }
		?>
	    </table>
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