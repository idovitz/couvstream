<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once($_SERVER["DOCUMENT_ROOT"]."/includes/config.php");

// Kijken welke functie er wordt aangeroepen
if($_GET['functie'] == "checkUser")
{
	echo checkUser();
}
elseif($_GET['functie'] == "fbUploaden")
{
	echo fbUploaden();
}
elseif($_GET['functie'] == "checklogin")
{
	echo checklogin();
}

// Functie checkt of de user is ingelogd en de app heeft geautoriseerd
function checkUser()
{
	require_once 'facebook-platform/php/facebook.php';
	
	$facebook = new Facebook(array(
			'appId'  => facebook_appid,
			'secret' => facebook_secret,
			'cookie' => true,
	));
	
	$access_token = $facebook->getAccessToken();
	$user = $facebook->getUser();

	// checken of de user de app heeft toegestaan
	if ($user)
	{
		try {
			$user_profile = $facebook->api('/me');
		} catch (FacebookApiException $e) {
			error_log($e);
			$user = null;
		}
	}
	
	if($user != null && $access_token)
	{		
		$user_albums = $facebook->api('/me/albums');
	  	return json_encode($check = array(
	  		'loggedIn' => 'true',
	  		'albums'   => $user_albums,
	  		'access_token' => $access_token,
	  		'foto' => $_GET['fotonaam']
  		));
	} 
	else 
	{
		$params = array(
  			'scope' => 'publish_stream, user_photos',
			'redirect_uri' => facebook_callback_url,
		);
	  	$loginUrl = $facebook->getLoginUrl($params);
		return json_encode($check = array(
			'loggedIn' => 'false',
			'login' => $loginUrl,
			'fotonaam' => $_GET['fotonaam'],
			'user' => $user
		));
	}
}

// Checken of de user al is ingelogd, hierin zit niet de login url
function checklogin()
{
	require_once 'facebook-platform/php/facebook.php';
	
	$facebook = new Facebook(array(
			'appId'  => facebook_appid,
			'secret' => facebook_secret,
			'cookie' => true,
	));
	
	$access_token = $facebook->getAccessToken();
	$user = $facebook->getUser();
		
// checken of de user de app heeft toegestaan
	if ($user)
	{
		try {
			$user_profile = $facebook->api('/me');
		} catch (FacebookApiException $e) {
			error_log($e);
			$user = null;
		}
	}
	
	if($user != null && $access_token)
	{		
		$user_albums = $facebook->api('/me/albums');
	  	return json_encode($check = array(
	  		'loggedIn' => 'true',
	  		'albums'   => $user_albums,
	  		'access_token' => $access_token,
	  		'fotonaam' => $_GET['fotonaam']
  		));
	} 
	else 
	{
		return json_encode($checklog = array(
			'loggedIn' => 'false',
			'fotonaam' => $_GET['fotonaam'],
			'user' => $user
		));
	}
}

// functie die de foto upload naar facebook
function fbUploaden()
{	
	require_once 'facebook-platform/php/facebook.php';
	
	$facebook = new Facebook(array(
			'appId'  			=> facebook_appid,
			'secret' 			=> facebook_secret,
			'cookie'			=> true,
			'fileUpload'		=> true,
	));
	
	$access_token = $facebook->getAccessToken();
	
	if($_GET['fbAlbumSelect'] == "nieuwFbAlbum")
	{
		// Maak album aan
		$album_details = array(
				'name'=> $_GET['fbAlbumNaam']
		);
		$create_album = $facebook->api('/me/albums', 'post', $album_details);
		
		// Het album id van het album wat zojuist aangemaakt is
		$album_uid = $create_album['id'];
	}
	else if($_GET['fbAlbumSelect'] == "Selecteer een Facebook album")
	{
		return json_encode($albumerror = array(
				'geenalbum' => "ja"
				));
	}
	else 
	{
		$album_uid = $_GET['fbAlbumSelect'];
	}
	
	// Upload een foto naar het album
	$photo_details = array(
			'name'=> $_GET['opmerking'],
	);
	$photo_details['image'] = '@' . realpath($_SERVER["DOCUMENT_ROOT"]."/streams/tmp/img/".$_GET['fotonaam'].".jpg");
	
	$facebook->api('/'.$album_uid.'/photos', 'post', $photo_details, 'access_token=' .$access_token);
	
	return json_encode($fbUpLog = array(
			'uploaden' => 'true',
			'fotonaam' => $_GET['fotonaam']
	));
}

?>