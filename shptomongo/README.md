# shptomongo

Dumps SHP files into Mongo DB databases

## Requirements

Running these scripts require:

* Python 3.x
* PyMongo library for Python (`pip3 install pymongo`)
* SshTunnel library for Python (`pip3 install sshtunnel`)
* `ogr2ogr` binary


### Linux Mint Rebecca users

...  may want to install pip from the dev package, as the python in Rebeccas pacakges are getting old. See https://github.com/pypa/get-pip#usage

... also see https://stackoverflow.com/a/34372779/1611925 for installing Python3.5

... and see https://askubuntu.com/a/767965/333952 for installing version *3.4* of mongo client to interface with mlabs

... and make sure you have version 3.6.1 of pymongo: sudo pip3.5 install pymongo==3.6.1

### Debian (Linux Mint Rebecca) pacakges recommended / required:
* python-pymongo or python3-pymongo
* sudo apt-get install python-setuptools
* sudo apt-get install python-dev python3.5-dev python3-dev


## Example Usage

First, convert the SHP file to GeoJSON:

```
python3 shptojson.py -i federalPollingBoundariesSample.shp
```

In the next step, we will upload the geojson to the mongodb instance.
You can specify the collection name with the -x argument.  This should also represent the jurisdiction and year.
Make sure that collection exists in the MongoDB instance, in a db specified by the url in the -m paramater. Then run the following command
to upload the parsed contents of the GeoJSON file to MongoDB:

```
  python3 jsontomongo.py \
    -i output.geojson \
    -m mongodb://user:pass@somebox.mlab.com:11248/fvc
```

If you need to use an SSH tunnel to access MongoDB instead, you can use
the following command:

```
  python3 jsontomongo.py \
    -i output.geojson \
    -u ubuntu \
    -s some-server.amazonaws.com \
    -p ~/mongokey.pem
```
