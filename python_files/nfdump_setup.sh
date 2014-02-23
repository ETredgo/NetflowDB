if [ "$(id -u)" != "0" ]; then
	echo "\n""You need to be root or have root privs :-)"
	echo "\n""Usage: sudo ./nfdump_setup.sh""\n"
	exit 1
else
	apt-get install flow-tools gcc flex byacc librrd-dev bison make -y
	apt-get install python-mysqldb -y
	apt-get install softflowd -y 
	wget https://pypi.python.org/packages/source/t/termcolor/termcolor-1.1.0.tar.gz	
	tar xzf termcolor-1.1.0.tar.gz
	cd termcolor-1.1.0
	python setup.py install
	cd ../
	rm -rf termcolor*
	tar xzf nfdump-1.6.10.tar.gz
	cd nfdump-1.6.10
	./configure --enable-profile
	make
	make install
	rm -rf nfdump-1.6.10
fi
