# shptomongo

Dumps SHP files into Mongo DB databases

## Requirements

Running these scripts require:

* Python 3.x
* PyMongo library for Python (`pip3 install pymongo`)
* SshTunnel library for Python (`pip3 install sshtunnel`)
* `ogr2ogr` binary

## Example Usage

First, convert the SHP file to GeoJSON:

```
python3 shptojson.py -i federalPollingBoundariesSample.shp
```

Make sure there are `pollingAreas` and `rawGeoJson` collections on the
MongoDB instance, in a `FairVotes` db. Then run the following command
to upload the parsed contents of the GeoJSON file to MongoDB:

```
Example usage:
  python3 jsontomongo.py \
    -i output.geojson \
    -u ubuntu \
    -s some-server.amazonaws.com \
    -p ~/mongokey.pem
```