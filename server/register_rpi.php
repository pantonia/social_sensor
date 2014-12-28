<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
<style>
body {
    background-color:#d0e4fe;
}
</style>
</head>
<body>

<?php

//Uploading the raspberry pi into the folder.
$target_dir = "images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
//echo "$target_file";
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//This string is for storing the information of the raspberry pis.

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
//    header("Refresh:0; url=../photos.php");
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 2000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
//        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
//	echo "<br>";
//	echo "This raspberry has been uploaded successfully.";
	echo "<br>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
	  
//The file for storing the registered raspberry pi in json format.
$raspberry_file = "raspberry_pis.json";

//Basic information of the raspberry pi
$new_id=$_POST["rpi_id"];
$new_ssid=$_POST["ssid"];
$new_lat=$_POST["latitude"];
$new_long=$_POST["longtitude"];
$new_desc=$_POST["description"];
$new_photo= $_FILES["fileToUpload"]["name"];

//Store the basic informatio of the new inserted raspberry pi in json format.
//The key point here is we insert the raspberry pi with a bit different format according to the file is empty or not.

//Here is the key point
//When the file is empty, we set the array of parameters of new_pi as $new_pi[]
if(0==filesize($raspberry_file)){
	$new_pi[] = array("id"=>$new_id,"ssid"=>$new_ssid,"latitude"=>$new_lat, "longitude"=>$new_long, "description" => $new_desc, "new_photo"=>$new_photo);
	file_put_contents($raspberry_file, json_encode($new_pi));
}else{
//When the file is not empty, we set the array of parameters of new_pi as $new_pi
	$new_pi = array("id"=>$new_id,"ssid"=>$new_ssid,"latitude"=>$new_lat, "longitude"=>$new_long, "description" => $new_desc, "new_photo"=>$new_photo);
	$data=json_decode(file_get_contents($raspberry_file),true);
	array_push($data,$new_pi);

	file_put_contents($raspberry_file, json_encode($data));
}

?>


<div align = "center" style = "font-size:13px font-weight: bold">

<?php      
echo "This Raspberry Pi with parameters below has been registered successfully.";
echo "<br>";
echo "<br>";

echo "ID"."          : ".$new_id;
echo "<br>";
echo "<br>";

echo "SSID"."        : ".$new_ssid;
echo "<br>";
echo "<br>";

echo "Latitude"."    : ".$new_lat;
echo "<br>";
echo "<br>";

echo "Longtitude"."  : ".$new_long;
echo "<br>";
echo "<br>";

echo "Description"." : ".$new_desc;
echo "<br>";
echo "<br>";

echo "The image of this raspberry pi";
echo "<br>";
echo "<br>";

echo '<img src="'.$target_file.'"" width=200" alt="">'."&nbsp;&nbsp;";
echo "<br>";
echo "<br>";

?>

</div>

<?php 

//Because after registeration, we need to upload the raspberry pi image to the ftp server.
//upload the image of this raspberry pi to the ftp server.
//In this case, we need to wait for a few time.

//The image file going to be uploaded.
$data_file = $target_file;

//The ftp server parameters.
$ftp_server = "ftp.internet-jukebox.org";
$ftp_user_name="internetpk";
$ftp_user_pass="6v2MGfNBdb65";

// set up basic connection
$conn_id = ftp_connect($ftp_server)  or die ("Cannot connect to host");

// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);


// check connection
if ((!$conn_id) || (!$login_result)) {
    echo "FTP connection has failed!";
    echo "Attempted to connect to $ftp_server for user $ftp_user_name";
    exit;
} else {
//    echo "Connected to $ftp_server successfully";
}

//Change to the correct directory.
ftp_chdir($conn_id, 'www/server/photos/');

// upload the photo of this raspberry pi, you should use FTP_BINARY
if (ftp_put($conn_id, $new_photo, $data_file, FTP_BINARY)) {
// echo "successfully uploaded $data_file\n";
} else {
// echo "There was a problem while uploading $file\n";
}

// close the connection
ftp_close($conn_id);

?>

<div align = "center">

<A HREF="map_server.php">Go back to the Map</A>

</div>




</body>
</html>
