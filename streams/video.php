<?php

include_once("../includes/Stream.php");
include_once("../admin/includes/Cameras.php");

$cameras = new Cameras();
$cam = $cameras->getCamera($_COOKIE["cid"]);

if($cam["blocked"] == 1)
{
	header("Location: speed.php");
}

$stream = new Stream();
$mediaUrl = $stream->getUrl();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="nl"> 
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/styles/<? echo style_name; ?>/img/favicon.ico" rel="SHORTCUT ICON" />
		<?
		if($_REQUEST["bitrate"] > "1")
		{
				echo ("<link rel='stylesheet' type='text/css' href='/styles/".style_name."/video4cif.css' />");
				$object_width = "614";
				$object_height = "460";
				$scale_href = "cif";
		}
		else
		{
				echo ("<link rel='stylesheet' type='text/css' href='/styles/".style_name."/video.css' />");
				$object_width = "352";
				$object_height = "288";
				$scale_href = "4cif";
		}
		?>
				
		<title><? echo longname; ?></title>
	</head>
<body>

<div class="container">
	
	<div class="mplayer">
		<OBJECT ID="MediaPlayer" WIDTH="<? echo ("$object_width");?>" HEIGHT="<? echo ("$object_height");?>" CLASSID="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" STANDBY="Loading Windows Media Player components..." TYPE="application/x-oleobject">
			<PARAM NAME="FileName" VALUE="<? echo $mediaUrl; ?>">
			<PARAM name="ShowControls" VALUE="false">
			<PARAM name="ShowStatusBar" value="true">
			<PARAM name="ShowDisplay" VALUE="false">
			<PARAM name="autostart" VALUE="true">
			<PARAM name="loop" VALUE="false">
			<EMBED 
				TYPE="application/x-mplayer2" 
				SRC="<? echo $mediaUrl; ?>"
				NAME="MediaPlayer"
				WIDTH="<? echo ("$object_width");?>" 
				HEIGHT="<? echo ("$object_height");?>"
				ShowControls="0" ShowStatusBar="1" ShowDisplay="0" autostart="true" loop="false"></> 
			</EMBED>
		</OBJECT>
	</div>
	
			
	<a href="speed.php?stream=<? echo ("$scale_href");?>"> 
		<img class="back" src="/styles/<? echo style_name; ?>/img/back.jpg" alt="Verander uw snelheid" />
	</a>
	<br />
	<a href="../index.php?logout=1"> 
		<img class="exit" src="/styles/<? echo style_name; ?>/img/exit.jpg" alt="Uitloggen" />
	</a>
	
	
			
</div>

</body>
</html>
