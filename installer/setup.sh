#!/bin/bash 
#
# author: sven.ginka@gmail.com
# date: 23.dec.2013
# modified: 15.apr.2014
#
# \todo
# 	check existence of "package.txt"

# config 
tempfolder=$1
log=/tmp/install-log.txt

# start
set -e

# install packages
  ./installer/su_required_packages.sh $tempfolder 
  
# install web client
  ./installer/su_web_client.sh $tempfolder 
  
# install server component
  #sudo ./su_server_component.sh >> $log

#finished
echo "install finished"

echo " --- "
myip=`sudo ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`
if [ -n "$myip" ]; then
  echo "(eth0) start a browser and type http://$myip/BlueberryC/"
fi
myip=`sudo ifconfig wlan0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`
if [ -n "$myip" ]; then
  echo "(wlan0) start a browser and type http://$myip/BlueberyC/"
fi
echo " --- "

exit 0
 
