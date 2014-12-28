<?php

//The information of the already registered raspberry pi
$raspberry_file = "raspberry_pis.json";

//Store it in an json array.
$raspberries = json_decode(file_get_contents($raspberry_file), true);
//print_r($raspberries);
//print_r(json_encode($raspberries, JSON_PRETTY_PRINT));

//The size of the $raspberries array, which means the number of raspberry pi inserted.
$count=sizeof($raspberries);

//For storing the results of each raspberry pi
$results=array();

//the item class contains the information for dealing with the item
//The main goal of this class is to store the content of the file
class item
{
      public $name;
      public $count;

      public function __construct($name, $count)
      {
          $this->name = $name;
          $this->count = $count;
      }
}

if($count!=0){

	for ($i = 1; $i <=$count; $i++) {
    		$file_name="votes_Rpi".$i.".txt";
    		$data_file="./votes/$file_name";
    		if(file_exists($data_file)==1&&filesize($data_file)!=0){
			$tmp = compute_votes($data_file);
                	array_push($results,$tmp);
		}
   	 
	}
}

//print_r($results);
//print_r(json_encode($results, JSON_PRETTY_PRINT));

function compute_votes($data_file){

	//empty array for storing the items
	$items=array();

	//Reading the file line by line
	
	$lines = file($data_file);

	foreach($lines as $line_num => $line)
	{

		$pos = strpos($line,"=");
		$name = substr($line,0,$pos);
	
		//Store the count of the vote
		$count = filter_var($line, FILTER_SANITIZE_NUMBER_INT);

		//make a new struct
		$tmp = new item($name,$count);

		//push this in the array of structs
		array_push($items,$tmp);
	}

	//Put the items and the votes in an array
	$data=array();

	foreach ($items as $value)
	{  
    		$tmp_name=$value->name;
    		$tmp_count=$value->count;
    		$tmp_element=array("$tmp_name"=>"$tmp_count");
    		$data=$data+$tmp_element;
	}
	
	//sort it as descending order
	arsort($data);
	
	//This variable is to store the order of the items of the question
	$order=0;

	//Put the items and the votes in a order with index 1,2,3,4,....
	//In this case as administrator, you should know how many items you have put for the question.

	//store this result
	$result=array();

	foreach ($data as $key=>$value)
	{	
		$order++;
		//echo  $key." ".$value;
		//The count of votes for each item
		$tmp_result=$key."=".$value; 	
		
		//In this way, we can pop it up on the map.
		//The index should be defined in this way.
		$tmp_order="n"."$order";

		//Assign the order to this item	
		$tmp=array("$tmp_order"=>"$tmp_result");
		
		$result=$result+$tmp;
	} 
//	print_r($data);
//	print_r($result);

	return $result;

} 

?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>Raspberry Map</title>
<meta http-equiv="refresh" content="10">
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js'></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css' rel='stylesheet' />
<style>
  body { margin:0; padding:0; }
    #map { position:absolute; top:100; bottom:100; width:100%; height:100%;}
</style>
</head>
<body>

<!-- <div id="debugdiv">DEBUG: </div> -->

<br/>

<div id='map'></div>

<script>
L.mapbox.accessToken = '';


var mapboxTiles = L.tileLayer('https://{s}.tiles.mapbox.com/v3/panayotis.kbh9i6fk/{z}/{x}/{y}.png', {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
            });

var rpis = <?php echo json_encode($raspberries, JSON_PRETTY_PRINT) ?>;
var votes = <?php echo json_encode($results,JSON_PRETTY_PRINT) ?>;


//PAY ATTENTION: there is no multi-dimentional array length in javascript.
//alert(votes.length);

var map = L.map('map')
    .addLayer(mapboxTiles)
        .setView([47.367, 8.55], 14);

map.on('click', function(e){

	//Store the variables.
	var latitude = e.latlng.lat;
        var longtitude= e.latlng.lng;

	//When the file is empty,so we add the first raspberry pi, the id is 1.
	//If the file is not empty, the id is the length of the string +1. 
	if(rpis==null){
		var new_id=1;
	}else{
		var new_id = (rpis.length)+1;
	}
	//alert(latitude+" "+longtitude+" "+new_id);
	//alert(rpis);

        //Display the confirm button.	
	if(confirm("Would you like to add another Raspberry Pi?")==true){		
		window.location.href = "./insert_rpi.php?new_id="+new_id+"&latitude="+latitude+"&longtitude="+longtitude;

	}
});

//var date_now = Date.now();

//$("#debugdiv").append("--"+date_now+"--");

for (var key in rpis) {
    if (rpis.hasOwnProperty(key)) {
//        alert(key);
//        $('#debugdiv').append(rpis[key].ssid+"-"+rpis[key].latitude+"-"+rpis[key].longitude);

        var marker = L.marker([parseFloat(rpis[key].latitude), parseFloat(rpis[key].longitude)], {
            color: 'red'}).addTo(map);

	//As the administrator, you should know how many items have you choosen for the quesion 
	//In this case, we have 7 kinds of music for answering
	
	if(votes[key]==null){
		  marker.bindPopup("<b>Hello "+rpis[key].ssid+"</b><br/><img width=100 src=./images/"+rpis[key].id+".jpg><br/><b>Description:</b><br/>"+rpis[key].description);
	}else{		
        	  marker.bindPopup("<b>Hello "+rpis[key].ssid+"</b><br/><img width=100 src=./images/"+rpis[key].id+".jpg><br/><b>Description:</b><br/>"+rpis[key].description+"<br> Voting Result of Music Type"
					+"<br>"+votes[key].n1
					+"<br>"+votes[key].n2
					+"<br>"+votes[key].n3
					+"<br>"+votes[key].n4
					+"<br>"+votes[key].n5
					+"<br>"+votes[key].n6
					+"<br>"+votes[key].n7
					);

					//If you have more items 
					//Add in this way 
					//+"<br>"+votes[key].n? ?goes the number you want

        }	
   }
}


</script>

<br/>
<br/>

</body>
</html>


