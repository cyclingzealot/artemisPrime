<?php
    require_once(__DIR__.'/config.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        padding: 0;
        margin: 0;
        }
      #map {
       height: 900px;
       width: 100%;
       overflow: hidden;
       float: left;
       border: thin solid #333;
       }
    </style>
  </head>
  <body>

    <div id="map"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"></script>
    <script src='http://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.4.4/proj4.js'></script>

    <script>

      var map = L.map('map')
        .setView([56.128016, -106.3468], 4);

      L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
          attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
          maxZoom: 18,
          id: 'mapbox.streets',
          accessToken: 'pk.eyJ1IjoiYXRhbXNpbmdoIiwiYSI6ImNqZnpuMDd5dTBnZm4ycW54N2JsODN1ajUifQ.as43FSImMXRYDfhro0yuoQ'
      }).addTo(map);

      // Get all polygons from db
      // for now parsing geojson file


    //Don't think this is needed
    //function getApiKey() {
    //    var client = new XMLHttpRequest();
    //    client.open('GET', '/apiKey.txt');
    //    client.onreadystatechange = function() {
    //        alert(client.responseText);
    //    }
    //    client.send();
    //}

      function update_status(file_name, pod_id){
        //atam query
        // var query_string = 'https://api.mlab.com/api/1/databases/fairvote/collections/pollingAreas/' + file_name + ':' + pod_id + '?apiKey=<?php echo $apiKey; ?>'
        //my query
        var query_string = 'https://api.mlab.com/api/1/databases/fvc/collections/ridings/?q={"_id": "' + pod_id  + '"}&apiKey=<?php echo $apiKey; ?>'

        // get drop down select
        var new_status = $("#statusoptions").val()
        var new_data = JSON.stringify( { "$set" : { "status" : new_status } } )
        console.log(query_string)
        // console.log(new_status)
        console.log(new_data)

        $.ajax( {
          type: "PUT",
          url: query_string,
          data: new_data,
          contentType: "application/json",
          success: function(response){
            // console.log(response)
            // console.log("it completes")
            // console.log(data)
            setTimeout(
              function()
              {
                 location.reload();
              }, 1000);
            }
          }
        );

      }

      function openprintablepage(file_text){
        window.open(file_text)
      }

      function get_pod_info(file_name, pod_id, layer) {
        // get info
        // atam query
        // var query_string = 'https://api.mlab.com/api/1/databases/fairvote/collections/pollingAreas/?q={"_id": "' + file_name + ':' + pod_id + '"}&apiKey=<?php echo $apiKey; ?>'
        // my query
        var query_string = 'https://api.mlab.com/api/1/databases/fvc/collections/ridings/?q={"_id": "' + pod_id  + '"}&apiKey=<?php echo $apiKey; ?>'


        $.ajax({
          dataType: "json",
          url: query_string,
          success: function( json ) {
            properties = json[0]["data"]["properties"]

            var html_text = '<table>'

            if (!json[0].hasOwnProperty('status')) {
              properties["Status"] = 'unknown'
            }else{
              properties["Status"] = json[0]['status']
            }

            for (var key in properties) {
              html_text += '<tr>'
              if (properties.hasOwnProperty(key)) {
                var val = properties[key]
                html_text += '<td>' + key + '</td>'
                html_text += '<td>' + val + '</td>'
              }
              html_text += '</tr>'
            }
            html_text += '</table><br>'

            html_text += '<select id="statusoptions" form="updatestatus">'
            html_text += '  <option value="unknown">Unknown</option>'
            html_text += '  <option value="skipped">Skipped</option>'
            html_text += '  <option value="to_canvas">To Canvas</option>'
            html_text += '  <option value="assigned">Assigned</option>'
            html_text += '  <option value="canvased">Canvased</option>'
            html_text += '</select>'
            html_text += '<button onclick="return update_status(\''+ file_name + '\',' + pod_id +');">Submit</button>'
            html_text += '<br><br>'

            html_text += '<button onclick="return openprintablepage(\'printable.html?file_name=' + file_name + '&pod_id=' + pod_id + '\');">Click to get printable version.</button>'

            layer.bindPopup(html_text);

            var curr_status = properties['Status']

            switch(curr_status) {
                case 'unknown' :
                  layer.setStyle({
                    color: "#fff",
                    fillColor: "LightGray",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.7
                  })
                  break;
                case 'skipped' :
                  layer.setStyle({
                    color: "#fff",
                    fillColor: "LightYellow",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 1
                  })
                  break;
                case 'to_canvas' :
                  layer.setStyle({
                    color: "#fff",
                    fillColor: "yellow",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.7
                  })
                  break;
                case 'assigned' :
                  layer.setStyle({
                    color: "#fff",
                    fillColor: "LightBlue",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.7
                  })
                  break;
                case 'canvased' :
                  layer.setStyle({
                    color: "#fff",
                    fillColor: "GreenYellow",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.7
                  })
                  break;
              }
          }
        });
      }

      var file_name = "output.geojson"
      var saneCounter = 0
      var objLimit = 200
      const bcDataProjection = '+proj=aea +lat_1=58.5 +lat_2=50 +lat_0=45 +lon_0=-126 +x_0=1000000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs'

      function onEachFeature(feature, layer) {
        if (saneCounter < objLimit) {
            get_pod_info(file_name, feature.properties.EDVA_CODE, layer);
            saneCounter += 1;
        }
      }


      $.getJSON(file_name, function(json) {

        saneCounter02 = 0
        bigLog = 0

        geojson_obj = L.geoJSON(json,
          {
            style: {
              color: "#fff",
              fillColor: "#fff",
              weight: 1,
              opacity: 0,
              fillOpacity: 0
            },
            onEachFeature: onEachFeature,
            filter: function(featureData, layer) {
                //if (bigLog < objLimit) {
                //    console.log(featureData)
                //    console.log(featureData.properties.ED_ABBREV)
                //    bigLog += 1
                //}
                if (featureData.properties.ED_ABBREV == "ABM" && saneCounter02 <= objLimit) {
                    saneCounter02 += 1
                    return true
                } else {
                    return false
                }

            },
            coordsToLatLng: function(coordinates) {
                //console.log("Original coordinates: " + coordinates)
                transformedCoords = proj4(bcDataProjection , "WGS84", coordinates)
                //console.log("Transformed coordinates: " + transformedCoords)
                return [transformedCoords[1], transformedCoords[0]];
            }
          }
        )

        geojson_obj.addTo(map);
        console.log(geojson_obj.getBounds());
        map.fitBounds(geojson_obj.getBounds());
      });

    </script>


    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAM8H88b09Zxky6AjM3gnPPwj5jBvpBylI&callback=initMap"
    async defer></script> -->
  </body>
</html>
