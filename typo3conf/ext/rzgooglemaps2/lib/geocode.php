<?php

$address = $_POST['address'];
$zip = $_POST['zip'];
$city = $_POST['city'];
$country = $_POST['country'];

// Build string
$address_final = urlencode($address.', '.$zip.' '.$city.', '.$country);

$url = "http://maps.google.com/maps/api/geocode/xml?address=".$address_final."&sensor=false";        
$output = file_get_contents($url);

// Load xml
$xml = simplexml_load_string($output);

// lat + lng
$lat = (string)$xml->result->geometry->location->lat;
$lng = (string)$xml->result->geometry->location->lng;

$arr = array(
    "lat" => $lat,
    "lng" => $lng
);

echo json_encode($arr);

?>
