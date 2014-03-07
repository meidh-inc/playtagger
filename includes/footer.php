<!--begin footer-->
<div id="footer" style="font-size:10px;padding-left:0px;margin-top:10px;">
	<center>
		Copyright 2012-2013, Meidh | 
		Need Help?: <a href="mailto:help@playtagger.com" style="outline:none;background:none;border:none;color:black;font-size:10px;">help@playtagger.com</a> | 
		Phone: (515)996-0046
		
		<?php
			
			//check to see if they are an admin
			$allowed_users = array(1, 4, 5, 12, 42);
			// 1 = eric-localhost
			// 4 = eric-live
			// 5 = chris-live-iahsra account
			// 12 = Brandi-live
			// 42 = John O'brien-live
			if(in_array($userid,$allowed_users)){
			    echo " | <a href=\"paid.php\" style=\"outline:none;background:none;border:none;color:black;font-size:10px;\">Payments</a>";
			}
			
		?>
		
		
		
		
		<!--<br>
		<a href="index.php">Home</a>
		<a href="mycampaigns.php">My Campaigns</a> |
		<a href="who.php">Who</a> |
		<a href="what.php">What</a> |
		<a href="when.php">When</a> |
		<a href="results.php">Results</a>
		-->
	</center>
</div>
</body>
</html>
<?php
	// Close connection to database
	//mysql_close($connection);
?>