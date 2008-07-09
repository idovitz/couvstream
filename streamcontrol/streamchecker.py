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
l = Log(config.loglevel, "streamchecker")

l.log(3, "Streamchecker started")

# timeout in seconds
timeout = 40
socket.setdefaulttimeout(timeout)

fromaddr = config.fromaddr
toaddr = config.toaddr
msg = ""
url = "http://localhost:%s" % config.listenport
namespace = "urn:couvstream"

try:
    client = SOAPProxy(url, namespace=namespace)
    report = client.checkStreams()
    
    # report killed process
    if str(report).find("killed") != -1:
        l.log(2, "Streamchecker report\nreport:\n%s" % report["msg"])
except Error, s:
    l.log(1, "Streamchecker connection to streamer failed\n%s" % s)
    msg="Subject: [FAMNET] connection to streamer failed\n%s" % s

if(msg is not ""):
	server = smtplib.SMTP("localhost")
	server.sendmail(fromaddr, toaddr, msg)
	server.quit()
