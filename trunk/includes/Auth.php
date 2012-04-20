<?php

include_once "includes/MysqlCon.php";

class Auth
{
	# class constructor
	public function Auth($username, $password)
	{
		# create connection database
		$this->con = new MysqlCon(dbHost, database, dbUser, dbPassword);
		
		$this->username = $this->con->con->real_escape_string(trim($username));
		$this->password = $this->con->con->real_escape_string(trim($password));
	}
	
	# initiate check
	public function check()
	{
		if($this->username == false)
		{
			return $this->checkSession();
		}else{
			if($this->checkUserPass())
			{
				$this->setSession();
				return true;
			}else{
				$this->errorTxt = "De ingevulde gegevens waren niet correct. Log opnieuw in.";
				return false;
			}
		}
	}
	
	# check if local network from config
	public function checkNetwork()
	{
		$address = split("\.", $_SERVER["REMOTE_ADDR"]);
		$netw = split("\.", local_network);
		
		$tmp = split("/", $netw[3]);
		$netw[3] = $tmp[0];
		$netw[4] = $tmp[1];
		
		$ret = true;
		for($i=0; $i<($netw[4]/8); $i++)
		{
			if($address[$i] != $netw[$i])
			{
				$ret = false;
			}
		}
		
		return $ret;
	}
		
	# logout
	public function logout()
	{
		# delete session cookies
		setCookie("sid", "");
		setCookie("uid", "");
		setCookie("cid", "");
		
		# delete session in database
		$this->con->runQuery("DELETE FROM sessions WHERE sid = '".$_COOKIE["sid"]."'");
	}
	
	# call for register address to streamer
	public function registerAddress()
	{
		$client = new SoapClient(null, array('location' => "http://localhost:".listenport."/", "uri" => "urn:couvstream", "style" => SOAP_RPC, "use" => SOAP_ENCODED, 'soap_version'  => SOAP_1_1, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8") );
		if($client->registerAddress(new SoapParam($_SERVER["REMOTE_ADDR"], "ip")))
		{
			return True;
		}else{
			return False;
		}
	}
	
	# check credentials
	private function checkUserPass()
	{
		$userArr = $this->con->getQuery("SELECT * FROM users usr JOIN groups grp ON usr.uid = grp.uid WHERE usr.uid = '".$this->username."' AND usr.expiredate > NOW()");
		if($userArr)
		{
			if($this->password == $userArr[0]["password"])
			{
				setcookie("cid", $userArr[0]["cid"]);
				
				if($userArr[0]["cid"] == -1 || $userArr[0]["cid"] == -2)
				{
					$this->expiretime = expiretime_viewer;
				}else if($userArr[0]["cid"] == 0){
					$this->expiretime = expiretime_admin;
				}else{
					$this->expiretime = expiretime_users;
				}
				
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	# create new session
	private function setSession()
	{
		$sessid = $this->generateSID();
	
		# write user + sessionid - database
		$this->con->runQuery("INSERT INTO sessions (sid, uid, expiration_date, ip) VALUES ('".$sessid."', '".$this->username."', DATE_ADD(NOW(), INTERVAL ".$this->expiretime." MINUTE), '".$_SERVER["REMOTE_ADDR"]."')");
		
		# write user + sessionid - cookies
		setcookie("uid", $this->username);
		setcookie("sid", $sessid);
	}
	
	# check existing sessions
	private function checkSession()
	{
		if($_COOKIE["sid"])
		{
			# request not expired session
			$sesArr = $this->con->getQuery("SELECT * FROM sessions WHERE sid = '".$_COOKIE["sid"]."' AND expiration_date > NOW()");
			if($sesArr)
			{
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	# generate new randomized session id
	private function generateSID()
	{
		$sessid = sha1("session".$this->username.$this->password.time().rand(0, 100000));
		return $sessid;
	}
}

?>
