<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once("../admin/includes/Cameras.php");
include_once("../includes/config.php");
session_start();

// array aanmaken
if(!isset($_SESSION["sessieImages"]))
{
	$_SESSION["sessieImages"] = array();
}

// Kijken welke functie er wordt aangeroepen
if($_GET['functie'] == "delete")
{
	echo delete();
}
elseif($_GET['functie'] == "maakFoto")
{
	echo maakFoto();
}
elseif($_GET['functie'] == "herladen")
{
	echo herladen();
}
elseif($_GET['functie'] == "logout")
{
	echo logout();
}

// functie om foto te maken
function maakFoto()	
{
	$cameras = new Cameras();
	$cam = $cameras->getCamera($_COOKIE["cid"]);
	
	// foto naam maken, bestaat uit een md5 van de session id en de user id
	$sid = $_COOKIE['sid'];
	$uid = $_COOKIE['uid'];
	$rand = rand();
	$md5 = $uid."".$sid;
	$hash = md5($md5."".$rand);
	$socialMediaServ = explode(',', social_media_services);
	
	$count = count($_SESSION["sessieImages"]);
	
	if($count < 4 && $cam["blocked"] != 1)
	{
		// ophalen jpg van camera
		$imageData = file_get_contents('http://'.$cam['ip'].'/axis-cgi/jpg/image.cgi?resolution=4CIF&compression=15&clock=1&date=1&text=1&textstring=IJsselland%20Ziekenhuis&dummy=1347438687695');
		
		// plaats van de foto defininieren en in variable zetten.
		$fn = "./tmp/img/".$hash.".jpg";
		$fnThumb = "./tmp/thumb/".$hash.".jpg";
		
		// wegschrijven van originele foto op server
		file_put_contents($fn,$imageData);
		
		// het maken van de thumbnail
		$size = 0.1969;
		header('Content-type: image/jpeg');
		list($width, $height) = getimagesize($fn);
		$modwidth = $width * $size;
		$modheight = $height * $size;
		$tn = imagecreatetruecolor($modwidth, $modheight);
		$thumb = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $thumb, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
		
		// wegschrijven van de thumbnail op server
		imagejpeg($tn, $fnThumb, 100);
		
		// array vullen
		array_push($_SESSION["sessieImages"], $hash);
		
		return json_encode(array(
				'foto' => $hash,
				'socialMedia' => social_media,
				'socialMediaServ' => $socialMediaServ,
				'alle fotos' => $_SESSION['sessieImages']
				));
	}
	else if($cam["blocked"] == 1)
	{
		return json_encode($errorar = array(
				'error' => true,
				'reason' => "blocked"
				));
	}
	else
	{
		return json_encode($errorar = array(
				'error' => true,
				'reason' => "limit"
				));
	}
}

// functie om foto te verwijderen
function delete()
{
	unlink("tmp/img/".$_GET['fotonaam'].".jpg");
	unlink("tmp/thumb/".$_GET['fotonaam'].".jpg");
	$key = array_search($_GET['fotonaam'], $_SESSION["sessieImages"]);
	unset($_SESSION["sessieImages"][$key]);
	$_SESSION['sessieImages'] = array_values($_SESSION['sessieImages']);
	return json_encode($_SESSION['sessieImages']);
}

// functie wordt uitgevoerd als de pagina herlaad
function herladen()
{
	$socialMediaServ = explode(',', social_media_services);
	return json_encode(array(
			'foto' => $_SESSION['sessieImages'],
			'socialMedia' => social_media,
			'socialMediaServ' => $socialMediaServ
			));
}

function logout()
{
	$path1 = "../streams/tmp/img";
	if ($handle1 = opendir($path1))
	{
		while (false !== ($file1 = readdir($handle1)))
		{
			if ((time()-filectime($path1.'/'.$file1)) >= 60*60*24)
			{
				if (preg_match('/\.jpg$/i', $file1))
				{
					unlink($path1.'/'.$file1);
				}
			}
		}
		closedir($handle);
	}
	$path2 = "../streams/tmp/thumb";
	if ($handle2 = opendir($path2))
	{
		while (false !== ($file2 = readdir($handle2)))
		{
			if ((time()-filectime($path2.'/'.$file2)) >= 60*60*24)
			{
				if (preg_match('/\.jpg$/i', $file2))
				{
					unlink($path2.'/'.$file2);
				}
			}
		}
		closedir($handle);
	}
}
?>