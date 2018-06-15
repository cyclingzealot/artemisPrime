<?php
$apiKey='yourMlabKeyHere';
$baseQuery="https://api.mlab.com/api/1/databases/__dbname___/collections/___collectionNmae____/?apiKey=$apiKey";

// This would be a projecttion transformation if say, your map is in UTM rather than the standard WGS84.
$proj4transform = <<<JS

            coordsToLatLng: function(coordinates) {
                //console.log("Original coordinates: " + coordinates)
                transformedCoords = proj4("+proj=aea +lat_1=58.5 +lat_2=50 +lat_0=45 +lon_0=-126 +x_0=1000000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs", "WGS84", coordinates)
                //console.log("Transformed coordinates: " + transformedCoords)
                return [transformedCoords[1], transformedCoords[0]];
            },
JS;

