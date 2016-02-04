cd ~
set -o errexit;
args=("$@");
while test $# -gt 0; do
        case "$1" in
                -no-check) nohttps="--no-check-certificate";;
                *) ext="$1";
        esac;
        shift
done
nohttps="--no-check-certificate";

phpini=`php --ini | grep -o -E 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
[[ -z $phpini ]] && phpini=`php --ini | grep -o -P 'Configuration File:\s+\S+php\.ini' | awk '{print $3}'`
[[ -z $phpini ]] && echo 'Could not find php.ini!' && exit;
extension_dir=`php -i | grep -o -E "^extension_dir => \S+" | awk '{print $3}'`
[[ -z $extension_dir ]] && echo 'Could not find extension_dir!' && exit;

wget $nohttps https://pecl.php.net/get/memcached-2.2.0.tgz -O - | tar xzvf -
cd memcached*;
sudo yum install -y libmemcached libmemcached-devel;
phpize
#sudo ./configure --with-libmemcached-dir=/usr/local/libmemcached/ --disable-memcached-sasl
./configure && make && sudo make install

cat <<-'MM' | sudo tee -a $phpini

[memcached]
extension=memcached.so
MM
