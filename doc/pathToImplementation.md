# Organizer
1. Create query to pull specific riding data from remote rather than local
** Node: query specific riding like this: https://api.mlab.com/api/1/databases/fvc/collections/ridings/?apiKey={apiKey}&q={%22ed_id%22:%22ABM%22}
1. Filter by riding pull down
** Riding list: https://api.mlab.com/api/1/databases/fvc/collections/ridings/?apiKey={apiKey}&f={%22ed_id%22:1,%22_id%22:0} (then apply a select distinct)
1. Alllow assign a riding to work

# End user
1. Edit a polling area by list
1. List selected polling area by distnace

# Future elections
1. For geodata sets, are we better off in storing data locally (would allow usage of postgres) or remotely with mlab?
