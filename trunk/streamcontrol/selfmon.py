#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import xml.etree.ElementTree as ET
import os, string, sys, smtplib, time, urllib2, socket
from subprocess import Popen, PIPE
from SOAPpy import SOAPProxy, Error
from utils import *

options = OptionParser(sys.argv)
config = ConfigLoader(options.c)
l = Log(config.loglevel, "selfmon")

# timeout in seconds
timeout = 50
socket.setdefaulttimeout(timeout)

fromaddr = config.fromaddr
toaddr = config.toaddr
url = "http://localhost:%s" % config.listenport
namespace = "urn:couvstream"

class Main:
	def __init__(self, l, config):
		self.l = l
		self.config = config
		loopcounter = 0
		
		# main while loop
		while 1:
			msg = ""
			self.l.log(3, "######################### loop %s #########################" % loopcounter)
			loopcounter += 1
			
			if self.countProc("strcond.py") < 1:
				self.l.log(1, "Streamer daemon is not running!")
				self.startProcess("/usr/bin/strcond.py")
				msg = "Subject: [FAMNET] Streamer daemon was not running, restarted.\nContact your administrator if message occurs."
			
			if self.config.ircut == "yes":
				if self.countProc("ircutd.py") < 1:
					self.l.log(1, "Ircut daemon is not running!")
					self.startProcess("/usr/bin/ircutd.py")
					msg = "Subject: [FAMNET] Ircut daemon was not running, restarted.\nContact your administrator if message occurs."
					
			if msg != "":
				server = smtplib.SMTP("localhost")
				server.sendmail(fromaddr, toaddr, msg)
				server.quit()
			
			time.sleep(15)
	
	def startProcess(self, path, args=""):
		pid = os.fork()	
		if pid == 0:
			os.setsid()
			p = Popen(path+args, shell=True, close_fds=True)
			self.wait()
			os._exit(0)
		else:
			self.wait()
		
	def countProc(self, name):
		self.l.log(3, "check %s" % name)
		
		p = Popen("/bin/ps" + " ax" + " | grep %s | grep -v grep" % name, shell=True, stdout=PIPE)
		op = string.join(p.stdout.readlines())
		p.stdout.close()
		self.wait(p.pid)
		
		oa = op.strip().split("\n")
		for p in oa:
			self.l.log(3, p)
		
		if oa[0] == "":
			return 0
		return len(oa)
		
	def wait(self, pid=-1):
		if pid == -1:
			while 1:
				try:
					os.wait()
				except:
					break
		else:
			try:
				os.waitpid(pid,0)
			except:
				pass
		
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
			
			# start main loop
			m = Main(l, config)
		else:
			os._exit(0)
	else:
		os._exit(0)
