# install zsh and oh-my-zsh
# wget https://raw.githubusercontent.com/hilojack/php-lib/master/app/zsh.sh -O - | sh
cd ~;
sudo yum install zsh -y ;
curl -s https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh | sh

# autojump
cd ~;
git clone git://github.com/joelthelion/autojump.git
cd autojump;
./install.py;
echo "[[ -s $HOME/.autojump/etc/profile.d/autojump.sh ]] && source $HOME/.autojump/etc/profile.d/autojump.sh" >> ~/.zshrc
echo "autoload -U compinit && compinit -u" >> ~/.zshrc

# prompt
cat <<-'MM' | tee -a ~/.zshrc
	${ret_status} 105 %{$fg[cyan]%}%c%{$reset_color%} $(git_prompt_info)
MM

cd ~
env zsh

exit;
