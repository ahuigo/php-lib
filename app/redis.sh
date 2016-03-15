cd ~
set -o errexit;
wget http://download.redis.io/releases/redis-3.0.7.tar.gz -O - | tar xzvf -
cd redis-3.0.7
make
#make install
nohup src/redis-server &
