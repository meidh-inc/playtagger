<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include_once("includes/form_functions.php"); ?>
<?php 

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






?>
<?php include("includes/header.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		

<link type="text/css" href="jquery_ss/css/custom-theme/jquery-ui-1.10.0.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery_ss/js/jquery-1.9.0.js"></script>
<script type="text/javascript" src="jquery_ss/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript">



</script>

<!--  Message Center Code
<td style="text-align:center;padding:0px;margin:0px;height:10px;font-weight:bold;">
    <?php// if (!empty($message)) {echo "<p style=\"color:#C20000; font-size: 12px;\">" . $message . "</p>";} ?>
    <?php// if (!empty($errors)) { display_errors($errors); } ?>
</td>
-->


<center>
    <form action="index.php" method="post">
<table border='0' >
    <tr>
        <td style="text-align:justify;padding:20px;">
	    
        </td>
    </tr>
    <tr>
        <td style="text-align:center;padding:20px;">
	    
        </td>
    </tr>
    <tr>
        <td style="text-align:center;padding:20px;">
	    
        </td>
    </tr>
</table>
    </form>
</center>
</div>
<?php include("includes/footer_index.php");?>