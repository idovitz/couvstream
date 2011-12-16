<?php
/*
#####################################
(c) 2007 IJSSELLAND ZIEKENHUIS
projectname: couvstream
#####################################
*/
include_once "../includes/config.php";
include_once "../includes/MysqlCon.php";

class Cameras
{
	public function Cameras()
	{
		# create connection database
		$this->con = new MysqlCon(dbHost, database, dbUser, dbPassword);
	}
	
	public function getCameras()
	{
		$camArr = $this->con->getQuery("SELECT * FROM cameras ORDER BY cid");
		
		return $camArr;
	}
	
	public function getCamera($cid)
	{
		$camArr = $this->con->getQuery("SELECT * FROM cameras WHERE cid = ".$cid);
		
		return $camArr[0];
	}
	
	public function block($cid)
	{
		try {
			$client = new SoapClient(null, array('location' => "http://localhost:".listenport."/", "uri" => "urn:couvstream", "style" => SOAP_RPC, "use" => SOAP_ENCODED, 'soap_version'  => SOAP_1_1, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8") );
		} catch (Exception $e) {
			echo "<b>1Connectie naar de streamer daemon is mislukt. Neem contact op met uw systeembeheerder.</b>";
			echo $e;
		}
		
		if($client->blockCam(new SoapParam($cid, "cid")))
		{
			return True;
		}else{
			return False;
		}
	}
	
	public function unblock($cid)
	{
		try {
			$client = new SoapClient(null, array('location' => "http://localhost:".listenport."/", "uri" => "urn:couvstream", "style" => SOAP_RPC, "use" => SOAP_ENCODED, 'soap_version'  => SOAP_1_1, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8") );
		} catch (Exception $e) {
			echo "<b>2Connectie naar de streamer daemon is mislukt. Neem contact op met uw systeembeheerder.</b>";
			echo $e;
		}
		
		if($client->unblockCam(new SoapParam($cid, "cid")))
		{
			return True;
		}else{
			return False;
		}
	}
	
	public function countStreams($cid="all")
	{
		try {
//			$client = new SoapClient(null, array('location' => "http://localhost:".listenport."/", "uri" => "urn:couvstream"/*, "style" => SOAP_RPC, "use" => SOAP_ENCODED*/, 'soap_version'  => SOAP_1_1, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8") );
			$client = new SoapClient(null, array('location' => "http://localhost:".listenport, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8", "uri" => "urn:couvstream") );
		} catch (Exception $e) {
			echo "<pre>";
                        var_dump($e);
			echo "</pre>";
			exit("<b>Connectie naar de streamer daemon is mislukt. Neem contact op met uw systeembeheerder.</b>");
		}
		
		$cArr = $client->countStreams();
		
		if($cArr && !is_soap_fault($cArr))
		{
			if($cid == "all")
			{
				return $cArr[1];
			}else{
				return $cArr[1][$cid];
			}
		}else if(is_soap_fault($cArr)){
			echo "<pre>";
			var_dump($cArr);
			echo "REQUEST HEADERS:\n" . $client->__getLastRequestHeaders() . "\n";
			echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
			echo "RESPONSE HEADERS:\n" . $client->__getLastResponseHeaders() . "\n";
			echo "Response:\n" . $client->__getLastResponse() . "\n";
			echo "</pre>";
			exit("<b>4Connectie naar de streamer daemon is mislukt. Neem contact op met uw systeembeheerder.</b>");
		}else{
			return False;
			
		}
	}
}
