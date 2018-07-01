<?php

require_once(__DIR__.'/config.php');


$actual_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$escaped_url = htmlspecialchars( $actual_url, ENT_QUOTES, 'UTF-8' );

$parts = parse_url($escaped_url);
parse_str($parts['query'], $query);
$ridingId = $query['riding'];

					  //Select data,     id  and     riding id         that has ed_id=ridingID
$url_mlab="$baseQuery&f={%22data%22:1,%22_id%22:1,%22ed_id%22:1}&q={%22ed_id%22:%20%22$ridingId%22}";

$ridingGeoJason;

//Get the contents of url_mlab
//Parse the elements so that :
//If we select the data element of the query above, and for each of the values (which are hashes), but them into an array indexed "features", and another element in the same level called "type":"FeatureCollection", that should give us valid geojason.  All the needed attributes ("type",    "properties", "geometry")
//Type is already there: {
//"type": "FeatureCollection",
//"features": [
//{ "type": "Feature", "properties": { "EDVA_CODE": "ABM024",

