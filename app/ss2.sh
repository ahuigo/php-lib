https://minenet.me/2016/05/10/shadowsocks-node.html

    curl --socks5 127.0.0.1:1080 http://httpbin.org/ip

默认配置：
服务器端口：自己设定（如不设定，默认为 8989）
密码：自己设定（如不设定，默认为 teddysun.com）
加密方式：自己设定（如不设定，默认为 aes-256-gcm）
备注：脚本默认创建单用户配置文件，如需配置多用户，安装完毕后参照下面的教程示例手动修改配置文件后重启即可。

Shadowsocks for Windows 客户端下载：
https://github.com/shadowsocks/shadowsocks-windows/releases

使用方法：
使用root用户登录，运行以下命令：

wget --no-check-certificate -O shadowsocks.sh https://raw.githubusercontent.com/teddysun/shadowsocks_install/master/shadowsocks.sh
chmod +x shadowsocks.sh
./shadowsocks.sh 2>&1 | tee shadowsocks.log

安装完成后，脚本提示如下：

Congratulations, Shadowsocks-python server install completed!
Your Server IP        :your_server_ip
Your Server Port      :your_server_port
Your Password         :your_password
Your Encryption Method:your_encryption_method

Welcome to visit:https://teddysun.com/342.html
Enjoy it!
卸载方法：
使用root用户登录，运行以下命令：

./shadowsocks.sh uninstall
单用户配置文件示例（2015 年 08 月 28 日修正）：
配置文件路径：/etc/shadowsocks.json

{
    "server":"0.0.0.0",
    "server_port":your_server_port,
    "local_address":"127.0.0.1",
    "local_port":1080,
    "password":"your_password",
    "timeout":300,
    "method":"your_encryption_method",
    "fast_open": false
}
多用户多端口配置文件示例（2015 年 08 月 28 日修正）：
配置文件路径：/etc/shadowsocks.json

{
    "server":"0.0.0.0",
    "local_address":"127.0.0.1",
    "local_port":1080,
    "port_password":{
         "8989":"password0",
         "9001":"password1",
         "9002":"password2",
         "9003":"password3",
         "9004":"password4"
    },
    "timeout":300,
    "method":"your_encryption_method",
    "fast_open": false
}
使用命令（2015 年 08 月 28 日修正）：
启动：/etc/init.d/shadowsocks start
停止：/etc/init.d/shadowsocks stop
重启：/etc/init.d/shadowsocks restart
状态：/etc/init.d/shadowsocks status
