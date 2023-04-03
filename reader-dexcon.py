# Convert Dexcon text data file into JSON
# Neuronic (c) 2020. All Rights Reserved.

from datetime import datetime
from dateutil.relativedelta import relativedelta

START = "11/5/2020"

START_DATE = datetime.strptime(START, "%m/%d/%Y")
DAYS = ["Thursday", "Friday", "Saturday", "Sunday"]
LOCATIONS = ["Boardroom 1", "Boardroom 2", "Concord", "Zoom", "Discord"]
LINKS = ["https://twitch.tv/dexboardroom1", "https://twitch.tv/dexboardroom2", "https://twitch.tv/dexconcord", "https://zoom.us/signin", "https://discord.gg/6s8Yjtm"]

def process_event(reader):
    output = '    { "code":"'
    
    code = reader.readline()[:-1]
    output += code + '",\n'
    print code

    name = reader.readline()[:-1].strip('" ')
    output += '      "name":"' + name + '",\n'
    print name

    speakers = reader.readline()[13:-1].strip('. ').replace('"', '\\"')
    output += '      "speakers":"' + speakers + '",\n'
    print speakers

    desc = reader.readline()[:-1].strip(' ').replace('"', '\\"')
    output += '      "desc":"' + desc + '",\n'
    print desc
    
    time = reader.readline()[:-1]
    day, start_end = time.split(', ')
    date = START_DATE + relativedelta(days=DAYS.index(day))
    start_date = date.strftime("%m/%d/%Y")
        
    start, end = start_end.split(' - ')
    start_time = datetime.strptime(start_date + " " + start, "%m/%d/%Y %I:%M%p")
    end_time = datetime.strptime(start_date + " " + end, "%m/%d/%Y %I:%M%p")
    output += '      "start":"' + start_time.strftime("%m/%d/%Y %H:%M") + '",\n'
    output += '      "end":"' + end_time.strftime("%m/%d/%Y %H:%M") + '",\n'
    print start_time
    print end_time
    
    rating = reader.readline()[:-1]
    output += '      "rating":"' + rating + '",\n'
    print rating

    location = reader.readline()[11:-1].strip('. ')
    output += '      "location":"' + location + '",\n'
    output += '      "link":"' + LINKS[LOCATIONS.index(location)] + '"\n'
    print location
    
    output += '    }'
    
    return output

reader = open('data-metatopia-2020.txt')
writer = open('data-metatopia-2020.json', 'w')

try:
    writer.write('{ "events": [\n')
    
    has_more = True
    while has_more:
        output = process_event(reader)
        writer.write(output)
        
        has_more = reader.readline()
        if has_more:
            writer.write(',\n')

    writer.write('\n')
    writer.write('  ]\n')
    writer.write('}\n')

finally:
    reader.close()
    writer.close()
