##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import xml.etree.ElementTree as ET

class ConfigLoader:
	def __init__(self, filename):
		tree = ET.parse(filename)
		config = tree.getroot()
		
		for group in config:
			for param in group:
				setattr(self, param.tag, param.text)
