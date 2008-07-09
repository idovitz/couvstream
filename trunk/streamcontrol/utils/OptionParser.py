##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

class OptionParser:
	def __init__(self, argv):
		# default values
		self.c = "/etc/couvstream/config.xml"
		
		up = ""
		
		for arg in argv[1:]:
			if arg[0] == "-":
				up = arg[1:]
			else:
				if up != "":
					setattr(self, up, arg)
	
