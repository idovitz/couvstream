##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import socket, urllib2

class UrlOpener:
	###
	# get url with urllib
	# from urllib2 import Request, urlopen, URLError
	def getURL(self, host, url):
		req = urllib2.Request(url)
		
		# Create an OpenerDirector with support for Basic HTTP Authentication...
		auth_handler = urllib2.HTTPBasicAuthHandler()
		auth_handler.add_password('/', host, self.config.camUser, self.config.camPass)
		opener = urllib2.build_opener(auth_handler)
		# ...and install it globally so it can be used with urlopen.
		urllib2.install_opener(opener)
		
		try:
		    response = urllib2.urlopen(req)
		except urllib2.URLError, e:
		    if hasattr(e, 'reason'):
		        print 'We failed to reach a server. (%s)' % url
		        print 'Reason: ', e.reason
		        return False
		    elif hasattr(e, 'code'):
		        print 'The server could not fulfill the request. (%s)' % url
		        print 'Error code: ', e.code
		        return False
		else:
		   return response.readlines()
