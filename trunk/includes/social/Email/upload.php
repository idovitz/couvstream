<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if($_GET['functie'] == "checkUser")
{
	echo checkUser();
}
elseif($_GET['functie'] == "emailVerzenden")
{
	echo emailVerzenden();
}

function checkUser()
{
	return json_encode($check = array(
			'fotonaam' => $_GET['fotonaam'],
		));
}

function emailVerzenden()
{
	$strTo = $_GET["emailOntvanger"];	
	$strSubject = $_GET["emailOnderwerp"];
	$strMessage = nl2br($_GET["emailBericht"]);
	
	$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
	
	if(preg_match($regex, $strTo) == false)
	{
		return json_encode($emailCheck = array(
				'error1' => true,
				));
	}
	else if($_GET['noemFoto'] == "")
	{
		return json_encode($emailCheck = array(
				'error2' => true,
		));
	}
	else if($_GET['naamVerzender'] == "")
	{
		return json_encode($emailCheck = array(
				'error2' => true,
		));
	}
	else if($_GET['emailVerzender'] == "")
	{
		return json_encode($emailCheck = array(
				'error2' => true,
		));
	}
	else if($_GET['emailOntvanger'] == "")
	{
		return json_encode($emailCheck = array(
				'error2' => true,
		));
	}
	else if($_GET['emailOnderwerp'] == "")
	{
		return json_encode($emailCheck = array(
				'error2' => true,
		));
	}
	else if($_GET['emailBericht'] == "")
	{
		return json_encode($emailCheck = array(
				'error2' => true,
		));
	}
	else
	{
		//*** Uniqid Session ***//
		$strSid = md5(uniqid(time()));
		
		$strHeader = "";
		$strHeader .= "From: ".$_GET["naamVerzender"]."<".$_GET["emailVerzender"].">\nReply-To: ".$_GET["emailVerzender"]."";
		
		$strHeader .= "MIME-Version: 1.0\n";
		$strHeader .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n\n";
		$strHeader .= "This is a multi-part message in MIME format.\n";
		
		$strHeader .= "--".$strSid."\n";
		$strHeader .= "Content-type: text/html; charset=utf-8\n";
		$strHeader .= "Content-Transfer-Encoding: 7bit\n\n";
		$strHeader .= $strMessage."\n\n";
		
		$strFilesName = $_GET['noemFoto'].".jpg";
		$strContent = chunk_split(base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/streams/tmp/img/".$_GET['fotonaam'].".jpg")));
		$strHeader .= "--".$strSid."\n";
		$strHeader .= "Content-Type: image/jpeg; name=\"".$strFilesName."\"\n";
		$strHeader .= "Content-Transfer-Encoding: base64\n";
		$strHeader .= "Content-Disposition: attachment; filename=\"".$strFilesName."\"\n\n";
		$strHeader .= $strContent."\n\n";
		
		$flgSend = @mail($strTo,$strSubject,null,$strHeader);
		
		if($flgSend)
		{
			return json_encode($emailCheck = array(
					'error' => false
			));
		}
		else
		{
			return json_encode($emailCheck = array(
					'error' => true,
					'fotonaam' => $_GET['fotonaam']
			));
		}
	}
}
?>