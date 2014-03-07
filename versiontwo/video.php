<?php// require_once("includes/session.php"); ?>
<?php// require_once("includes/connection.php"); ?>
<?php// require_once("includes/functions.php"); ?>
<?php// include_once("includes/form_functions.php"); ?>
<?php 
/*
if (isset($_POST['enter'])) { // The user clicked 'login'
    //create array to place any errors
	$errors = array();  
    
    // perform validations on the form data
        $required_fields = array('pin');
	$errors = array_merge($errors, check_required_fields($required_fields, $_POST));
        $fields_with_lengths = array('pin' => 10);
        $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
	
    // get the content from the variables
        $pin = trim(mysql_prep($_POST['pin']));
	
    // no errors detected so far, continue
        if ( empty($errors) ) {
	    
	    //do stuff
	    
	    //redirect_to("template.php");
	
    // errors detected, relay them to the user
	} else {
	    if (count($errors) == 1) {
		$message = "There was 1 error in the form.";
	    } else {
		$message = "There were " . count($errors) . " errors in the form.";
	    }
	}
}




*/


$vidID = "kdgz635g33g";

?>
<?php include("includes/header.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery_ss/css/custom-theme/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery_ss/js/jquery-1.8.3.js"></script>
<script type="text/javascript" src="jquery_ss/js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="myjs/vidnav.js"></script>
<script type="text/javascript" src="myjs/vidplayer.js"></script>


<script type="text/javascript">
var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
	    height: '405',
	    width: '720',
	    videoId: '<?php echo $vidID; ?>',
	    events: {
		'onReady': onPlayerReady,
		'onStateChange': onPlayerStateChange
	    }
        });
    }
    $(function() {
    $( "#tagpop" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "open" );
	});
	});
</script>
<style>
    .ui-menu { width: 135px; height: 50; border: none; },
    label { display: inline-block; width: 5em; }
</style>



















<form action="video.php" method="post">
<center>

<div style="width:725px;">
    <div style="height:20px;"></div>
    <div style="height:60px;">
	<div class="ui-corner-all" style="background-color:#C5C4BD;">
	<table style="margin-left: 0px;">
	    <tr>
		<td>
		    <ul id="addMenu" style="background-color:#C5C4BD;">
			<li>
			    <a href="#">Add</a>
			    <ul>
				<li><a href="#">Game</a></li>
				<li><a href="#">Team</a></li>
				<li><a href="#">Coach</a></li>
				<li><a href="#">Player</a></li>
			    </ul>
			</li>
		    </ul>
		</td>
		<td>
		    <ul id="searchMenu">
			<li><a href="#">Search</a></li>
		    </ul>
		</td>
		<td>
		    <ul id="profileMenu">
			<li>
			    <a href="#">Profiles</a>
			    <ul>
				<li><a href="#">My Profile</a></li>
				<li  class="ui-state-disabled"><a href="#" title="Subscription Required">Players</a></li>
				<li  class="ui-state-disabled"><a href="#">Teams</a></li>
			    </ul>
			</li>
		    </ul>
		</td>
		<td>
		    <ul id="logoutMenu">
			<li><a href="#">Logout</a></li>
		    </ul>
		</td>
		<td>
		    <ul id="helpMenu">
			<li><a href="#">Help</a></li>
		    </ul>
		</td>
	    </tr>
	</table>
	</div>
    </div>
    <div class="ui-corner-all" style="height:410px;background-color: black;">
	<div id="player"></div>
    </div>
    <div style="height:50px;padding-top: 10px;">
	<button id="tagpop" onclick="pauseVideo();" alt="tag" name="tag" style="width:499px;border-color:#555555;">Tag Video</button>
    </div>
    <div style="height:170px;">
	<div class="ui-corner-all" style="float: left;width: 350px;height:170px;background-color:#C5C4BD;margin-left: 5px;">
	    
	</div>
	<div class="ui-corner-all" style="float: right;width: 350px;height:170px;background-color:#C5C4BD;margin-right: 5px;">
	    
	</div>
    </div>
    <div style="height:50px;"></div>
</div>

</center>
</form>

<?php include("includes/footer.php");?>