#!/usr/bin/env python3
import os,glob
from sys import argv;
script, filename = argv
filePathPre = os.environ.get('HOME')+'/www/jek/img/';
imgNameFormat = filename + '-%d.%s';
imgMD = '![%s](/img/%s)'
for oldFile in glob.iglob(os.environ.get('HOME')+'/Desktop/Screen*.*'):
    for i in range(1,29):
        imgName = imgNameFormat % (i, oldFile.split('.')[-1])
        newFilePath = filePathPre + imgName;
        if not os.path.isfile(newFilePath):
            imgMD = imgMD % (imgName, imgName)
            os.rename(oldFile,newFilePath)
            print(imgMD);
            break;
