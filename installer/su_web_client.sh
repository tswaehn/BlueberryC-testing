#!/bin/bash 
#
# author: sven.ginka@gmail.com
# date: 23.dec.2013
# modified: 15.apr.2014
#
# \todo
# 	check existence of "package.txt"


#config
tempfolder=$1
install_path=/var/www/BlueberryC/

#start
client=$tempfolder"client/"

# move files to folder
  echo "--- install web client "
  
  # setup directories
  echo "creating target directory ($install_path)"
  sudo mkdir -p $install_path || exit 1

  # move over all important files
  echo "copying files from $client"
  sudo cp $client"." $install_path -r -f || exit 1
  
  # make them available to apache2
  sudo chown www-data.www-data $install_path -R || exit 1
  
  
exit 0
