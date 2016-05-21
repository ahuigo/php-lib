# 1. src
#git clone git://github.com/php/php-src.git
git clone https://git.php.net/repository/php-src.git
cd php-src && git checkout PHP-5.3 # 签出5.3分支

# 2. 准备编译环境
sudo apt-get install build-essential

# 3. 编译
cd ~/php-src
./buildconf
# 执行完以后就可以开始configure了，configure有很多的参数，比如指定安装目录，是否开启相关模块等选项：
./configure --help # 查看可用参数
# 为了尽快得到可以测试的环境，我们仅编译一个最精简的PHP:
./configure --disable-all
make

# 测试下
./sapi/cli/php -v
