#!/usr/bin/python
__author__ = "Ed Tredgett, Mike Antcliffe - Logically Secure"

try:
	from decimal import *
	import socket,struct, sys
	import MySQLdb
	import urllib2
	from random import choice
except:
	print """
Unable to import the relevant python modules please ensure you have the following installed:
	> python-MySQLDB
	> urllib2
	> python-random
	> python-socket
	> python-struct
	> python-decimal
"""
	sys.exit(1)

blackl_ips = []
tor_ips = []
db_blackl = []
db_tornodes = []
count = 0
random = [f for f in range(11,251)]

#Converts IP Address to decimal for data storage
def decimal_ip(ip):
	dec = struct.unpack("!I", socket.inet_aton(ip))[0]
	return dec

#Retrieves blacklist from openbl
def get_blacklist():
	global count
	x = conn.cursor()
	print "Retrieving black list from the web.........."
	response = urllib2.urlopen('http://www.openbl.org/lists/base_all.txt')
	for ip_addr in response.readlines():
		if str("#") in ip_addr:
			pass
                else:
			blip = decimal_ip(ip_addr)
			if blip not in db_blackl:
				db_blackl.append(blip)
				print "New blacklisted IP found adding " + str(blip) + " to the database"
				x.execute("INSERT INTO open_bl (ip) VALUES (%s)",(blip))
			else:
				pass

#Retrieves current tor node list from torproject
def get_tornodes():
	x = conn.cursor()
	print "Retrieving tor node list from the web..........."
	url = 'https://check.torproject.org/cgi-bin/TorBulkExitList.py?ip=162.'+str(choice(random)) + '.' +str(choice(random)) + '.' + str(choice(random))
	print url
	response = urllib2.urlopen(url)
	for ip_addr in response.readlines():
		if str("#") in ip_addr:
			pass
                else:
					blip = decimal_ip(ip_addr)
#					tor_ips.append(blip)
					if blip in db_tornodes:
						pass
					else:
						db_tornodes.append(blip)
						print "New tornode found adding " + str(blip) + " to the database"
						x.execute("INSERT INTO tor_nodes (ip) VALUES (%s)",(blip))

#Compares list to find any IP Addresses which arn't currently held within the database
def compare_current():
		global db_blackl
		global db_tornodes
		x = conn.cursor()
		nfdata_ips = []
		query = "SELECT distinct sa,da FROM nf_data"
		x.execute(query)
		rows = x.fetchall()
		for line in rows:
			if int(line[0]) in nfdata_ips:
				pass
			else:
				nfdata_ips.append(int(line[0]))
			if int(line[1]) in nfdata_ips:
				pass
			else:
				nfdata_ips.append(int(line[1]))
		for ip in nfdata_ips:
			if ip in db_blackl:
				x.execute("UPDATE open_bl SET found=1" + " WHERE ip="+str(ip))
			else:
				pass
			if ip in db_tornodes:
				x.execute("UPDATE tor_nodes SET found=1" + " WHERE ip="+str(ip))
			else:
				pass
					
def main():
	global x
	query = "SELECT ip FROM open_bl"
	query_tor = "SELECT ip from tor_nodes"
	x.execute(query)
	bl_rows = x.fetchall()
	x.execute(query_tor)
	tor_rows = x.fetchall()
	for line in bl_rows:
		for x in line:
			db_blackl.append(int(x))
	for y in tor_rows:
		for b in y:
			db_tornodes.append(int(b))
	get_blacklist()
	get_tornodes()
	for line in tor_ips:
		if line in blackl_ips:
			pass
		else:
			blackl_ips.append(line)
	compare_current()
	
if __name__ == "__main__":        
	conn = MySQLdb.connect(host= "127.0.0.1", user="nfpy",passwd="netflowdb!!",db="nflow")
        x = conn.cursor()
        main()
	conn.commit()
	conn.close()
	print "Done!"
