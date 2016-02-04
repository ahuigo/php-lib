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
	export PS1='%n@%m%{$fg[cyan]%} %c%{$fg_bold[blue]%}$(git_prompt_info)%{$fg_bold[blue]%}>%{$reset_color%}'
MM

cd ~
env zsh
