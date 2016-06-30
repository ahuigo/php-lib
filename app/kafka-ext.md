# install librdkafka

	git clone https://github.com/edenhill/librdkafka/
	cd librdkafka
	./configure
	make 
	sudo make install
	echo '/usr/local/lib/'| sudo tee -a /etc/ld.so.conf.d/usrlib.conf
	sudo ldconfig -v

# install php client

	git clone https://github.com/arnaud-lb/php-rdkafka.git
	cd php-rdkafka
	phpize
	./configure
	make
	sudo make install
	echo 'extension=rdkafka.so' | sudo tee -a /etc/php.d/rdkafka.ini

# usage : https://github.com/arnaud-lb/php-rdkafka
