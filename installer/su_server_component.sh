#!/bin/bash 
#
# author: sven.ginka@gmail.com
# date: 23.dec.2013
# modified: 15.apr.2014
#
# \todo
# 	check existence of "package.txt"
 
  echo "--- install socket"
  sudo cp BlueberryC-server/blueberryc-server /etc/init.d/
  sudo cp -R BlueberryC-server /usr/lib/

  ## update startup information
  sudo update-rc.d blueberryc-server defaults

  ## restart blueberryc-server socket
  sudo /etc/init.d/blueberryc-server restart
  

  # -- add www-data to sudoers - this is neccessary because we
  #    need access to reboot/shutdown/.. 
  if ! [ -e "/etc/sudoers.d/www-data" ]; then
    echo "adding www-data to sudoers"
    sudo bash -c "echo 'www-data ALL=(ALL) NOPASSWD: ALL' > /etc/sudoers.d/www-data"
  fi 

exit 0