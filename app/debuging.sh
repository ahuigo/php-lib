##################################################
## Install debuging
################################################
cd ~
set -o errexit

while test $# -gt 0; do
	case "$1" in
		-xhprof) xhprof=true;;
		-no-check) nohttps="--no-check-certificate";;
	esac;
	shift
done
nohttps="--no-check-certificate";

wget $nohttps https://raw.githubusercontent.com/hilojack/php-lib/master/debuging.php -O /tmp/debuging.php;
if [[ -n $xhprof ]];then
	wget $nohttps https://raw.githubusercontent.com/hilojack/php-lib/master/app/xhprof.sh -O - | sh;
fi

if ! php -i | grep -F debuging.php > /dev/null ;then
phpini=`php --ini | grep -o -E 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
[[ -z $phpini ]] && phpini=`php --ini | grep -o -P 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
[[ -z $phpini ]] && echo 'Could not find php.ini!' && exit;

#cat >> $phpini <<-MM
cat <<-MM | sudo tee -a $phpini
	[debuging]
	auto_prepend_file=/tmp/debuging.php
MM
fi
cd ~
