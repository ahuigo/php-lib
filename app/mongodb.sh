# install mongodb
## see: https://docs.mongodb.com/manual/tutorial/install-mongodb-on-red-hat/
##  If you get that /usr/bin/mongod: symbol lookup error: /usr/bin/mongod: undefined symbol: _ZN7pcrecpp2RE4InitEPKcPKNS_10RE_OptionsE then you should install pcre and pcre-devel from the repository
sudo yum install pcre pcre-devel -y
sudo yum install -y mongodb-server mongodb
service mongod start

## check config: rpm -ql mongod-server | grep conf /etc/mongodb.conf
## check log in config file: logpath=/var/log/mongodb/mongod.log
