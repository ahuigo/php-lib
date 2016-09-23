wget http://downloads.sourceforge.net/infozip/unzip60.tar.gz  -O - | tar xzvf -
cd unzip60
make -f unix/Makefile generic
mkdir ~/usr
make prefix=~/usr/ -f unix/Makefile install
export PATH=$PATH:~/usr/bin/
echo 'export PATH=$PATH:~/usr/bin/' >> ~/.bashrc

