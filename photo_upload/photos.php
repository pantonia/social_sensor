<!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
  <?php 
    if (isset($_GET['album'])) {
	  echo $_GET['album'];
	} else {
	  echo 'Photo Gallery';
	}
  ?>
</title>

<!-- start gallery header --> 
<link rel="stylesheet" type="text/css" href="folio-gallery.css" />
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>

<link rel="stylesheet" type="text/css" href="colorbox/colorbox.css" />
<!--<link rel="stylesheet" type="text/css" href="fancybox/fancybox.css" />-->
<script type="text/javascript" src="colorbox/jquery.colorbox-min.js"></script>
<!--<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.1.min.js"></script>-->
<script type="text/javascript">
$(document).ready(function() {	
	
	// colorbox settings
	$(".albumpix").colorbox({rel:'albumpix'});
	
	// fancy box settings
	/*
	$("a.albumpix").fancybox({
		'autoScale	'		: true, 
		'hideOnOverlayClick': true,
		'hideOnContentClick': true
	});
	*/
});
</script>
<!-- end gallery header -->
</head>
<body>

<div align = "center" style = "font-size:15px; font-weight: bold">
	<br>
       <?php echo "Welcome to Local Photo Album";?>
</div>

<div align="center" style="font-size:13px;font-weight:bold;">
	<br>
	<a href="../../index.html">&laquo; Back to HomePages.com</a>
</div>

<p>&nbsp;</p>

<div class="gallery">  
  <?php include "folio-gallery.php"; ?>
</div>   

<form align = "center" action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">     
</form>

<div align="center">
<br>
<button onclick="myFunction()">View image uploaded</button>
<script>
    function myFunction() {
        location.reload();
    }
</script>
</div>

<div align = "center" style = "font-size:13px; font-weight: bold">
	<br>
	<?php echo "If you would like to leave a message, please write it on this board";?>
</div>


<?php

$input_file1="./forum/text1.txt";
$input_file2="./forum/text2.txt";
$input_file3="./forum/text3.txt";

if (!empty($_POST["text1"])) {
   $text1 = $_POST["text1"]; 
   file_put_contents($input_file1,$text1);
}

if (!empty($_POST["text2"])) {
   $text2 = $_POST["text2"];
   file_put_contents($input_file2,$text2);                      
}

if (!empty($_POST["text3"])) {
   $text3 = $_POST["text3"];                      
   file_put_contents($input_file3,$text3);
}



$text1_text = file_get_contents($input_file1, true);
$text2_text = file_get_contents($input_file2, true);
$text3_text = file_get_contents($input_file3, true);

?>



<form align="center" method="POST" action="?">
<textarea id="text1" name="text1" class="form-control" rows="10"><?php echo $text1_text?></textarea>
<textarea id="text2" name="text2" class="form-control" rows="10"><?php echo $text2_text?></textarea>
<textarea id="text3" name="text3" class="form-control" rows="10"><?php echo $text3_text?></textarea>
    <br/>
    <button type="submit" class="btn btn-primary">Save</button>
</form>



</body>
</html>
