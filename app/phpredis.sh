if hash git; then
	git clone https://github.com/phpredis/phpredis
else
	echo "Please install git! " && exit;
fi

# 1. install phptrace extension
cd phpredis
phpize
./configure && make && sudo make install

cat <<-MM | sudo tee -a $phpini

	[phptrace]
	extension=redis.so

MM
