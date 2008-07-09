#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import string, sys, socket
from SOAPpy import SOAPProxy
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

try:
    client = SOAPProxy(url, namespace=namespace)
    report = client.countStreams()
    print report[0]
except:
    print -1
