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
      .marginsH {
        height: 60px;
      }
      #map {
       height: 100%;
       width: 100%;
       overflow: hidden;
       float: left;
       border: thin solid #333;
       }
    </style>
  </head>
  <body>

    <div id="controls" class="marginsH">Ridings: <select id="ridingsSelect"></select></div>
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

    //Populate the pull down menu               // Include ed_id field, exclude _id
    //Ccould be done server side in php
    var query = '<?php echo $baseQuery; ?>' + '&f={"ed_id":1,"_id":0}'

    //Parse JSON for pull down menu population
    $.ajax( { url: query,
          data: JSON.stringify( {"distinct": "ed_id"} ),
          type: "GET",
          contentType: "application/json",
          success: function(response) {

                ridings = []
                lookup = {}
                for (var item, i=0; item=response[i++];) {
                    var edcode = item["ed_id"]

                    if (!(edcode in lookup)) {
                        lookup[edcode] = 1;
                        ridings.push(edcode)

                    }

                }

                $.each(ridings, function(key, edcode) {
                    console.log(edcode)
                    $('#ridingsSelect')
                        .append($("<option></option>").attr("value",edcode).text(edcode));
                })

            },
    } )

      // Get all polygons from db
      // for now parsing geojson file


      function update_status(file_name, pod_id){
        //atam query
        // var query_string = 'https://api.mlab.com/api/1/databases/fairvote/collections/pollingAreas/' + file_name + ':' + pod_id + '?apiKey=<?php echo $apiKey; ?>'
        //my query
        var query_string = '<?php echo $baseQuery; ?>' + '&q={"_id":"' + pod_id  + '"}'

        console.log('query_string is ' + query_string)

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

            draw()

            //setTimeout(
            //  function()
            //  {
            //     location.reload(); //TODO Change this so URL accepts a location reload? Or call redraw?
            //  }, 1000);

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
        var query_string = '<?php echo $baseQuery; ?>' + '&q={"_id": "' + pod_id  + '"}'


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
            html_text += '<button onclick="return update_status(\''+ file_name + '\',\'' + pod_id +'\');">Submit</button>'
            html_text += '<br><br>'

            html_text += '<button onclick="return openprintablepage(\'printable.php?unique_polling_id=' + pod_id + '\');">Click to get printable version.</button>'

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

      //TODO: Set this to the baseQuery with a riding id
                                        //This worked: https://api.mlab.com/api/1/databases/fvc/collections/ridings/?apiKey=______apiKey_________&f={%22data%22:1,%22_id%22:1,%22ed_id%22:1}&q={%22ed_id%22:%20%22__________ridingId______________________%22}
                                        //TODO: If we select the data element of the query above, and for each of the values (which are hashes), but them into an array indexed "features", and another element in the same level called "type":"FeatureCollection", that should give us valid geojason.  All the needed attributes ("type", "properties", "geometry")
      var saneCounter = 0
      var objLimit = 600

      function onEachFeature(feature, layer) {
        if (saneCounter < objLimit) {
            get_pod_info(file_name, feature.properties.EDVA_CODE, layer);
            saneCounter += 1;
        }
      }


      function draw() {
        map.removeLayer;

       var ridingsSelectObj = document.getElementById("ridingsSelect");
       var ridingSelectedId = ridingsSelectObj.options[ridingsSelectObj.selectedIndex].value;

       var queryRidingInfo = 'https://api.mlab.com/api/1/databases/fvc/collections/ridings/?apiKey=<?php echo $apiKey; ?>&q={"ed_id":"' + ridingSelectedId + '"}&f={"data":1,"_id":1,"ed_id":1}'


       console.log('queryRidingInfo: ' + queryRidingInfo);

       // queryRidingInfo has a query returning data now, but either :
       // - the code must proces that data structure OR
       // - the data strcuture must match output.geojson

       // If we use the data strcuture approach, the beginning structure of data returned by queryRidingInfo needs changing:
       // - Data from output.geoson: { "type": "FeatureCollection", "features":
       // - Data of queryRidingInfo: [ { "_id" : "ABS057" , "ed_id" : "ABS" , "data" :

       $.getJSON(queryRidingInfo, function(json) {

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

            // TODO: Replace filter below with a query for file_name
            // filter: function(featureData, layer) {
//
  //               var ridingsSelectObj = document.getElementById("ridingsSelect");
    //             var ridingSelectedId = ridingsSelectObj.options[ridingsSelectObj.selectedIndex].value;
//
  //               console.log('Looking for ' + ridingSelectedId)
//
                    // KEEP: This filter is not in use, but this saneCounter02 may help us if we are hitting memory errors
  //               if (featureData.properties.ED_ABBREV == ridingSelectedId && saneCounter02 <= objLimit) {
    //                 saneCounter02 += 1
      //               return true
        //         } else {
          //           return false
            //     }

//             },
            <?php echo $proj4transform; ?>
          }
        )

        geojson_obj.addTo(map);
        console.log(geojson_obj.getBounds());
        map.fitBounds(geojson_obj.getBounds());
      });
      }

      // Might be simpler to just do the select server side
      $( document ).ready(function() {
          console.log('Document ready. Executing...')
            $('#ridingsSelect').change(function() {console.log('Select.change fired');  draw()})
      })


    </script>


    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAM8H88b09Zxky6AjM3gnPPwj5jBvpBylI&callback=initMap"
    async defer></script> -->
  </body>
</html>
