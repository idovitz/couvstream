<?php
include_once("../includes/Stream.php");
include_once("../admin/includes/Cameras.php");
include_once("../includes/config.php");

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
		<meta http-equiv="X-UA-Compatible" content="IE=8">
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
			$object_width = "604";
			$object_height = "476";
			$scale_href = "cif";
		}
		?>
		
		<link rel="stylesheet" href="//releases.flowplayer.org/5.4.6/skin/playful.css">
		
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.2.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="/styles/<? echo style_name;?>/css/custom-theme/jquery-ui-1.9.0.custom.css" />
		<script type="text/javascript" src="/js/fotoconfig.js"></script>
		<?php 
			if(social_media == "yes")
			{
				$socialMediaServ = explode(',', social_media_services);
				foreach($socialMediaServ as $script)
				{
					echo '<script type="text/javascript" src="/includes/social/'.$script.'/'.$script.'.js"></script>';
				}
			};
		?>
		
		<script src="//releases.flowplayer.org/5.4.6/flowplayer.min.js"></script>
		
		<script>
			flowplayer.conf = {
				ratio: 3/4,
				embed: false,
				autoplay: true
			};
</script>
		
		
		<title><? echo longname; ?></title>
	</head>
<body>
	<div class="mplayer">
		
		<div class="flowplayer" style="width: <? echo ("$object_width");?>px;">
			<video>
				<source src="<? echo $mediaUrl; ?>" type='video/flash' />
			</video>
		</div>
		
	</div>
			
	<a href="speed.php?stream=<? echo ("$scale_href");?>">
		<img class="back" src="/styles/<? echo style_name; ?>/img/back.jpg" title="Verander uw snelheid" alt="Verander uw snelheid" />
	</a>
	<br />
	<a id="logout" onclick="logout()" href="javascript:void(0)">
		<img class="exit" src="/styles/<? echo style_name; ?>/img/exit.jpg" title="Uitloggen" alt="Uitloggen" />
	</a>
	<br />
	
	<img class="neemfoto" id="neemFoto" onclick="neemfoto(this)" src="../styles/<?php echo style_name; ?>/img/fotocamera_icon.png" title="Maak foto" alt="Maak foto"/>
		
	<!-- De div waar alle Thumbnails automatisch in worden gezet  --> 
	<div id="divThumbContainer" class="thumbcontainer" title="Hier komen de foto's">
	</div>		
	<!-- Popup dialog als de gebruiker een 5e foto wilt maken -->
	<div id='dialog-message' title='Oeps!' style="display:none;">
		
	</div>
	
	<!-- Popup dialog met daarin de foto op ware grootte -->
	<div id="dialog-foto" style="display:none">
	</div>
	
	<div id="wait-dialog" style="display:none">
		Even geduld alstublieft. We zijn uw verzoek aan het verwerken.<br />
		<img class="ajax-loader" src="../styles/<?php echo style_name; ?>/img/ajax-loader.gif"/>
	</div>
	<?php 
		if(social_media == "yes")
		{
			$socialMediaServ = explode(',', social_media_services);
			foreach($socialMediaServ as $value)
			{
				include $_SERVER["DOCUMENT_ROOT"].'/includes/social/'.$value.'/'.$value.'.php';
			}
		};
	?>	
</body>
</html>
