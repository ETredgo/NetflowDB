#!/usr/bin/python
__author__ = "Ed Tredgett, Mike Antcliffe - Logically Secure"

#################################################
                                            	#
tmp = "/var/netflowdb/tmp/"                                    #   IF YOU MOVE THE TMP DIRECTORY FROM THIS FOLDER, YOU WILL NEED TO CHANGE THAT PATH
                                                #
#################################################

# Attempts to import the modules required in order for the script to run
try:
        import sys,os,csv
        from decimal import *
        import socket,struct
        import MySQLdb
        import time
        import urllib2
        from time import strftime,gmtime
except:
        print "Couldn't import all the necessary modules, please ensure you have the following modules installed"
        print ">>> python-MySQLdb\n>>>python-commands\n>>python-urllib2"
	sys.exit(1)

# Header of the script either displayed in color or in plain black/white, dependant on whether termcolor is installed (Not essential)
try:
	from termcolor import colored
	print colored("\nNetflowDB. Netflow monitoring tool with Blacklist and Tor node alerts/filters!","red")
	print colored("Created by: Ed Tredgett and Mike Antcliffe (IRGeeks)!","green")
	print colored("Contact: edtredgett@logicallysecure.com, mikeantcliffe@logicallysecure.com","blue")
except:
	print """
NetflowDB. Netflow monitoring tool with Blacklist and Tor node alerts/filters!
Created by: Ed Tredgett and Mike Antcliffe (IRGeeks)!
Contact: edtredgett@logicallysecure.com, mikeantcliffe@logicallysecure.com
"""

#Edits the time stamp, so that it is stored in the database as just an integer.
def unixtime(time):
	tmo = time.replace('-', '').replace(':','').replace('.','').replace(' ','')
	return tmo

#Mileseconds function used for packet duration.
def mileseconds(m):
	milesecond = Decimal(m)*1000
	duration = str(milesecond).split('.')
	return duration[0]

#Simple function to convert an IP Address(IPv4) into a decimal number so that it can be stored as an int value.
def decimal_ip(ip):
	dec = struct.unpack("!I", socket.inet_aton(ip))[0]
	return dec

#Simple comparrison so that a integer value can represent a string within the data base.
def protocol_type(p):
# 1 = tcp | 2 = udp | 3 = icmp
	if p.lower() == 'tcp':
		return 1
	elif p.lower() == 'udp':
		return 2
	elif p.lower() == 'icmp':
		return 3
	else:
		return 4

#Replaces the binary with ASCII characters so they can be stored and presented in that way.
def flag(f):
	flag_point = f.replace('.','0').replace('A','1').replace('P','1').replace('R','1').replace('S','1').replace('F','1').replace('U','1')
	return flag_point

#Function to get the last file id from the database
def nf_files():
	query = "SELECT id, fname from nf_files"
	x.execute(query)
	id = []
	files_db = []
	for row in range(x.rowcount):
		row = x.fetchone()
		id.append(int(row[0]))
		files_db.append(row[1])
	return id, files_db

#This function looks for new netflow files within the specified path which has been created
def newnf_files():
	ids = nf_files()[0]
	files = nf_files()[1]
	try:	print colored("\n----------------------------------- Attempting to retrieve new netflow data -----------------------------------","yellow")
	except:	print "\n----------------------------------- Attempting to retrieve new netflow data -----------------------------------"
	try:	print colored("\n-------------------------- (This may take a while, dependening on how much data there is!)---------------------","yellow")
	except:	print "\n-------------------------- (This may take a while, dependening on how much data there is!)---------------------"
	for dir,subdir,file in os.walk(netflow_path):
       		path = str(dir)+'/'
#		dirList = sorted(os.listdir(path))
		for f in file:
			if f not in files:
				if "current" in f:
					pass
				else:
					try:
						new_files = f
						a = os.path.getctime(str(path)+str(new_files))
						ctime = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(int(a)))
						fname = new_files
						fpath = path
						status = 1
						utime = time.strftime("%Y-%m-%d %H:%M:%S")
						x.execute("INSERT INTO nf_files (ctime,fpath,fname,status,utime) VALUES (%s,%s,%s,%s,%s)",(ctime,fpath,fname,status,utime))
						parse_id = int(conn.insert_id())
						nf_to_csv(parse_id)
					except:
						pass

#Function to convert new netflow files to csv format ready for parsing into the database
def nf_to_csv(fid):
	query = "SELECT fpath, fname from nf_files where id = " + str(fid)
	x.execute(query)
	fname = ""
	path = ""
	fname = x.fetchone()
	total_path = ""
	for f in fname:
		total_path += f
	os.system('nfdump -r ' + str(total_path) + ' -q -o "fmt:%ts,%td,%sa,%da,%sp,%dp,%pr,%flg,%byt,%pkt" > ' + str(tmp) + str(fid) + '.csv')
	parse_csv(fid)

#This function extracts the netflow data and inserts it into the relavant columns within the table
def parse_csv(fid):
	global total_lines
	bytes = 0
	pkts = 0
	row_count = 0
	with open(str(tmp)+str(fid) +'.csv','rb') as f:
		reader = csv.reader(f)
		for row in reader:
			if first_line in row[0]:
				pass
			else:
				line = row
				if ":" in line[2]:
					src_ip = parse_ipv6(line[2].strip(' '))
				else:
					src_ip = decimal_ip(line[2].strip(' '))
				if ":" in line[3]:
					dst_ip = parse_ipv6(line[3].strip(' '))
				else:
					dst_ip = decimal_ip(line[3].strip(' '))
				row_count += 1
				total_lines += 1
				st_time = unixtime(line[0])
				milesec = mileseconds(line[1])							
				src_port = line[4].strip(' ')
				dst_port = line[5].strip(' ')
				protocol = protocol_type(line[6].strip(' '))
				flags = line[7].strip(' ')
				tbytes = line[8].strip(' ')
				bytes += int(tbytes)
				packets = line[9].strip(' ')
				pkts += int(packets)
				x.execute("INSERT INTO nf_data (ts,td,sa,da,sp,dp,pr,flg,byt,pkt,fid) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",(st_time,milesec,src_ip,dst_ip,src_port,dst_port,protocol,flags,tbytes,packets,fid))
				x.execute("UPDATE nf_files SET `status`=%s, `totalbytes`= %s, `totalpackets`= %s, `rowsproc`=%s where id = %s",(4,bytes,pkts,row_count,fid))
#OS system is used to clear up the csv files from the tmp directory after they have been utilised and the data is inserted into the database
		conn.commit()
#		os.system('rm ' + str(tmp) + '*')

#Function to parse IPv6 addresses to the database if one if encountered within the netflow data (Not fool proof yet)
def parse_ipv6(ipv6):
	global ipv6_dict
	if "empty" in ipv6_dict:
		ipv6_dict = {}
		print "fetching ipv6 array"
		query = "SELECT id,ipv6 FROM ipv6_table"
		x.execute(query)
		rows = x.fetchall()
		for line in rows:
			ipv6_dict[line[0]] = line[1]
	else:
		print "dictionary already built"
	if ipv6 in ipv6_dict.values():
		print "value found in dictionary"
		ipv6_ref = int([k for k, v in ipv6_dict.iteritems() if v == ipv6][0])
		return 5000000000 + ipv6_ref
	else:
		print "new value found"
		x.execute("INSERT INTO ipv6_table (ipv6) VALUES (%s)",(ipv6))
		ipv6_ref = int(conn.insert_id())
		ipv6_dict[ipv6_ref] = ipv6
		return 5000000000 + ipv6_ref

def main():
	global first_line
	first_line = "Verify"
	global bytes
	bytes = 0
	global pkts
	pkts = 0
	global total_lines
	total_lines = 0
	global ipv6_dict
	ipv6_dict = {'empty':'empty'}
	newnf_files()

if __name__ == "__main__":
	netflow_path = "/home/netflowdb/netflow_data"
	conn = MySQLdb.connect(host= "127.0.0.1", user="nfpy",passwd="netflowdb!!",db="nflow")
	x = conn.cursor()
	main()
	try: print "*********************************** %s lines added to the databases (Netflow Data) ***********************************"%total_lines
	except: print "*********************************** %s lines added to the databases (Netflow Data)  ***********************************"%total_lines
	print "$s lines added to DB" %total_lines
	conn.close()
