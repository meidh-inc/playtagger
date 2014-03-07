<?php include("includes/header_index.php"); ?>
		
             <!-- ------------------- page layout begins here ------------------- --> 
		
<style>
img.bg {
  /* Set rules to fill background */
  min-height: 100%;
  min-width: 1024px;
	
  /* Set up proportionate scaling */
  width: 100%;
  height: auto;
	
  /* Set up positioning */
  position: fixed;
  top: -250px;
  left: 0;
}

@media screen and (max-width: 1024px) { /* Specific to this particular image */
  img.bg {
    left: 50%;
    margin-left: -512px;   /* 50% */
  }
}
#page-wrap {
    background: none repeat scroll 0 0 white;
    box-shadow: 0 0 20px black;
    position: relative;
    width: 800px;
}
</style>

<img src="includes/basketball.jpg" class="bg" alt="">
<center>
<div id="page-wrap">
    <iframe src="http://www.playtagger.com/v2/index.php?s=4" frameborder='0' style="width:795px; height:1600px; padding:0px;" ></iframe>
    
    <!--<iframe src="http://localhost:8080/playtagger/v2/index.php?s=3" frameborder='0' style="width:795px; height:1600px; padding:0px;" ></iframe>
    -->
</div>
</center>

<?php include("includes/footer_index.php");?>