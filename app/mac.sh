# brew
ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
brew install wget

# zsh autojump
sh -c "$(wget https://raw.github.com/robbyrussell/oh-my-zsh/master/tools/install.sh -O -)"
brew install autojump gnu-sed
gsed -i 's/^plugins=(git/& autojump/' ~/.zshrc
