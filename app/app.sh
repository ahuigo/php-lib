# app
git clone https://github.com/hilojack/php-lib

# debuging
nohttps="--no-check-certificate";
zsh <(wget $nohttps https://raw.githubusercontent.com/hilojack/php-lib/master/app/debuging.sh -O -) -xhprof
#wget --no-check-certificate https://raw.githubusercontent.com/hilojack/php-lib/master/debugingLegacy.php -O /tmp/debuging.php;

# xdebug
nohttps="--no-check-certificate";
wget $nohttps https://raw.githubusercontent.com/hilojack/php-lib/master/app/xdebug.sh -O - | zsh;

# memcached
zsh <(wget https://raw.githubusercontent.com/hilojack/php-lib/master/app/memcached.sh -O -) -no-check
