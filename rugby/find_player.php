<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php 

// Get info from session
    $userid = $_SESSION['userid'];




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
	    <table border='0' style="width:100%;text-align:center;font-size:14px;">
		<tr>
		    <td>
			Player
		    </td>
		    <td>
			HS Grad
		    </td>
		    <td>
			Coll Grad
		    </td>
		    <td>
			Games
		    </td>
		    <td>
			Scores<br>per Game
		    </td>
		    <td>
			Assists<br>per Game
		    </td>
		    <td>
			Tackles<br>per Game
		    </td>
		    <td>
			Nice Runs<br>per Game
		    </td>
		    <td>
			Won Possessions<br>per Game
		    </td>
		    <td>
			Kicks<br>per Game
		    </td>
		</tr>
		<tr><td colspan='10' style="height:1px;border-bottom:solid gray thin;"></td></tr>
		<?php
		    $get_players = mysql_query("SELECT id, firstName, lastName, hsGrad, colGrad FROM users
					       WHERE userLevel = 'player'
					       AND firstName != ''
					       ORDER BY lastName ");
		    while($array_players = mysql_fetch_array($get_players)){
			$player = $array_players['firstName']." ".$array_players['lastName'];
			$pid = $array_players['id'];
			
			$get_gameCount = mysql_query("SELECT tagID, gameURL FROM tags
						    WHERE player = '{$player}' GROUP BY gameURL ");
			$gameCount = mysql_num_rows($get_gameCount);
			if($gameCount == 0){
			    $gameCountb = 1;
			}else{
			    $gameCountb = $gameCount;
			}
			$get_scoreCount = mysql_query("SELECT type FROM tags WHERE type = 'Score' AND player = '{$player}' ");
			$scoreCount = mysql_num_rows($get_scoreCount);
			$scoreper = round($scoreCount/$gameCountb, 1);
			$get_assistCount = mysql_query("SELECT type FROM tags WHERE type = 'Assist' AND player = '{$player}' ");
			$assistCount = mysql_num_rows($get_assistCount);
			$assistper = round($assistCount/$gameCountb, 1);
			$get_tackleCount = mysql_query("SELECT type FROM tags WHERE type = 'Tackle' AND player = '{$player}' ");
			$tackleCount = mysql_num_rows($get_tackleCount);
			$tackleper = round($tackleCount/$gameCountb, 1);
			$get_nicerunCount = mysql_query("SELECT type FROM tags WHERE type = 'Nice Run' AND player = '{$player}' ");
			$nicerunCount = mysql_num_rows($get_nicerunCount);
			$nicerunper = round($nicerunCount/$gameCountb, 1);
			$get_wonpossCount = mysql_query("SELECT type FROM tags WHERE type = 'Won Possession' AND player = '{$player}' ");
			$wonpossCount = mysql_num_rows($get_wonpossCount);
			$wonpossper = round($wonpossCount/$gameCountb, 1);
			$get_kickCount = mysql_query("SELECT type FROM tags WHERE type = 'Kick' AND player = '{$player}' ");
			$kickCount = mysql_num_rows($get_kickCount);
			$kickper = round($kickCount/$gameCountb, 1);
			
			echo "<tr>";
			    echo "<td style=\"text-align:left;\">";
				echo "<a href=\"player.php?pid=$pid\">".$player."</a>";
			    echo "</td>";
			    echo "<td>";
				echo $array_players['hsGrad'];
			    echo "</td>";
			    echo "<td>";
				echo $array_players['colGrad'];
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