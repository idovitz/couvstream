#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import ephem
import datetime
from dateutil import tz
import os, string, sys, smtplib, time, socket
from SOAPpy import SOAPProxy, Error
from utils import *

options = OptionParser(sys.argv)
config = ConfigLoader(options.c)
l = Log(config.loglevel, "ircutd")

if config.ircut == "no":
	l.log(1, "ircut daemon stopped, not needed")
	os._exit(0)

# timeout in seconds
timeout = 50
socket.setdefaulttimeout(timeout)

fromaddr = config.fromaddr
toaddr = config.toaddr
url = "http://localhost:%s" % config.listenport
namespace = "urn:couvstream"

def switchIrcut(n):
	try:
		client = SOAPProxy(url, namespace=namespace)
		report = client.switchIRFilter("all", n)
		msg = "Subject: [FAMNET] IR CUT FILTER report\n\nreport:\n%s" % report
	except Error, s:
		l.log(1, "connection to streamer failed: %s" % s)
		msg = "Subject: [FAMNET] connection to streamer failed\n%s" % s

	server = smtplib.SMTP("localhost")
	server.sendmail(fromaddr, toaddr, msg)
	server.quit()

def getSunTimes():
	obs = ephem.Observer()
	obs.lat = '51.52'
	obs.long= '4.78'
	obs.elev = -10
	obs.pressure= 0
	obs.horizon = '-1'
	
	sun = ephem.Sun()
	
	from_zone = tz.tzutc()
	rise_time = obs.previous_rising(sun, use_center=True).datetime()
	set_time = obs.next_setting(sun, use_center=True).datetime()

	to_zone = tz.tzlocal()
	
	rise_time = rise_time.replace(tzinfo=from_zone)
	set_time = set_time.replace(tzinfo=from_zone)

	return [[rise_time.astimezone(to_zone).hour,rise_time.astimezone(to_zone).minute],[set_time.astimezone(to_zone).hour, set_time.astimezone(to_zone).minute]]
#	return [[22,25],[22,27]]

# DEAMONIZE
if __name__ == "__main__":
	pid = os.fork ()
	if pid == 0: # if pid is child
		#os.chdir('/')
		os.umask(0)
		os.setsid() # Start new process group.
		pid = os.fork () # Second fork will start detached process.
		if pid == 0: # if pid is child
			# Close all open files
			try:
				maxfd = os.sysconf("SC_OPEN_MAX")
			except (AttributeError, ValueError):
				maxfd = 256
	
			for fd in range(0, maxfd):
				try:
					os.close(fd)
				except OSError:
					pass
			
			# Reopen std(in|out|err)
			os.open("/dev/null", os.O_RDONLY) # stdin
			os.open("/dev/null", os.O_WRONLY | os.O_APPEND | os.O_CREAT, 0644)
			os.open("/dev/null", os.O_WRONLY | os.O_APPEND | os.O_CREAT, 0644)
			
			# get new times
			suntimes = getSunTimes()
			l.log(2, "i am started and received new suntimes: %s" % suntimes)
			
			# main while loop
			while 1:
				l.log(3, "Todo for time %s" % (list(time.localtime()[3:5])))
		
				# 0.00 get new suntimes
				if suntimes == 0:
					suntimes = getSunTimes()
				else:
					if list(time.localtime()[3:5]) == [0,0]:
						suntimes = getSunTimes()
						l.log(1, "received new suntimes: %s" % suntimes)
					# sunset
					if list(time.localtime()[3:5]) == suntimes[0][0:2]:
						l.log(1, "set sunset: OFF")
						switchIrcut("off")
					# sunrise
					elif list(time.localtime()[3:5]) == suntimes[1][0:2]:
						l.log(1, "set sunrise: ON")
						switchIrcut("on")
					# do nothing
					else:
						l.log(3, "False")
	
				time.sleep(60)
		else:
			os._exit(0)
	else:
		os._exit(0)
