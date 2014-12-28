<?php

$raspberry_file = "raspberry_pis.txt";

$raspberries = json_decode(file_get_contents($raspberry_file), true);

?>


<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>MAP with GJSON</title>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css' rel='stylesheet' />
<script src="/js/jquery.min.js"></script>
<style>
body { margin:0; padding:0; }
#map { position:absolute; top:100; bottom:20; width:50%; height:50%;}
</style>
<style>
.leaflet-popup-content img {
max-width:50%;
}
</style>
</head>

<body>

<script>
$(document).ready(function(){
setInterval(function() {
$("#debugdiv").load("./refresh.php");
}, 3000);
})
</script>

<div id='debugdiv'></div>
<div id='map'></div>

<script>

L.mapbox.accessToken = 'pk.eyJ1IjoicGFuYXlvdGlzIiwiYSI6IjhVWVREOTAifQ.NWt_NiMoXds89Q7wF81msw';

/*

var mapboxTiles = L.tileLayer('https://{s}.tiles.mapbox.com/v3/panayotis.kbh9i6fk/{z}/{x}/{y}.png', {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
});

var map = L.map('map')
.addLayer(mapboxTiles)
.setView([47.367, 8.55], 14);
*/


L.mapbox.accessToken = 'pk.eyJ1IjoicGFuYXlvdGlzIiwiYSI6IjhVWVREOTAifQ.NWt_NiMoXds89Q7wF81msw';
var map = L.mapbox.map('map', 'panayotis.kbh9i6fk')
    .setView([47.367, 8.55], 12);

var rpis = <?php echo json_encode($raspberries, JSON_PRETTY_PRINT) ?>

var geoJson = new Array();

for (var key in rpis) {
 if (rpis.hasOwnProperty(key)) {

 var properties = new Array();
 var geometry = new Array();
 var marker_color = "#bbb";

 if (rpis[key].activity == "1")
     marker_color = "#258";


 geoJson.push({"type":'Feature', "geometry": {"type": 'Point', "coordinates":[parseFloat(rpis[key].longitude), parseFloat(rpis[key].latitude)]}, "properties": {"title": rpis[key].ssid, "image": './images/'+rpis[key].ssid+'.jpg', "url": '', "city": 'zurich', 'marker-color': marker_color}});

}
}

var myLayer = L.mapbox.featureLayer().addTo(map);

function resetColors() {
    for (var i = 0; i < geoJson.length; i++) {
        geoJson[i].properties['marker-color'] = geoJson[i].properties['old-color'] ||
            geoJson[i].properties['marker-color'];
    }
    myLayer.setGeoJSON(geoJson);
}


/*
myLayer.on('click', function(e) {
    map.panTo(e.layer.getLatLng());
    
    //resetColors();
    //e.layer.feature.properties['old-color'] = e.layer.feature.properties['marker-color'];
    e.layer.feature.properties['marker-color'] = '#ff8888';
    myLayer.setGeoJSON(geoJson);
});
 */

myLayer.on('layeradd', function(e) {
    var marker = e.layer,
        feature = marker.feature;

    var popupContent =  feature.properties.title+'<br/><a target="_blank" class="popup" href="' + feature.properties.url + '">' +
    '<img src="' + feature.properties.image + '" /><br/>' +
    feature.properties.city +
    '</a>';

    marker.bindPopup(popupContent,{
    closeButton: false,
    minWidth: 320
    });

});

myLayer.setGeoJSON(geoJson);

$('#debugdiv').append("TEST"+JSON.stringify(geoJson, undefined, 2));

//map.on('click', resetColors);

</script>

</body>
</html>


