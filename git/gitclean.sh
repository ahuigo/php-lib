# This script is used to clean all git commit
# Refer to : http://stackoverflow.com/questions/13716658/how-to-delete-all-commit-history-in-github
# To delete sensitive data from git: https://help.github.com/articles/remove-sensitive-data/
git checkout --orphan latest_branch
git add -A
git commit -am "Delete all previous commit"
git branch -D master
git branch -m master
git push -f origin master
