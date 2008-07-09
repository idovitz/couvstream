#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import string, sys, smtplib, socket
from SOAPpy import SOAPProxy, Error
from utils import *

options = OptionParser(sys.argv)
config = ConfigLoader(options.c)
l = Log(config.loglevel, "filterChecker")

# timeout in seconds
timeout = 30
socket.setdefaulttimeout(timeout)

fromaddr = config.fromaddr
toaddr = config.toaddr
msg = ""
url = "http://localhost:%s" % config.listenport
namespace = "urn:couvstream"

l.log(3, "FilterChecker started")

try:
    client = SOAPProxy(url, namespace=namespace)
    report = client.checkAddresses()
    if len(report) > 0:
        l.log(2, "FilterChecker report\nreport:\n%s" % "\n".join(report))
except Error, s:
    l.log(1, "Filterchecker connection to streamer failed\n%s" % s)
    msg="Subject: [FAMNET] connection to streamer failed\n%s" % s

if(msg is not ""):
	server = smtplib.SMTP("localhost")
	server.sendmail(fromaddr, toaddr, msg)
	server.quit()
