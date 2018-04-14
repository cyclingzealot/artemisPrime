import shapefile
import json
import datetime
import argparse

def main():
    """
    Convert the SHP file to GeoJSON.
    """
    arguments = load_command_line_arguments()

    features = load_shp_file(arguments.input)

    feature_to_geojson(features, arguments.output)

def load_command_line_arguments():
    """
    Load command-line arguments.
    """
    parser = argparse.ArgumentParser(description='Converts SHP files to GeoJSON.')
    parser.add_argument('--input', '-i', required=True,
                        help='the SHP file to convert')
    parser.add_argument('--output', '-o', default='output.json',
                        help='the GeoJSON file to output to')
    args = parser.parse_args()
    return args

def load_shp_file(input_file):
    # read the shapefile
    reader = shapefile.Reader(input_file)
    fields = reader.fields[1:]
    field_names = [field[0] for field in fields]
    features = []
    for sr in reader.shapeRecords():
        atr = dict(zip(field_names, sr.record))
        geom = sr.shape.__geo_interface__
        features.append(dict(type="Feature", geometry=geom, properties=atr))
    return features

def feature_to_geojson(features, output_path):
    """Converts feature to GeoJSON."""

    # Convert to JSON.
    json_contents = json.dumps(
        {"type": "FeatureCollection", "features": features}, 
        default=json_serial)

    # Write the GeoJSON file.
    with open(output_path, "w") as geojson:
        geojson.write(json_contents)

def json_serial(obj):
    """JSON serializer for objects not serializable by default json code"""
    if isinstance(obj, (datetime.date, datetime.datetime)):
        return obj.isoformat()
    raise TypeError ("Type %s not serializable" % type(obj))

if __name__ == "__main__":
    main()