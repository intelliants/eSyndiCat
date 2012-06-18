#!/bin/bash

echo "*****************************************************************"
echo " Hello $USER, this script will set"
echo " appropriate permissions to required directories and files."
echo " ";
echo " backup/"
echo " tmp/"
echo " plugins/"
echo " uploads/"
echo " includes/config.inc.php"
echo ""
read -s -n1 -p "Please confirm those operations [y/n]: " confirmed

if [ $confirmed = "y" ]; then
	chmod 777 ../backup/
	chmod 777 ../tmp/
	chmod 777 ../plugins/
	chmod 777 ../uploads/
	chmod 777 ../includes/config.inc.php
	echo "";
	echo "Done!";
else
	echo "ok";
fi

exit 0
