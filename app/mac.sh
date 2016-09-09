# insall all soft on mac osx
# install brew
ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
brew install wget

# zsh autojump
sh -c "$(wget https://raw.github.com/robbyrussell/oh-my-zsh/master/tools/install.sh -O -)"
brew install autojump gnu-sed
gsed -i 's/^plugins=(git/& autojump/' ~/.zshrc



# nginx

## This tap is designed specifically for a custom build of Nginx with more module options.
brew tap homebrew/nginx 
brew install nginx-full --with-echo-module

### myself
rm -r /usr/local/var/www
ln -s /Users/hilojack/www /usr/local/var/www
sudo ln -s /usr/local/etc/nginx /etc/nginx

# php
brew install php55 --with-fpm php55-mcrypt php55-memcached php55-xdebug php55-yaf

## xdebug.ini

cat <<MM | tee -a /usr/local/etc/php/5.6/conf.d/ext-xdebug.ini
	[xdebug]
	zend_extension="/usr/local/Cellar/php54-xdebug/2.2.5/xdebug.so"
	;通过GET/POST/COOKIE:传XDEBUG_PROFILE触发
	xdebug.profiler_enable_trigger=1
	;通过GET/POST/COOKIE:传XDEBUG_TRACE触发
	xdebug.profiler_output_dir="/Users/hilojack/test/xdebug"
	;full variable contents and variable name
	xdebug.profiler_output_name=callgrind.%p
	xdebug.trace_enable_trigger=1
	xdebug.trace_output_dir="/Users/hilojack/test/xdebug"

	xdebug.collect_params=3
	xdebug.show_mem_delta=1

	xdebug.var_display_max_children=1280
	xdebug.var_display_max_data=10000
	xdebug.var_display_max_depth=6

	[yaf]
	;yaf.library=/usr/
	
MM

## start

### start up
cp /usr/local/Cellar/php56/*/homebrew.mxcl.php56.plist ~/Library/LaunchAgents/
### start manual
launchctl load -w ~/Library/LaunchAgents/homebrew.mxcl.php55.plist 
### stop manual
launchctl unload -w ~/Library/LaunchAgents/homebrew.mxcl.php55.plist 

### bugs
#// refer /var/log/system.log
