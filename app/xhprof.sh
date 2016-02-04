####################################################
# This script is used to install xhprof extension.
# Require: wget.
# Support: supports centos, fedora, redhat, macosx.
# Author: hilojack.com
# Usage: sh <(wget https://raw.githubusercontent.com/hilojack/php-lib/master/app/xhprof.sh -O -)
####################################################
if ! php -m | grep xhprof > /dev/null; then
	cd ~;
	phpini=`php --ini | grep -o -E 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
	[[ -z $phpini ]] && phpini=`php --ini | grep -o -P 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
	[[ -z $phpini ]] && echo 'Could not find php.ini!' && exit;
	extension_dir=`php -i | grep -o -E "^extension_dir => \S+" | awk '{print $3}'`
	[[ -z $extension_dir ]] && echo 'Could not find extension_dir!' && exit;
	wget http://pecl.php.net/get/xhprof-0.9.4.tgz -O - | tar xzvf -
	cd xhprof-0.9.4/extension/
	phpize
	./configure
	make && sudo make install
	! [[ -d /opt/xhprof ]] && sudo mkdir -p /opt/xhprof;
	sudo chmod 777 /opt/xhprof/

	cat <<-MM | sudo tee -a $phpini

		[xhprof]
		extension = xhprof.so
		xhprof.output_dir = "/opt/xhprof"
	MM

	mv ~/xhprof-0.9.4/xhprof_lib/ /opt/xhprof/
	mv ~/xhprof-0.9.4/xhprof_html/ /opt/xhprof/
	#ownip=$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)
	nohup php -S 0.0.0.0:9876 -t /opt/xhprof/xhprof_html/ &
	sudo yum install graphviz -y || brew install graphviz; # xhprof's callgraph rely on graphviz
fi
