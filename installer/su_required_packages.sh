#!/bin/bash 
#
# author: sven.ginka@gmail.com
# date: 15.apr.2014
# modified: 15.apr.2014
#
# \todo
# 	check existence of "package.txt"

# check last update timestamp
# stat -c %Y /var/cache/apt/

# install apache and php5
  if [ $(dpkg-query -l | grep -c '^i.* apache2 ') -eq 1 ]
  then
    echo "apache2 allready installed"
  else
    sudo apt-get update  
    sudo apt-get install -y apache2
  fi

  if [ $(dpkg-query -l | grep -c '^i.* libapache2-mod-php5 ') -eq 1 ]
  then
    echo "libapache2-mod-php5 allready installed"
  else
    sudo apt-get update  
    sudo apt-get install -y libapache2-mod-php5
  fi
  
  if [ $(dpkg-query -l | grep -c '^i.* php5-curl ') -eq 1 ]
  then
    echo "php5-curl allready installed"
  else
    sudo apt-get update  
    sudo apt-get install -y php5-curl
  fi 

exit 0
