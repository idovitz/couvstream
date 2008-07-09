<?php

include_once "../admin/includes/Cameras.php";

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
	<link rel="stylesheet" type="text/css" href="/styles/<? echo style_name; ?>/monitoring.css" />
	<title><? echo systemname; ?> Monitor</title>
	<script type="text/javascript">
	function timedRefresh()
	{
		var t=setTimeout("location.reload()", 300000)
	}
	</script>
</head>

<body onload="timedRefresh();">
<div class="container">
	<div class="header">
		<h1 class="header">Overzicht actieve camera`s</h1>
	</div>
		

	<div class="content">
		<fieldset>
		
			<?
			foreach($camArr as $cam)
			{
				if($cam["child_name"] || $_COOKIE["cid"] == "-2")
				{
			?>
			<div class="<? if($cam["blocked"] == 0){ echo "cam1"; }else{ echo "cam_lock";} ?>">
				<div class="overview_webcam_name">
				
				<?
				echo $cam["name"]."</div>";
				if($cam["blocked"] == 0)
				{
					echo '<a href="?block='.$cam["cid"].'"><img class="overview_webcam_lock" src="/styles/'.style_name.'/img/monitoring/lock.gif" alt="Stop">';
				}else{
					echo '<a href="?unblock='.$cam["cid"].'"><img class="overview_webcam_lock" src="/styles/'.style_name.'/img/monitoring/unlock.gif" alt="Stop">';
				}
				echo '<a href="viewcam.php?cid='.$cam["cid"].'"><img class="overview_webcam_enlarge" src="/styles/'.style_name.'/img/monitoring/enlarge.gif" alt="vergroot beeld"></a>';
				?>
				
				
				<SCRIPT LANGUAGE="JavaScript">
				// Set the BaseURL to the URL of your camera
				var BaseURL = "http://<? echo $cam["ip"]; ?>/";

				// DisplayWidth & DisplayHeight specifies the displayed width & height of the image.
				// You may change these numbers, the effect will be a stretched or a shrunk image
				var DisplayWidth = "176";
				var DisplayHeight = "144";

				// This is the path to the image generating file inside the camera itself
				var File = "axis-cgi/mjpg/video.cgi?resolution=QCIF&compression=70";
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
				  theDate = new Date();
				  output  = '<IMG SRC="';
				  output += BaseURL;
				  output += File;
				  output += '&dummy=' + theDate.getTime().toString(10);
				  output += '" HEIGHT="';
				  output += DisplayHeight;
				  output += '" WIDTH="';
				  output += DisplayWidth;
				  output += '" ALT="Camera Image">';
				}
				document.write(output);
				//document.Player.ToolbarConfiguration = "play,+snapshot,+fullscreen"
				//document.Player.UIMode = "MDConfig";
				//document.Player.MotionConfigURL = "/axis-cgi/operator/param.cgi?ImageSource=0"
				//document.Player.MotionDataURL = "/axis-cgi/motion/motiondata.cgi";
				</SCRIPT>
				<div class="overview_webcam_name">
				<?
				echo $cam["child_name"];
				if(count($streams[$cam["cid"]]))
					echo " - ".count($streams[$cam["cid"]])." kijker(s)";
				echo "</div>";
				?>
			</div>
			<?
				}
			}
			?>
			<a href="javascript:location.reload()"><img class="reload" src="/styles/<? echo style_name; ?>/img/monitoring/reload.gif" alt="herladen" /></a>
			<a href="../index.php?logout=1"><img class="reload" src="/styles/<? echo style_name; ?>/img/monitoring/logout.gif" alt="Uitloggen"></a>

		</fieldset>
	</div>
	<div class="footer">
		<?
			include("../admin/includes/footer.php");
		?>
	</div>
</div>

</body>

</html>
