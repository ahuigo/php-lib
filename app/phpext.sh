####################################################
# This script is used to install php extension.
# Require: wget.
# Support: supports centos, fedora, redhat, macosx.
# Author: hilojack.com
# Usage: sh <(wget https://raw.githubusercontent.com/hilojack/php-lib/master/app/phpext.sh -O -) phptrace
####################################################
cd ~
set -o errexit;
args=("$@");
while test $# -gt 0; do
	case "$1" in 
		-no-check) nohttps="--no-check-certificate";;
		[[:alpha:]]*) ext="$1";;
	esac;
	shift
done
[[ -z "$ext" ]] && exit;

php -m | grep -F "$ext" && echo "Warning: you have installed $ext before!" && exit;
if ! php -m | grep $ext > /dev/null; then
	phpini=`php --ini | grep -o -E 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
	[[ -z $phpini ]] && phpini=`php --ini | grep -o -P 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
	[[ -z $phpini ]] && echo 'Could not find php.ini!' && exit;
	extension_dir=`php -i | grep -o -E "^extension_dir => \S+" | awk '{print $3}'`
	[[ -z $extension_dir ]] && echo 'Could not find extension_dir!' && exit;

	source <(wget $nohttps https://raw.githubusercontent.com/hilojack/php-lib/master/app/$ext.sh -O -) "${args[@]}"

	if ps aux| grep php-fpm > /dev/null; then
		if hash service; then
			sudo service php-fpm restart;
		elif hash php-fpm; then
			sudo pkill php-fpm;
			sudo php-fpm -D;
		fi;
	else
		sudo apachectl restart;
	fi
fi
cd ~
