<?php

include_once "../includes/config.php";
include_once "../includes/MysqlCon.php";

class Users
{
	public function Users()
	{
		# create connection database
		$this->con = new MysqlCon(dbHost, database, dbUser, dbPassword);
	}
	
	public function addUser($name, $cid, $expiretime)
	{
		$name = trim($name);
		
		# translate name to uid 
		$uid = str_replace(" ", "_", strtolower($name));
		
		# check existing users for cid
		$delusers = $this->con->getQuery("SELECT * FROM groups WHERE cid = ".$cid);
		if(count($delusers) == 0)
		{
			if(count($this->con->getQuery("SELECT * FROM users WHERE uid = '".$uid."'")) == 0)
			{
				# create user
				$passwd = $this->generatePassword(8);
				$this->con->runQuery("INSERT INTO users (uid, password, name, expiredate, startdate) VALUES('".$uid."', '".$passwd."', '".$name."', DATE_ADD(NOW(), INTERVAL ".$expiretime." DAY), NOW())");
				$this->con->runQuery("INSERT INTO groups (uid, cid) VALUES('".$uid."', ".$cid.")");
				$this->con->runQuery("UPDATE cameras SET child_name = '".$name."' WHERE cid = '".$cid."'");
				return true;
			}else{
				return false;
			}
		}else{
			return $delusers[0];
		}
	}
	
	public function updateUser($name, $cid, $expiretime)
	{
		# translate name to uid 
		$uid = str_replace(" ", "_", strtolower($name));
		
		# get user vars
		$userArr = $this->getUser($uid);
		
		# check existing users for new cid
		$delusers = $this->con->getQuery("SELECT * FROM groups WHERE cid = ".$cid." AND uid <> '".$uid."'");
		if(count($delusers) == 0)
		{
			# update user
			$passwd = $this->generatePassword(8);
			$this->con->runQuery("UPDATE users SET name = '".$name."', expiredate = DATE_ADD(NOW(), INTERVAL ".($expiretime*60*24)." MINUTE) WHERE uid = '".$uid."'");
			$this->con->runQuery("UPDATE groups SET cid = '".$cid."' WHERE uid = '".$uid."'");
			$this->con->runQuery("UPDATE cameras SET child_name = '".$name."' WHERE cid = '".$cid."'");
			if($userArr["cid"] != $cid)
				$this->con->runQuery("UPDATE cameras SET child_name = '' WHERE cid = '".$userArr["cid"]."'");
			return true;
		}else{
			return $delusers[0];
		}
	}
	
	public function delUser($uid)
	{
		$userArr = $this->getUser($uid);
		$this->con->runQuery("UPDATE cameras SET child_name = '' WHERE cid = '".$userArr["cid"]."'");
		$this->con->runQuery("DELETE FROM users WHERE uid = '".$uid."'");
		$this->con->runQuery("DELETE FROM groups WHERE uid = '".$uid."'");
	}
	
	public function getUsers()
	{
		$userArr = $this->con->getQuery("SELECT usr.uid, usr.name, DATE_FORMAT(usr.expiredate , '%d/%m/%Y') AS expiredate, DATE_FORMAT(usr.startdate , '%d/%m/%Y') AS startdate, grp.*, cam.name AS camname FROM users usr LEFT JOIN groups grp ON usr.uid = grp.uid LEFT JOIN cameras cam ON grp.cid = cam.cid WHERE grp.cid != 0 AND grp.cid != -1 AND grp.cid != -2 ORDER BY grp.cid");
		
		return $userArr;
	}
	
	public function getUser($uid)
	{
		$userArr = $this->con->getQuery("SELECT * FROM users usr LEFT JOIN groups grp ON usr.uid = grp.uid WHERE usr.uid = '".$uid."'");
		
		return $userArr[0];
	}
	
	private function generatePassword($length = 10)
	{
		$password = "";
		$possible = "0123456789bcdfghjkmnpqrstvwxyz";
		$i = 0;
		
		while ($i < $length) {
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			if (!strstr($password, $char))
			{
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
}

?>
