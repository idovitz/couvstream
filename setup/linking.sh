#!/bin/bash

rm /usr/bin/strcond.py
rm /usr/bin/ircutd.py
rm /usr/bin/selfmon.py
rm /usr/bin/streamchecker.py
rm /usr/bin/streamcounter.py
rm /usr/bin/filterchecker.py

find ../streamcontrol/ -name "*.py" -type f -exec chmod 775 {} \;

ln -s `pwd`/../streamcontrol/strcond.py /usr/bin/
ln -s `pwd`/../streamcontrol/ircutd.py /usr/bin/
ln -s `pwd`/../streamcontrol/selfmon.py /usr/bin/
ln -s `pwd`/../streamcontrol/streamchecker.py /usr/bin/
ln -s `pwd`/../streamcontrol/streamcounter.py /usr/bin/
ln -s `pwd`/../streamcontrol/filterchecker.py /usr/bin/
ln -s `pwd`/../couvctl /usr/bin/