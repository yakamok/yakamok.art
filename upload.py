#resize and move images
import os
import glob
import sys


originDir = '/path/to/source/' #the sources images that need to be processed
destDir = '/path/to/destination/' #This is the final destination dir
images = glob.glob(originDir + "*jpg") #index files ready for processing

for x in images:
    os.system('mogrify -quality 80 -auto-orient -resize "1280x1280>" ' + x) #resize images to desired size and quality
    os.system('cp ' + x + " " + x.replace(".jpg",".th.jpg") ) #create copys to be turned into thumbnails
    os.system("mogrify -quality 55 -auto-orient -resize 310 " + x.replace(".jpg",".th.jpg")) #create thumbnails and resize and reduce quality



os.system('for i in ' + originDir + '*.th.jpg; do echo "Processing $i"; exiftool -all= "$i"; done') #remove all exif data from thumbs to reduce file size
os.system('rsync -av ' + originDir + '*.jpg ' destDir) #sync local copy to production server
os.system('rm ' + originDir + '*.jpg') #cleanup source folder ready for new images

