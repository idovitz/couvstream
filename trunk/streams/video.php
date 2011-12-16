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
				$scale_href = "4cif";
		}
		else
		{
				echo ("<link rel='stylesheet' type='text/css' href='/styles/".style_name."/video.css' />");
				$object_width = "352";
				$object_height = "288";
				$scale_href = "cif";
		}
		?>
		<script type="text/javascript" src="/flowplayer/flowplayer.min.js"></script>
		<script type="text/javascript" src="/flowplayer/flowplayer.ipad-3.2.2.min.js"></script>
		<title><? echo longname; ?></title>
	</head>
<body>

<div class="container">
	
	<div class="mplayer">
		<div id="flowplayerDiv">
		
		
		<?
		if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad'))
		{
		?>
		<video src="<? echo $mediaUrl; ?>" />
		
		<?
		}else{
		?>
		<a  
			 href="<? echo $mediaUrl; ?>"
			 style="display:block;width:<? echo ("$object_width");?>px;height:<? echo ("$object_height");?>px;minWidth:<? echo ("$object_width");?>px"
			 id="player">
		</a> 
		<script>	
			flowplayer("player", "/flowplayer/flowplayer-3.2.7.swf", {
				clip: {
					scaling: 'fit',
					metaData: false
				},
				plugins: {
					controls: {
						play:false,
						volume:false,
						mute:false,
						time:false,
						stop:false,
						playlist:false,
						fullscreen:true,
						scrubber: false
					}
				}
			});
		</script>
		<?
		}
		?>
		
		</div>
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
