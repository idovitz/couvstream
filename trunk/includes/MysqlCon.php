<?php

class MysqlCon
{
	# class constructor
	public function MysqlCon($server, $database, $user="root", $password="")
	{
		$this->con = new mysqli($server, $user, $password, $database);
		
		/* check connection */
		if (mysqli_connect_errno()) {
			printf("MYSQL Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
	}
	
	# query met result (geeft array terug met resultaten)
	public function getQuery($sql)
	{
		if($result = $this->con->query($sql))
		{
			$returnArr = array();
			while($rArr = $result->fetch_assoc())
			{
				array_push($returnArr, $rArr);
			}
			
			$result->close();
			
			return $returnArr;
		}
		else
		{
			printf("MYSQL Errormessage: %s\n", $this->con->error);
			return false;
		}
	}
	
	# query zonder result
	public function runQuery($sql)
	{
		if($this->con->query($sql) == true)
		{
			return true;
		}
		else
		{
			printf("MYSQL Errormessage: ERROR in SQL=[%s] %s\n", $sql, $this->con->error);
			return false;
		}
	}
	
	# escape bijzondere tekens
	public function escape_string($sqldata)
	{
		return $sqldata;
		return $this->con->real_escape_string($sqldata);
	}
	
	public function __destruct()
	{
		$this->con->close();
	}
}

?>
