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
	sudo mkdir /opt
	sudo chmod a+rwx /opt
	tar xzvf /tmp/xhprof-0.9.4.tgz.1 -C /opt
	wget http://pecl.php.net/get/xhprof-0.9.4.tgz -O - | tar xzvf - -C /opt
	mv /opt/xhprof-0.9.4 /opt/xhprof

	cd /opt/xhprof/extension/
	phpize
	./configure
	make && sudo make install
	sudo chmod 777 /opt/xhprof/

	cat <<-MM | sudo tee -a $phpini

		[xhprof]
		extension = xhprof.so
		xhprof.output_dir = "/tmp"
	MM

	#ownip=$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)
	nohup php -S 0.0.0.0:9876 -t /opt/xhprof/xhprof_html/ &
	sudo yum install graphviz -y || brew install graphviz; # xhprof's callgraph rely on graphviz
fi

# nginx
cat <<-MM
server {
		root /opt/xhprof/xhprof_html;
    listen   9876;
    server_name xhprof.my.com;
    index  index.php;

    location / {
        try_files $uri $uri/ /index.php?$uri&$args;
    }

    location ~ \.php$ {
                try_files $uri =404;
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_pass 127.0.0.1:9000;
                fastcgi_index index.php;
                include fastcgi_params;
    }
}
MM
cat <<NOTE
1. php -S 0:9876 不会读取 ini_get('xhprof.output_dir');
	所以最好默认`/tmp`
NOTE
