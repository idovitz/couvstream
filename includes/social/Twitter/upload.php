<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();

include_once($_SERVER["DOCUMENT_ROOT"]."/includes/config.php");

if ($_GET['functie'] == "checkUser")
{
	echo checkUser();
}
elseif ($_GET['functie'] == "getAccess")
{
	echo getAccess();
}
elseif ($_GET['functie'] == "twit-upload")
{
	echo twitUpload();
}
//Functie die een request token aanvraagd
function checkUser()
{
	require_once('twitteroauth/twitteroauth.php');
	
	$connection = new TwitterOAuth(twitter_consumer_key, twitter_consumer_secret);
	
	$request_token = $connection->getRequestToken(twitter_callback_url);

	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	// Als de request tokens niet leeg zijn, stuur dan de authorize link terug
	if($_SESSION['oauth_token'] != "" && $_SESSION['oauth_token_secret'] != "")
	{
		$connection = new TwitterOAuth(twitter_consumer_key, twitter_consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		
		$authorize = $connection->getAuthorizeURL($token);

		return(json_encode($twitcheck = array(
				'response' => $authorize,
				'fotonaam' => $_GET['fotonaam'],
				'oauth_token' => $_SESSION['oauth_token'],
				'oauth_token_secret' => $_SESSION['oauth_token_secret']
		)));
	}
	else
	{
		return(json_encode($twitcheck = array(
				'error' => "true",
				'fotonaam' => $_GET['fotonaam']
		)));
	}
}
//Functie die kijkt of de gebruiker de app heeft toegestaan, zo ja, dan worden er gegevens terug gestuurd voor de dialog.
function getAccess()
{
	require_once('twitteroauth/twitteroauth.php');
	
	$connection = new TwitterOAuth(twitter_consumer_key, twitter_consumer_secret, $_GET['oauth_token'], $_GET['oauth_token_secret']);
	//Kijken of de gebruiker al klaar is met inloggen.
	if($_SESSION['oauth_verifier'] != "")
	{
		$xml_access_token = $connection->getAccessToken(array('oauth_verifier' => $_SESSION['oauth_verifier']));

		$_SESSION['access_token'] = $xml_access_token;

		// Stukje script voor het debuggen, als er iets fout gaat, dit uncommenten.
 		/* foreach($xml_access_token as $key => $value)
		{
			print("|| ".$key." || => %% ".$value." %%\n");
		} */ 
		
		//Checken of we de oauth tokens hebben gekregen
		if (!empty($_SESSION['access_token']) &&
				!empty($_SESSION['access_token']['oauth_token']) &&
				!empty($_SESSION['access_token']['oauth_token_secret'])) 
		{			
			$connection = new TwitterOAuth(twitter_consumer_key, twitter_consumer_secret, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
			$verify = $connection->get('account/verify_credentials');
			
			//Checken of het verifieren goed is gegaan, 200 is goed
			if($connection->http_code == 200)
			{
				return(json_encode($twitcheck = array(
						'fotonaam' => $_GET['fotonaam'],
						'token' => $_SESSION['access_token']['oauth_token'],
						'secret' => $_SESSION['access_token']['oauth_token_secret'],
						'naam' => $_SESSION['access_token']['screen_name'],
						'error' => 'false'
					)));
			}
			else 
			{
				return(json_encode($twitcheck = array(
						'fotonaam' => $_GET['fotonaam'],
						'error' => 'true'
					)));
			}
		}
		else
		{
			return(json_encode($twitcheck = array(
				'fotonaam' => $_GET['fotonaam'],
				'error' => 'true',
				'access_token' => 'no',
				'oauth_token' => $_GET['oauth_token'],
				'oauth_token_secret' => $_GET['oauth_token_secret'],
				'oauth_verifier' => $_SESSION['oauth_verifier']
			)));
		}
	}
	else
	{
		return(json_encode($twitcheck = array(
				'fotonaam' => $_GET['fotonaam'],
				'error' => 'true',
				'oauth_token' => $_GET['oauth_token'],
				'oauth_token_secret' => $_GET['oauth_token_secret']
		)));
	}
}
//Functie die de foto plus tekst upload naar twitter
function twitUpload()
{
	//Checken of de gebruiker niet meer dan 100 tekens heeft gebruikt
	if(strlen($_GET['status']) <= 100)
	{
		require 'tmhoauth/tmhOAuth.php';
		require 'tmhoauth/tmhUtilities.php';
		
		$connection = new tmhOAuth(array(
		  'consumer_key' => twitter_consumer_key,
		  'consumer_secret' => twitter_consumer_secret,
		  'user_token' => $_GET['token'],
		  'user_secret' => $_GET['secret'],
		));
		
		$image = $_SERVER["DOCUMENT_ROOT"]."/streams/tmp/img/".$_GET['fotonaam'].".jpg";
		//Uploaden van de status
		$code = $connection->request(
				'POST',
				  'https://api.twitter.com/1.1/statuses/update_with_media.json',
				array(
						'media[]' => "@{$image};type=image/jpeg;filename={$image}",
						'status' => "".$_GET['status'].""
						),
						true, // use auth
						true // multipart
						);
		//Checken of het uploaden gelukt is
		if ($code == 200) 
		{
			return(json_encode($twitcheck = array(
					'status' => 'gelukt',
					'error' => 'false',
			)));
		} 
		else 
		{
			return(json_encode($twitcheck = array(
					'status' => 'niet gelukt',
					'error' => 'true',
					'token' => $_GET['token'],
					'secret' => $_GET['secret'],
					'fotonaam' => $_GET['fotonaam'],
					'http' => $code,
			)));
		}
	}
	else 
	{
		return(json_encode($twitcheck = array(
				'error' => 'true',
				'token' => $_GET['token'],
				'secret' => $_GET['secret'],
				'fotonaam' => $_GET['fotonaam'],
				'count' => 'yes',
		)));
	}
}
?>