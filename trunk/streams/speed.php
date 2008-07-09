<?php

include_once("../admin/includes/Cameras.php");

if(isset($_POST["bitrate"]) && !$_GET["reset"])
{
	setcookie("bitrate", $_POST["bitrate"]);
	header("Location: video.php");
}

$cameras = new Cameras();
$cam = $cameras->getCamera($_COOKIE["cid"]);

function parseBitrates()
{
	$a = split("]", rtrim(ltrim(str_replace("\"", "", bitrates), "["), "]"));
	
	for($i=0; $i<count($a); $i++)
	{
		$a[$i] = split(",", rtrim(ltrim($a[$i], ", ["), "]"));
	}
	
	return $a;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="nl"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/styles/<? echo style_name; ?>/img/favicon.ico" rel="SHORTCUT ICON" />
	<title><? echo longname; ?></title>
	<link rel="stylesheet" type="text/css" href="/styles/<? echo style_name; ?>/speed.css" />
</head>

<body>

<?
if($cam["blocked"] == 0)
{
?>

<div class="container">
	<div class="header">
		<h1 class="header"><? echo longname; ?></h1>
	</div>
	<form method="POST" action="">
		<fieldset>
			<legend>Kies uw snelheid:</legend>
			<?
			$bitrates = parseBitrates();
			for($i=0; $i<count($bitrates); $i++){
				echo "<p class=\"topmargin\">
						<input onclick=\"submit()\" type=\"radio\" name=\"bitrate\" value=\"".$i."\" />
						<label for=\"name\">vanaf ".$bitrates[$i][1]." KB/s</label>
						<div class=\"speed_tekst\">".$bitrates[$i][4]."</div>
					</p>";
			}
			?>
		</fieldset>
	<div class="footer">
		<p class="copywrite">&copy; 2007 by IJsselland Ziekenhuis</p>
	</div>
	</form>
</div>

<?
}else{
?>

<div class="container">
	<div class="header">
		<h1 class="header"><? echo longname; ?></h1>
	</div>
		De camera is tijdelijk buiten gebruik!
	<div class="footer">
		<p class="copywrite">&copy; 2007 IJsselland Ziekenhuis</p>
	</div>
</div>

<?
}
?>

</body>
</html>
