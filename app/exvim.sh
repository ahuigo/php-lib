cd ~
mkdir exvim
wget https://github.com/exvim/main/releases/download/v0.5.0/exvim-v0.5.0.tar.gz -O - | tar xzvf -C exvim
cd exvim
sed -i '/gsearch_engine/{s/idutils/grep/}' vimfiles/bundle/ex-vimentry/autoload/vimentry.vim
sh unix/replace-my-vim.sh
