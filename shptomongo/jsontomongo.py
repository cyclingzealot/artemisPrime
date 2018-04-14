import pymongo
import json
import argparse
from sshtunnel import SSHTunnelForwarder

def main():
  """
  Uploads the contents of the GeoJSON to a MongoDB instance.
  Connects to the MongDB instance via an SSH tunnel.
  """

  arguments = load_command_line_arguments()

  with SSHTunnelForwarder(
      arguments.host,
      ssh_username=arguments.user,
      ssh_pkey=arguments.pem,
      remote_bind_address=('127.0.0.1', 27017)) as tunnel:

    client = pymongo.MongoClient('127.0.0.1', tunnel.local_bind_port)
    upload_json_to_mongo(client, arguments.file)

def load_command_line_arguments():
  """
  Load command-line arguments.
  """
  parser = argparse.ArgumentParser(description='Uploads a GeoJSON file to MongoDB.')
  parser.add_argument('--file', '-i', required=True,
                      help='the GeoJSON file')
  parser.add_argument('--host', '-s', required=True,
                    help='the host/ip for the server instance')
  parser.add_argument('--user', '-u', required=True,
                    help='the ssh user to the server instance')
  parser.add_argument('--pem', '-p', required=True,
                    help='the ssh .pem to the server instance')
  args = parser.parse_args()
  return args

def upload_json_to_mongo(client, input_file):
  """Upload JSON data to MongoDB instance."""
  with open(input_file) as geojson_file:
    raw_json = geojson_file.read()
    parsed_json = json.loads(raw_json)

    create_raw_geo_json_entry(client, input_file, raw_json)
    create_polling_area_entry(client, input_file, parsed_json)

def create_raw_geo_json_entry(client, input_file, raw_json):
  """Upload the raw GeoJSON file."""
  raw_geo_json_entry = {
    "_id": input_file,
    "file": raw_json
  }

  client.FairVote.rawGeoJson.update(
    {'_id':input_file}, {"$set": raw_geo_json_entry}, upsert=True)

def create_polling_area_entry(client, input_file, parsed_json):
  """Upload the contents of the GeoJSON file as documents."""
  all_polling_areas = parsed_json["features"]
  for polling_area in all_polling_areas:
    properties = polling_area["properties"]
    pd_id = properties['PD_ID']
    key = "%s:%s" % (input_file, pd_id)
    polling_area["_id"] = key

    client.FairVote.pollingAreas.update(
      {'_id':key}, {"$set": polling_area}, upsert=True)

if __name__ == "__main__":
  main()