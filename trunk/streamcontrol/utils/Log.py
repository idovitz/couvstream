##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import syslog

class Log:
    def __init__(self, loglevel, name):
        syslog.openlog('[FAMNET] %s' % name)
        self.loglevel = int(loglevel)
#        self.f = open(logfile, 'a')
        
        
    def log(self, msglevel, msg):
        if msglevel <= self.loglevel:
            syslog.syslog(msg)
