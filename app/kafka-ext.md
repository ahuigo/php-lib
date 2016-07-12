## reference: http://www.cnblogs.com/imarno/p/5198940.html
## install zookeeper

	cd
	wget http://mirror.bit.edu.cn/apache/zookeeper/zookeeper-3.4.8/zookeeper-3.4.8.tar.gz -O - | tar xzvf -
	cd zookeeper-3.4.8/src/c
	./configure && make && sudo make install
	cd

## install php-zookeeper

	cd ~
	git clone https://github.com/andreiz/php-zookeeper
	cd php-zookeeper
	phpize && ./configure && make && sudo make install
	echo 'extension=zookeeper.so' | sudo tee -a /etc/php.d/zookeeper.ini

## install librdkafka

	git clone https://github.com/edenhill/librdkafka/
	cd librdkafka
	./configure
	make
	sudo make install
	echo '/usr/local/lib/'| sudo tee -a /etc/ld.so.conf.d/usrlib.conf
	sudo ldconfig -v


## install php rdkafka

	cd ~
	git clone https://github.com/arnaud-lb/php-rdkafka.git
	cd php-rdkafka
	phpize
	./configure
	make
	sudo make install
	echo 'extension=rdkafka.so' | sudo tee -a /etc/php.d/rdkafka.ini

### usage : https://github.com/arnaud-lb/php-rdkafka
