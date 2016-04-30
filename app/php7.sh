# yum
sudo yum update -y
# gcc
yum install -y \
gcc-c++ autoconf \
libjpeg libjpeg-devel libpng \
libpng-devel freetype freetype-devel \
libpng libpng-devel libxml2 libxml2-devel \
zlib zlib-devel glibc glibc-devel \
glib2 glib2-devel bzip2 bzip2-devel \
ncurses curl openssl-devel \
gdbm-devel db4-devel libXpm-devel \
libX11-devel gd-devel gmp-devel \
readline-devel libxslt-devel \
expat-devel xmlrpc-c xmlrpc-c-devel \
libicu-devel libmcrypt-devel \
libmemcached-devel

# php7
cd
wget http://cn2.php.net/distributions/php-7.0.0.tar.gz
tar -xzvf php-7.0.0.tar.gz
cd php-7.0.0.0

./configure --prefix=/usr/local/php \
--with-mysql-sock --with-mysqli \
--enable-fpm  --enable-soap \
--with-libxml-dir --with-openssl \
--with-mcrypt --with-mhash \
--with-pcre-regex  --with-zlib \
--enable-bcmath --with-iconv \
--with-bz2 --enable-calendar \
--with-curl --with-cdb --enable-dom \
--enable-exif --enable-fileinfo \
--enable-filter --with-pcre-dir \
--enable-ftp --with-gd \
--with-openssl-dir --with-jpeg-dir \
--with-png-dir --with-zlib-dir \
--with-freetype-dir \
--enable-gd-native-ttf \
--enable-gd-jis-conv --with-gettext \
--with-gmp --with-mhash \
--enable-json --enable-mbstring \
--enable-mbregex \
--enable-mbregex-backtrack \
--with-libmbfl --with-onig \
--enable-pdo --with-pdo-mysql \
--with-zlib-dir  --with-readline \
--enable-session --enable-shmop \
--enable-simplexml --enable-sockets \
--enable-sysvmsg --enable-sysvsem \
--enable-sysvshm --enable-wddx \
--with-libxml-dir  --with-xsl \
--enable-zip \
--enable-mysqlnd-compression-support \
--with-pear --enable-intl \
&& make && make install

# php.ini
cp php.ini-development /usr/local/php/lib/php.ini
ls -sf /usr/local/php/lib/php.ini /etc/

# php-fpm.conf
cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
sudo ls -sf  /usr/local/php/etc/php-fpm.conf /etc/
#cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf

# php-fpm
sudo cp ~/sapi/fpm/init.d.php-fpm /etc/init.d/php-fpm
sudo chmod +x /etc/init.d/php-fpm
# start
service php-fpm start

# php-mc
cd 
git clone  https://github.com/php-memcached-dev/php-memcached.git
cd php-memcached/
git checkout php7
/usr/local/php/bin/phpize
./configure --with-php-config=/usr/local/php/bin/php-config
make
make install
