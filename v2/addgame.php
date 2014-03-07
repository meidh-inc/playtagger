<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php //confirm_logged_in(); ?>
<?php 

// Get info from session
//    $userid = $_SESSION['userid'];





/* GAME VIDEO UPLOADER CODE
  
$url = "http://www.youtube.com/watch?feature=player_detailpage&v=_3fGXL8vrS4";

function youtube_id_from_url($url) {
   $pattern =
    '%^# Match any youtube URL
    (?:https?://)?  # Optional scheme. Either http or https
    (?:www\.)?      # Optional www subdomain
    (?:             # Group host alternatives
      youtu\.be/    # Either youtu.be,
    | youtube\.com  # or youtube.com
      (?:           # Group path alternatives
        /embed/     # Either /embed/
      | /v/         # or /v/
      | .*v=        # or /watch\?v=
      )             # End path alternatives.
    )               # End host alternatives.
    ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
    ($|&).*         # if additional parameters are also in query string after video id.
    $%x'
    ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
      return $matches[1];
    }
    return false;
 }

echo youtube_id_from_url($url);
*/






?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery/css/smoothness/jquery-ui-1.9.0.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript">



</script>
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript">
    google.load("swfobject", "2.1");
</script>    
<script src="js-functions.js"></script>





<center>
<table border='1' style="width:800px;background-color:#E6E6E6;padding:10px;margin-top:5px;">
    <tr>
	<td colspan='2' style="height:50px;text-align:center;">
	    Line A
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="height:150px;text-align:left;">
	    
	    
	    
	    
	    
	    
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="height:50px;text-align:center;">
	    Line 2
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="height:100px;text-align:center;">
	    Line 3
	    
	</td>
    </tr>
    <tr><td colspan='2' style="height:1px;border-bottom:solid gray thin;"></td></tr>
    <tr>
	<td colspan='2' style="text-align:left;">
	    Line 4
	    
	    
	    
	</td>
    </tr>
</table>
</center>
<?php include("includes/footer.php");?>