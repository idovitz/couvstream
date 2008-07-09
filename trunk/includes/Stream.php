<?php

include_once("../includes/config.php");

class Stream
{
	public function Stream()
	{
		$this->bitrate = intval($_COOKIE["bitrate"]);
		$this->setMux();
		$this->cid = intval($_COOKIE["cid"]);
		
		$this->startStream();
	}
	
	public function getUrl()
	{
		$url = $this->muxstr."://".$_SERVER["SERVER_NAME"];
		$url .= "/streams/cam".$this->cid."/".$this->bitrate."/".$this->mux."/stream.asf";
		
		return $url;
	}

	public function switchIrCutFilter()
	{
		$client = new SoapClient(null, array('location' => "http://localhost:".listenport."/", "uri" => "urn:couvstream", "style" => SOAP_RPC, "use" => SOAP_ENCODED, 'soap_version'  => SOAP_1_1, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8") );
		if($client->switchIRFilter(new SoapParam($this->cid, "cid"), new SoapParam($this->bitrate, "value")))
		{
			return True;
		}else{
			return False;
		}
	}
	
	private function setMux()
	{
		if(strpos($_SERVER["HTTP_USER_AGENT"], "Windows"))
		{
			$this->muxstr = "mms";
			$this->mux = "1";
		}else{
			$this->muxstr = "http";
			$this->mux = "0";
		}
	}
	
	private function startStream()
	{
		$client = new SoapClient(null, array('location' => "http://localhost:".listenport."/", "uri" => "urn:couvstream", "style" => SOAP_RPC, "use" => SOAP_ENCODED, 'soap_version'  => SOAP_1_1, "trace" => 1, "exceptions"=>0, "encoding" => "utf-8") );
		if($client->startStream(new SoapParam($this->cid, "cid"), new SoapParam($this->bitrate, "bitrate")))
		{
			return True;
		}else{
			return False;
		}
	}
}

?>
