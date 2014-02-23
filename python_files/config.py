#!/usr/bin/env python
__author__ = "Ed Tredgett - Logically Secure"

#IMPORTANT, IMPORTANT, IMPORTANT, IMPORTANT!!!!
#Your MySQL Username and Password must be entered here before the config script is run in order to correctly build the databases.
#################################################
						#
netflow_path = '/home/netflowdb/netflow_data'	               		#
mysql_usr = 'nfpy'		                       	#
mysql_passwd = 'netflowdb!!'	                       	#
						#
#################################################
#IMPORTANT, IMPORTANT, IMPORTANT, IMPORTANT!!!!

try:
	import MySQLdb
	import sys,os
except:
	print """

Required python dependencies not installed.
Ensure python-MySQLdb is installed then re-run the script to continue :)

"""

try:
	from termcolor import colored
	print colored("""
[][]    [] [][][][] [][][][][] [][][][] []         [][][]   []      []   [][][]  [][][]
[] []   [] []           []     []       []        []    []  []      []   []   [] []    []
[]  []  [] [][][]       []     [][][]   []       []      [] []  []  []   []   [] [][][][]
[]   [] [] []           []     []       []        []    []  []  []  []   []   [] []    []
[]    [][] [][][][]     []     []       [][][][]   [][][]   [][][][][]   [][][]  [][][]
""",'blue')
	print colored("NetflowDB. Netflow monitoring tool with Blacklist and Tor node alerts/filters!","blue")
	print colored("Created by: Ed Tredgett and Mike Antcliffe - Logically Secure Ltd","blue")
	print colored("Contact: edtredgett@logicallysecure.com, mikeantcliffe@logicallysecure.com","blue")
except:
	print """
[][]    [] [][][][] [][][][][] [][][][] []         [][][]   []      []   [][][]  [][][]
[] []   [] []           []     []       []        []    []  []      []   []   [] []    []
[]  []  [] [][][]       []     [][][]   []       []      [] []  []  []   []   [] [][][][]
[]   [] [] []           []     []       []        []    []  []  []  []   []   [] []    []
[]    [][] [][][][]     []     []       [][][][]   [][][]   [][][][][]   [][][]  [][][]

NetflowDB. Netflow monitoring tool with Blacklist and Tor node alerts/filters!
Created by: Ed Tredgett and Mike Antcliffe - Logically Secure Ltd!
Contact: edtredgett@logicallysecure.com, mikeantcliffe@logicallysecure.com
        """

#def create_db():
#	x = conn.cursor()
#	try:	x.execute("CREATE DATABASE nflow;")
#	except Exception: 
#		try: 
#			print colored("""
####################################################################################################
# 					ERROR!							   #
#												   #	
#   (1) You may not have specified the MySQL username or password in this scirpt   		   #
#   (2) The Database may have already been create, delete it and re-run this script to continue    #
#   												   #
#					ERROR!		            				   #
####################################################################################################
#""",'red')
#		except:
#			print """
#~~~~~~~~~~~~ ERROR! ~~~~~~~~~~
#
#Database has already been created, delete database and re-run this config script to continue
#
#~~~~~~~~~~~~ ERROR! ~~~~~~~~~~
#"""
#           
#		sys.exit(1)
#	conn.close()
#	create_tables()

def create_tables():
	conn = MySQLdb.connect(host="127.0.0.1", user=mysql_usr,passwd=mysql_passwd,db="nflow",local_infile=1)
	x = conn.cursor()
	try:
		x.execute("CREATE TABLE IF NOT EXISTS `ipv6_table` (`id` int(12) NOT NULL AUTO_INCREMENT,`ipv6` mediumtext,PRIMARY KEY (`id`))")	
		x.execute("CREATE TABLE IF NOT EXISTS `nf_data` (`id` int(12) NOT NULL AUTO_INCREMENT,`ts` bigint(17) NOT NULL,`td` int(12) NOT NULL,`sa` bigint(10) NOT NULL,`da` bigint(10) NOT NULL,`sp` int(5) NOT NULL,`dp` int(5) NOT NULL,`pr` int(2) NOT NULL,`flg` varchar(6) NOT NULL,`byt` int(12) NOT NULL,`pkt` int(12) NOT NULL,`fid` int(12) NOT NULL,PRIMARY KEY (`id`)) ")
		x.execute("CREATE TABLE IF NOT EXISTS `nf_files` (`id` int(12) NOT NULL AUTO_INCREMENT,`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`fpath` varchar(64) NOT NULL DEFAULT '/',`fname` varchar(20) NOT NULL,`status` int(2) NOT NULL DEFAULT '1',`utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',`totalbytes` int(24) NOT NULL DEFAULT '0',`totalpackets` int(24) NOT NULL DEFAULT '0',`rowsproc` int(12) NOT NULL DEFAULT '0',`rowscsv` int(12) NOT NULL DEFAULT '0',PRIMARY KEY (`id`)) ")
		x.execute("CREATE TABLE IF NOT EXISTS `open_bl` (`id` int(12) NOT NULL AUTO_INCREMENT,`ip` bigint(12) NOT NULL,`found` int(1) NOT NULL DEFAULT '0',`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ")
		x.execute("CREATE TABLE IF NOT EXISTS `tor_nodes` (`id` int(12) NOT NULL AUTO_INCREMENT,`ip` bigint(12) NOT NULL,`found` int(1) NOT NULL DEFAULT '0',`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ")
		x.execute("LOAD DATA LOCAL INFILE 'open_bl.csv' INTO TABLE open_bl FIELDS TERMINATED BY ',';")
		x.execute("LOAD DATA LOCAL INFILE 'tor_nodes.csv' INTO TABLE tor_nodes FIELDS TERMINATED BY ',';")
		conn.commit()
		try: print colored('\nAll Databases have been built :-) The Blacklist and Tor data has been imported also!\n','green')
		except: print '\nAll Databases have been built :-) The blacklist and tor data has been imported also!\n'
	except Exception:
		print '\n Error creating tables, delete then nflow database then rerun this config script to continue\n'
		sys.exit(1)
	append_script()

def append_script():
	with open("netflowdb.py", "a") as myfile:
		myfile.write('	netflow_path = "%s"'%netflow_path)
		myfile.write('\n	conn = MySQLdb.connect(host= "127.0.0.1", user="%s",passwd="%s",db="nflow")'%(mysql_usr,mysql_passwd))
		myfile.write('\n	x = conn.cursor()')
		myfile.write('\n	main()')
		myfile.write('\n	try: print colored("*********************************** %s lines added to the databases (Netflow Data) ***********************************","green")%total_lines')
		myfile.write('\n	except: print "*********************************** %s lines added to the databases (Netflow Data)  ***********************************"%total_lines')
		myfile.write('\n	conn.close()')
	with open("tor_bl.py","a") as myfiles:
		myfiles.write('        conn = MySQLdb.connect(host= "127.0.0.1", user="%s",passwd="%s",db="nflow")'%(mysql_usr,mysql_passwd))
                myfiles.write('\n        x = conn.cursor()')
                myfiles.write('\n        main()')
		myfiles.write('\n	conn.close()')

if __name__ == "__main__":
	try:
		conn = MySQLdb.connect(host= "127.0.0.1", user=mysql_usr,passwd=mysql_passwd, local_infile = 1)
	except:
		print "Wrong username/password, please try again"
		sys.exit(1)
	x = conn.cursor()
	create_tables()
