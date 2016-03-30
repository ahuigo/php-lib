#!/usr/bin/env python3
import os,shutil,glob
from sys import argv;
script, filename = argv
filepathPre = os.environ.get('HOME')+'/www/jek/img/'+filename+'-'
img = '![%s](/p/%s)'
for oldFile in glob.iglob(os.environ.get('HOME')+'/Desktop/Screen*.png'):
    for i in range(1,29):
        newfilePath = filepathPre +str(i)+'.png'
        if not os.path.isfile(newfilePath):
            filename = filename + '-' + str(i) + '.png';
            img = img % (filename, filename)
            os.rename(oldFile,newfilePath)
            print(img);
            break;
