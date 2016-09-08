cd ~
set -o errexit;
wget http://download.redis.io/releases/redis-3.2.3.tar.gz -O - | tar xzvf -
cd redis-3.2.3
make
#export PATH=$PATH:./src/
#make install
nohup src/redis-server &
