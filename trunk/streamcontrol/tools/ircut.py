#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import os, string, sys, smtplib, time, urllib, socket
from SOAPpy import SOAPProxy, Error

apath = os.getcwd().replace("/tools", "")
sys.path.append(apath)
from utils import *

options = OptionParser(sys.argv)
print options.c
config = ConfigLoader(options.c)
l = Log(config.loglevel, "ircut")

url = "http://localhost:%s" % config.listenport
namespace = "urn:couvstream"

# timeout in seconds
timeout = 50
socket.setdefaulttimeout(timeout)

def switchIrcut(n):
    try:
        client = SOAPProxy(url, namespace=namespace)
        report = client.switchIRFilter("all", n)
        msg = "Subject: [FAMNET] IR CUT FILTER report\n\nreport:\n%s" % report
    except Error, s:
        msg="Subject: [FAMNET] connection to streamer failed\n%s" % s

    server = smtplib.SMTP("localhost")
    server.sendmail(config.fromaddr, config.toaddr, msg)
    server.quit()
    
print "switch ircut %s" % options.s
switchIrcut(options.s)
