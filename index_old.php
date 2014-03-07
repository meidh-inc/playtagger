<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php //Process Log In Action and set up an email queue

// Set Timezone
    date_default_timezone_set('America/Chicago');



?>
<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		
<!--
<link type="text/css" href="jquery/css/custom-theme/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $( "input:submit, a, button", ".demo" ).button();
    $( "a", ".demo" ).click(function() { return false; });
});
</script>
-->


<center>
<table border='0' style="width:795px;background-color:#E6E6E6;margin:0px;margin-top:0px;padding:0px;">
    <tr>
        <td colspan='2' style="text-align:center;padding-top: 20px;padding-bottom: 20px;">
            <img src="images/playtagger_header.png" alt="Playtagger General Logo" >
        </td>
    </tr>
    <tr>
	<td style="text-align:center;width:400px;">
	    <a href="http://www.worldrugbyshop.com/playtagger.html">
            <img src="images/playtagger_logo_rugby.png" alt="Playtagger Logo" style="width: 350px;">
            </a>
            
        </td>
        <td style="vertical-align:center;padding-left:10px;padding-right:35px;font-family:sans-serif;font-size:15px;">
            <span style="color:#9BC21B;font-weight:bold;">PlayTagger</span> is your hassle-free, online highlight reel.
            <br><br>
            Tag game highlights of you and your friends - and college, National team, and professional
            scouts can instantly see your online highlight reel!
            <br><br>
            No matter where you want to go, <span style="color:#9BC21B;font-weight:bold;">PlayTagger</span> is the fastest, easiest way to be seen!
            <br><br>
            Sports are always being added.  Rugby is available now while Volleyball and Hockey are on the way.
        </td>
    </tr>
    <tr>
	<td style="text-align:center;width:400px;">
            <img src="images/volleyball_image.png" alt="Playtagger Volleyball Image">
        </td>
        <td style="text-align:center;padding: 40px;">
            <img src="images/hockey_image.png" alt="Playtagger Hockey Image">
        </td>
    </tr>
    <tr>
        <td colspan='2' style="text-align:center;padding-top: 30px;">
            <img src="images/how.png" alt="Playtagger How" >
        </td>
    </tr>
</table>
</center>
</div>
<?php include("includes/footer_index.php");?>