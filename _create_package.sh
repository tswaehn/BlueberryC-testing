#!/bin/bash 
#
# author: sven.ginka@gmail.com
# date: 23.dec.2013
# modified: 18.jan.2014
#


# notes on build_nr:
#
# - the build_nr controls which actions will take place
# 
# if build_nr contains "rel" then 
# 	(1) the new package will be pushed with version number to server
#	(2) the default package will be overwritten and published
#	(3) the install scripts will be updated
#	(4) the release-list on the server will be updated
#
# if build_nr contains "rc" then
#	(1) the server path is ./rc/ 
#	(2) the new package will be pushed with version number to server
#	(3) the install scripts will be updated
#	(4) the rc-release-list on the server will be updated
#
# else
#	(1) the server path is ./testing/
#	(2) the package will be pushed into a separate directory called "testing"
#	(3) the install scripts will be updated
#	(4) the test-release-list on the server will be updated
#
#


# \todo
# - hardcoded "filelist.txt"
# - hardcoded "package.txt"


# --- config 
name="BlueberryC"

ftproot="ftp://copiino.cc/TestX/"
ftpuser=`cat ftp-login.txt`
echo "login for ftp "$ftpuser

# --- start
localroot="./_packages/"
# import build_nr
build_nr=`cat build_nr`

# check if "release", "release candidate" or "testing"
if [ $(echo -e "$build_nr" | grep -c "rel") -eq 1 ]
then
  echo "creating release"
  packagedir="rel/"
else
  if [ $(echo -e "$build_nr" | grep -c "rc") -eq 1 ]
  then
    echo "creating release candidate"
    packagedir="rc/"
  else
    echo "creating testing"
    packagedir="testing/"
  fi
fi
# set local and remote dir
ftpdir=$ftproot$packagedir
localdir=$localroot$packagedir

# setup filenames
packagename=$name"_"$build_nr".tar.gz"
defaultpackagename=$name".tar.gz"
setupname=$name"_"$build_nr"_setup.tar.gz"
defaultsetupname=$name"_setup.tar.gz"

# setup directories
filelist=$localdir"filelist.txt"
setuplist=$localdir"setuplist.txt"
packagelist=$localdir"packages.txt"
#
package=$localdir$packagename
default_package=$localdir$defaultpackagename
#
setup_package=$localdir$setupname
default_setup=$localdir$defaultsetupname


# display recipe info
echo "all files go into $localdir"

# create directory if not existent
mkdir -p $localdir

# --- create core package
  echo "--- creating package"
  echo $packagename" ("$defaultpackagename")"

  # copy build_nr for display in client
  cp build_nr ./client
  
  # remove old list
  rm -f $filelist
  
  #find ./  -maxdepth 1 -iname "upgrade.sh" -print >> $filelist
  
  find ./  -maxdepth 1 -iname "build_nr" -print >> $filelist  
  find ./  -maxdepth 1 -iname "setup.sh" -print >> $filelist

  find ./client/ -iname "*" -print >> $filelist
  find ./server/ -iname "*" -print >> $filelist
  find ./installer/ -iname "*" -print >> $filelist
  

  # create archive (note that www-data is usually uid=33 gid=33, thus change owner now)
  tar -cpzf $package --owner=33 --group=33 -T $filelist

# --- upload files to server  
  echo "--- uploading files to server"
  echo "remote target is "$ftpdir

  #move packages (raw + setup) to server
    echo "uploading new package"
    curl -P - -T $package $ftpdir --ftp-create-dirs --user $ftpuser

  # update and upload package list
    echo "adding new release to download list"
    echo -e "$build_nr\t$setupname" >> $packagelist
    curl -P - -T $packagelist $ftpdir --ftp-create-dirs --user $ftpuser
    
  # overwrite default package
    echo "uploading new default package"
    cp $package $default_package
    curl -P - -T $default_package $ftpdir --user $ftpuser


# everything is fine
exit 0

