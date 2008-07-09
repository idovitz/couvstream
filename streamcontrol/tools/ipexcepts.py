#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
##################################################

import os, string, sys, socket
from SOAPpy import SOAPProxy, Error

apath = os.getcwd().replace("/tools", "")
sys.path.append(apath)
from utils import *



options = OptionParser(sys.argv)
config = ConfigLoader(options.c)

# timeout in seconds
timeout = 15
socket.setdefaulttimeout(timeout)

fromaddr = config.fromaddr
toaddr = config.toaddr
url = "http://localhost:%s" % config.listenport
namespace = "urn:couvstream"

for ip in config.ipt_excepts.split(";"):
	try:
		client = SOAPProxy(url, namespace=namespace)
		report = client.registerAddress(ip)
		print report[0]
	except Error, s:
		print "FAILED %s" % s
