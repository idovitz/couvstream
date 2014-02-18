<?php

include_once "includes/Cameras.php";

$cameras = new Cameras();

if($_GET["block"])
{
	$cameras->block($_GET["block"]);
}

if($_GET["unblock"])
{
	$cameras->unblock($_GET["unblock"]);
}

$camArr = $cameras->getCameras();
$streams = $cameras->countStreams();

?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/styles/<? echo style_name; ?>/img/favicon.ico" rel="SHORTCUT ICON" />
	<link rel="stylesheet" type="text/css" href="/styles/<? echo style_name; ?>/admin.css" />
	<meta http-equiv="refresh" content="60" />
	<title><? echo systemname; ?> Beheer</title>
	<script type="text/javascript">
	function timedRefresh()
	{
		var t=setTimeout("location.reload()", 300000)
	}
	</script>
</head>

<body onload="timedRefresh()">
<div class="container">
	<div class="header">
		<h1 class="header">Overzicht <? if($_GET["viewalles"]){ echo "alle"; }else{ echo "actieve"; } ?>  camera`s</h1>
	</div>
		
	<div class="left">
		
<? include("includes/menu.php") ?>

	</div>
	<div class="content">
		<fieldset>
		
			<?
			foreach($camArr as $cam)
			{
				if($cam["child_name"] || $_GET["viewall"])
				{
			?>
			<div class="<? if($cam["blocked"] == 0){ echo "cam1"; }else{ echo "cam_lock";} ?>">
				<div class="overview_webcam_name">
				<?
				echo $cam["name"];
				
				$streamcount = 0;
				$camstr = 'cam'.$cam["cid"];
				if(isset($streams->$camstr)){
					$streamcount = count($streams->$camstr);
				}
				
				if($streamcount > 0)
					echo "<img class=\"overview_webcam_eyes\" src=\"/styles/".style_name."/img/eyes.gif\" alt=\"".$streamcount." kijker(s)\" />";
				
				if($cam["blocked"] == 0)
				{
					echo '<a href="?block='.$cam["cid"].'&viewalles='.$_GET["viewalles"].'"><img class="overview_webcam_lock" src="/styles/'.style_name.'/img/admin/lock.gif" alt="Op slot zetten"></a>';
				}else{
					echo '<a href="?unblock='.$cam["cid"].'&viewalles='.$_GET["viewalles"].'"><img class="overview_webcam_lock" src="/styles/'.style_name.'/img/admin/unlock.gif" alt="Slot verwijderen"></a>';
				}
				echo '<a href="viewcam.php?cid='.$cam["cid"].'"><img class="overview_webcam_enlarge" src="/styles/'.style_name.'/img/admin/enlarge.gif" alt="vergroot beeld"></a></div>';
				?>
				
				
				<SCRIPT LANGUAGE="JavaScript">
				// Set the BaseURL to the URL of your camera
				var BaseURL = "http://<? echo $cam["ip"]; ?>/";

				// DisplayWidth & DisplayHeight specifies the displayed width & height of the image.
				// You may change these numbers, the effect will be a stretched or a shrunk image
				var DisplayWidth = "176";
				var DisplayHeight = "144";

				// This is the path to the image generating file inside the camera itself
				var File = "axis-cgi/mjpg/video.cgi?resolution=QCIF&compression=70&clock=1";
				// No changes required below this point
				var output = "";
				if ((navigator.appName == "Microsoft Internet Explorer") &&
				   (navigator.platform != "MacPPC") && (navigator.platform != "Mac68k"))
				{
				  // If Internet Explorer under Windows then use ActiveX 
				  output  = '<OBJECT ID="Player" width='
				  output += DisplayWidth;
				  output += ' height=';
				  output += DisplayHeight;
				  output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
				  output += 'CODEBASE="';
				  output += BaseURL;
				  output += 'activex/AMC.cab#version=3,32,19,0">';
				  output += '<PARAM NAME="MediaURL" VALUE="';
				  output += BaseURL;
				  output += File + '">';
				  output += '<param name="MediaType" value="mjpeg-unicast">';
				  output += '<param name="ShowStatusBar" value="0">';
				  output += '<param name="ShowToolbar" value="0">';
				  output += '<param name="AutoStart" value="1">';
				  output += '<param name="StretchToFit" value="1">';
				  // Remove the '//' for the ptz settings below to use the code for click-in-image. 
				  output += '<param name="PTZControlURL" value="';
				  output += BaseURL;
				  output += '/axis-cgi/com/ptz.cgi?camera=1">';
				  output += '<param name="UIMode" value="ptz-absolute">'; // or "ptz-absolute"
				  output += '<BR><B>Axis Media Control</B><BR>';
				  output += 'The AXIS Media Control, which enables you ';
				  output += 'to view live image streams in Microsoft Internet';
				  output += ' Explorer, could not be registered on your computer.';
				  output += '<BR></OBJECT>';
				} else {
				  // If not IE for Windows use the browser itself to display
				  var output = "";
				  var AppletDir = BaseURL + "java/ama";
				  var VideoPath = "axis-cgi/mjpg/video.cgi?resolution=QCIF&compression=70";
				  var restOfPath = "";
				  var VideoURL = BaseURL + VideoPath;
				
				  output  = '<APPLET archive="ama.jar" codeBase="';
				  output += AppletDir + '"';
				  output += ' code="ama.MediaApplet" height=';
				  output += DisplayHeight;
				  output += ' width=';
				  output += DisplayWidth;
				  output += '>';
				  output += '<PARAM NAME="code" VALUE="ama.MediaApplet">';
				  output += '<PARAM NAME="archive" VALUE="ama.jar">';
				  output += '<PARAM NAME="codebase" VALUE="';
				  output += AppletDir + '">';
				  output += '<PARAM NAME="ama_cgi-path" VALUE="axis-cgi">';
				  output += '<PARAM NAME="cache_archive" VALUE="ama.jar, ptz.jar">';
				  output += '<PARAM NAME="cache_version" VALUE="1.0.0.0, 1.2.0.0">';
				  output += '<PARAM NAME="ama_plugins" VALUE="ptz.PTZ">';
				  output += '<PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">';
				  output += '<PARAM NAME="ama_url" VALUE="';
				  output += VideoURL;
				  output += DisplayWidth + 'x' + DisplayHeight;
				  output += restOfPath +'">';
				  output += '</APPLET>'
				}
				document.write(output);
				</SCRIPT>
				<div class="overview_webcam_name">
				<?
				echo $cam["child_name"];
				echo "</div>";
				?>
			</div>
			<?
				}
			}
			?>
			<a href="javascript:location.reload()"><img class="reload" src="/styles/<? echo style_name; ?>/img/admin/reload.gif" alt="herladen" /></a>
		</fieldset>
	</div>
	<div class="footer">
		<?
			include("includes/footer.php")
		?>
	</div>
</div>

</body>

</html>
