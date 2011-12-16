#!/usr/bin/python
##################################################
# projectname: couvstream
# Copyright (C) 2007  IJSSELLAND ZIEKENHUIS
###################################################

import os, sys, string, SOAPpy, time, socket, urllib2, psutil
from subprocess import Popen, PIPE
from utils import *

# timeout in seconds
timeout = 4
socket.setdefaulttimeout(timeout)

###
# Controls streamprocesses
class StreamControl:
	###
	# INIT some variables
	def __init__(self):
		self.options = OptionParser(sys.argv)
		self.config = ConfigLoader(self.options.c)
		self.l = Log(self.config.loglevel, "strcond")
		
		self.bitrates = eval(self.config.bitrates)
		self.nomKill = []
		
		# connect to database
		self.db = DBmysql(self.config.dbUser, self.config.dbPassword, self.config.database)
		
	###
	# wait for child processes
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
	
	###
	# initiate stream
	def startStream(self, cid, bitrate):
		cid = int(cid)
		bitrate = int(bitrate)
		
		# log
		self.l.log(2, "start stream with camid %s and bitrate %s" % (cid, bitrate))
		
		# check if blocked
		if self.getCam(cid)[4] == 0:
			# check for existing process
			if self.checkProcess(cid, bitrate) == False:
				return self.startProcess(cid, bitrate)
			else:
				return "process not started"
		else:
			return "process not started"
	
	###
	# checks if the process is running
	def checkProcess(self, cid, bitrate):
		# get portnrs
		ports = self.getPorts(cid, bitrate)
		
		# log
		self.l.log(3, "check if vlc is running with camid %s and bitrate %s" % (cid, bitrate))
		
		# check listening ports with netstat
		p = Popen("/bin/netstat" + " -lnpt" + " | grep vlc", shell=True, stdout=PIPE)
		out = string.join(p.stdout.readlines())
		p.stdout.close()
		self.wait(p.pid)
		
		if out.find(":%s" % ports[0]) == -1 and out.find(":%s" % ports[1]) == -1:
			return False
		else:
			return True
		
	###	
	# start vlc deamon streaming process
	def startProcess(self, cid, bitrate):
		cam = self.getCam(cid)
		ports = self.getPorts(cid, bitrate)
		
		# vlc arguments (opens 2 ports with mux http:// and mms://)
		cmdArg = "%s -n 2 %s --no-audio -d 'http://%s:%s@%s/axis-cgi/mjpg/video.cgi?resolution=%s&compression=%s' --sout '#transcode{vcodec=h264,venc=x264{%s},vb=%s,scale=%s,acodec=none}:std{access=http{mime=video/x-flv},mux=ffmpeg{mux=flv},dst=:%s}'" % (self.config.nicePath, self.config.vlcPath, self.config.camUser, self.config.camPass, cam[2], self.bitrates[bitrate][0], self.bitrates[bitrate][3], self.bitrates[bitrate][5], self.bitrates[bitrate][1], self.bitrates[bitrate][2], ports[0])
		
		#cmdArg = "%s -n 2 %s --no-audio -d 'http://%s:%s@%s/axis-cgi/mjpg/video.cgi?resolution=%s&compression=%s' --sout '#transcode{vcodec=h264,venc=x264{%s},vb=%s,scale=%s,acodec=none}:duplicate{dst=std{access=http{mime=video/x-flv},mux=ffmpeg{mux=flv},dst=:%s},dst=std{mux=ts,dst=:%s}}'" % (self.config.nicePath, self.config.vlcPath, self.config.camUser, self.config.camPass, cam[2], self.bitrates[bitrate][0], self.bitrates[bitrate][3], self.bitrates[bitrate][5], self.bitrates[bitrate][1], self.bitrates[bitrate][2], ports[0], ports[1])
		
		# log
		self.l.log(2, "start vlc with cmd: %s" % cmdArg)
		
		pid = os.fork()	
		if pid == 0:
			os.setsid()
			p = Popen(cmdArg, shell=True, close_fds=True)
			self.wait()			
			os._exit(0)
		else:
			self.wait()
		
		# sleep for M$ wmplayer
		time.sleep(4)
		
		return True
	
	###
	# set ip address on self
	def getCam(self, cid):
		cam = self.db.querySQL("SELECT * FROM cameras WHERE cid = %s" % cid)
		return cam[0]
	
	###
	# portnrs: bv cam1 en bitrate nr 0 = 8001 en 8101
	def getPorts(self, cid, bitrate):
		port0 = int(self.config.beginPort)+(bitrate*100)*2+cid;
		port1 = int(self.config.beginPort)+(bitrate*100)*2+cid+100;
		return [port0,port1]
	
	###
	# get url with urllib
	# from urllib2 import Request, urlopen, URLError
	def getURL(self, host, url):
		req = urllib2.Request(url)
		self.l.log(3, "Get Url: %s" % url)
		
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
		    	self.l.log(1, "We failed to reach a server. (%s) reason: %s" % (url, e.reason))
		        return False
		    elif hasattr(e, 'code'):
		    	self.l.log(1, "The server could not fulfill the request. (%s) Error code: %s" % (url, e.code))
		        return False
		else:
		   return response.readlines()
	
	###
	# get registered addresses
	def getFilterList(self, camip):
		
		flines = self.getURL(camip, "http://%s/axis-cgi/admin/ipfilter.cgi?action=list" % (camip))
		if flines != False:
			alist = flines[0].strip().split(" ")
			alist.remove("Accept")
			alist.remove("addresses:")
			return alist
		else:
			return False
	
	###
	# get cameras where ip not is registered
	def inFilterList(self, ip, out):
		regList = []
		unregList = []
		for cam in self.getCamList():
			alist = self.getFilterList(cam[2])
			if alist != False:
				if ip in alist:
					regList.append(cam)
				else:
					unregList.append(cam)
		
		if(out == 1):
			return unregList
		else:
			return regList
	
	###
	# register allowed address in camera iptables filter
	def registerAddress(self, ip):
		added = True
		for cam in self.inFilterList(ip, 1):
			f = self.getURL(cam[2], "http://%s/axis-cgi/admin/ipfilter.cgi?action=add&ipaddress=%s&enable=yes&policy=allow" % (cam[2], ip))
			if f[1] != "OK\r\n":
				added = False
				addList.append("added %s to %s")
		
		# mail
		
		return added
	
	###
	# check filterlists in camera's for admin sessions
	def checkAddresses(self):
		# get sessions
		sesList = self.db.querySQL("SELECT ip FROM sessions WHERE expiration_date > NOW()")
		
		# get camera's
		camList = self.getCamList()
		
		removed = []
		for cam in camList:
			# get filterlist for cam
			flist = self.getFilterList(cam[2])
			
			if flist != False:
				for fip in flist:
					# check session or remove
					if (fip,) not in sesList and fip != self.config.serverAddress and fip not in self.config.ipt_excepts.split(";"):
						f = self.getURL(cam[2], "http://%s/axis-cgi/admin/ipfilter.cgi?action=remove&ipaddress=%s&enable=yes&policy=allow" % (cam[2], fip))
						removed.append("removed %s from camera %s" % (fip, cam[2]))
			
		return removed
	
	###
	# get cameralist from database
	def getCamList(self):
		camList = self.db.querySQL("SELECT * FROM cameras ORDER BY cid")
		return camList
	
	###
	# block all streams from camera
	def blockCam(self, cid):
		self.l.log(2, "block camera %s" % cid)
		cam = self.getCam(cid)
		
		if cam[4] != 1:
			camList = self.db.execSQL("UPDATE cameras SET blocked = 1 WHERE cid = %s" % cid)
		
		self.killStream(cam[2])
		
		return True
	
	###
	# block all streams from camera
	def unblockCam(self, cid):
		self.l.log(2, "unblock camera %s" % cid)
		cam = self.getCam(cid)
		
		if cam[4] != 0:
			camList = self.db.execSQL("UPDATE cameras SET blocked = 0 WHERE cid = %s" % cid)
		
		return True
	
	###
	# set ir-cut filter
	def setIRFilter(self, cid, value):
		cam = self.getCam(cid)
		r = "http://%s/axis-cgi/admin/param.cgi?action=update&root.PTZ.Various.V1.IrCutFilter=%s" % (cam[2], value)
		f = self.getURL(cam[2], "http://%s/axis-cgi/admin/param.cgi?action=update&root.PTZ.Various.V1.IrCutFilter=%s" % (cam[2], value))
		if f != False:
			r += " %s" % f[0]
		else:
			r += " failed"
		return r
		
	###
	# switch ir-cut filter
	def switchIRFilter(self, cid, value):
		r = ""
		if cid == "all":
			cams = self.getCamList()
			for cam in cams:
				r += "%s\n" % self.setIRFilter(cam[0], value)
		else:
			r += self.setIRFilter(cid, value)
		
		return r
	
	###
	# kill all streams from ip	
	def killStream(self, ip):
		for p in psutil.process_iter():
			if p.name == "vlc":
				if ''.join(p.cmdline).find('@%s/' % ip) != -1:
					p.kill()
	
	###
	# count running streams
	def countStreams(self):
		streams = 0
		retArr = {}		
		
		for p in psutil.process_iter():
			if p.name == "vlc":
				for con in p.get_connections(kind="tcp"):
					if con.status == 'ESTABLISHED' and con.remote_address[0] == "127.0.0.1":
						streams += 1
					
						try:
							p = con.local_address[1]
					
							b = (p-int(self.config.beginPort))/100
							c = (p-int(self.config.beginPort))-b*100
					
							self.l.log(3, "countstreams5 %s - %s" % (b, c))
					
							try:
								retArr["cam%s" % c].append(b)
							except:
								retArr["cam%s" % c] = [b]
						except:
							self.l.log(3, "countstreams6 %s" % (con))
		
		
		self.l.log(3, "countstreams: %s" % [streams, retArr])
		return [streams, retArr]
	
	###
	# check streams for activaty
	def checkStreams(self):
		report = []
		self.l.log(3, "checkStreams started")
		
		for p in psutil.process_iter():
			if p.name == "vlc":
				self.l.log(3, "checkStreams: %s" % p)
				kill = 1
				for con in p.get_connections(kind="tcp"):
					if con.family == socket.AF_INET and con.status == "LISTEN":
						port = con.local_address[1]
						for proxycon in p.get_connections(kind="tcp"):
							if proxycon.family == socket.AF_INET and proxycon.status == "ESTABLISHED" and proxycon.local_address[1] == port:
								kill = 0
				
				# append to kill list
				self.l.log(3, "checkStreams: %s" % [p.pid, kill])
				report.append([p.pid, kill])
			
		# check nomination list for non-existable pid's
		for nproc in self.nomKill:
			exist = 0
			for p in psutil.process_iter():
				if p.name == "vlc":
					if p.pid == nproc:
						exist = 1
			
			if exist == 0:
				self.nomKill.remove(nproc)
		
		# kill process if not used
		msg = ""
		oldNom = self.nomKill[:]
		for proc in report:
			if proc[1] == 1 and proc[0] in self.nomKill:
				process = psutil.Process(proc[0])
				process.kill()
				msg += "killed %s\n" % proc[0]
				self.nomKill.remove(proc[0])
			elif proc[0] in self.nomKill:
				self.nomKill.remove(proc[0])
				msg += "remove %s from nom\n" % proc[0]
			elif proc[1] == 1:
				self.nomKill.append(proc[0])
				msg += "add %s to nom\n" % proc[0]
			else:
				msg += "nothing to do for %s\n" % proc[0]
		
		return {"report":report,"oldnom":oldNom,"nominatie":self.nomKill,"msg":msg}

if __name__ == "__main__":
	pid = os.fork()
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
			
			
			##############################################
			# start SoapServer and Register StreamControl instance on Soap
			strControl = StreamControl()
			server = SOAPpy.SOAPServer(("127.0.0.1", int(strControl.config.listenport)), namespace="urn:couvstream")
			server.config.dumpSOAPIn = 0
			server.config.dumpSOAPOut = 0
			server.registerObject(strControl)
			server.serve_forever()
		else:
			os._exit(0)
	else:
		os._exit(0)
