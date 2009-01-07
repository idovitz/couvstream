#!/bin/bash

CONFIGDIR=/etc/couvstream

if test `whoami` = "root"
then
	echo -n "Do you realy want to install couvstream, this will overide all configuration!!!!
[yes/no]: "
	read INSTALL

	if [ "$INSTALL" = "yes" ]
	then
		PORTB=8000
		echo -en "\nBegin port range for stream processes [8000]: "
		read PORTB_R
		if [ "$PORTB_R" != "" ]
		then
			PORTB=$PORTB_R
		fi

		while [ "$NRCAMS" = "" ]
		do 
			echo -en "\nNumber of camera's: "
			read NRCAMS
		done

		######### copy config
		if test -d
		then
			mkdir -p $CONFIGDIR/cams
			cp ../config/example-config.xml $CONFIGDIR/config.xml
		fi
		

		######### make apache config files
		for i in `seq 1 $NRCAMS`
		do
			./addcam $i $PORTB > $CONFIGDIR/cams/cam$i
		done
		
		./linking.sh
	fi
else
	echo "run as root!"
fi