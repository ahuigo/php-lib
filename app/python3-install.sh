# p3 and pip3
yum groupinstall 'Development Tools' -y
yum install zlib-devel bzip2-devel  openssl-devel ncurses-devel -y
wget  https://www.python.org/ftp/python/3.5.2/Python-3.5.2.tar.xz
tar Jxvf  Python-3.5.2.tar.xz
cd Python-3.5.2
./configure --prefix=/usr/local/python3
make && make install
echo 'export PATH=$PATH:/usr/local/python3/bin' >> ~/.bashrc

