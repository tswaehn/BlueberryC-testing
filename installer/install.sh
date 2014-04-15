#!/bin/bash 
#
# author: sven.ginka@gmail.com
# date: 23.dec.2013
# modified: 15.apr.2014
#

# this script will be invoked per cmd line by

# install release
# curl http://www.copiino.cc/BlueberryC/install.sh | bash -s 
#
# install release candidate
# curl http://www.copiino.cc/BlueberryC/install.sh | bash -s rc
#
# install release testing
# curl http://www.copiino.cc/BlueberryC/install.sh | bash -s testing



# get install type
type=$1

# config for WebAppCenter
#dl_root="http://www.copiino.cc/BlueberryC/"
dl_root="http://www.copiino.cc/TestX/"
dl_name="BlueberryC.tar.gz"
setup_name="installer/setup.sh"

# type selector
  if [ -z $type ] || [ $(echo -e "$type" | grep -c "rel") -eq 1 ]
  then
    echo "install release"
    dl_file=$dl_root"rel/"$dl_name
  else
    if [ $(echo -e "$type" | grep -c "rc") -eq 1 ]
    then
      echo "install release candidate"
      dl_file=$dl_root"rc/"$dl_name
    else
      echo "install testing"
      dl_file=$dl_root"testing/"$dl_name
    fi
  fi

# create and move to a save place (tempfolder)
cd /tmp/
tempfolder="/tmp/BlueberryC/"
mkdir -p $tempfolder
cd $tempfolder
# cleanup first
sudo rm $tempfolder"/*" -r -f

# set default variables
local_file=$tempfolder$dl_name
log=$tempfolder"install-log.txt"
setup=$tempfolder$setup_name

# start download setup package
echo "downloading setup content from $dl_file ... "
curl -s $dl_file -o $local_file

echo "extracting ..."
sudo tar -xvpf $local_file > $log

if [ -e $setup ]; then
  echo "starting setup"
  $setup $tempfolder
  exit 0
  
else
  echo "error: setup file missing"
  exit 1
fi

