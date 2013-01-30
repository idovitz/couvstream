<?php 
	session_start();
	include_once($_SERVER["DOCUMENT_ROOT"]."/includes/config.php");
	require_once('twitteroauth/twitteroauth.php');
	
	$_SESSION['oauth_verifier'] = "";
	$_SESSION["oauth_verifier"] = $_REQUEST['oauth_verifier'];	
	$connection = new TwitterOAuth(twitter_consumer_key, twitter_consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	
	if($_SESSION['oauth_verifier'] != "")
	{
		$xml_access_token = $connection->getAccessToken(array('oauth_verifier' => $_SESSION['oauth_verifier']));
				
		$_SESSION['access_token'] = $xml_access_token;
		if($_SESSION["access_token"])
		{
			?>
			<!DOCTYPE script PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
				<link rel='stylesheet' type='text/css' href='/styles/<? echo style_name; ?>/video.css' />
				<script>
					setTimeout(function(){self.close();},5000);
				</script>
			</head>
			<body>
				<img class="closeimg" src="/styles/<? echo style_name; ?>/img/login.jpg"/>
				<div class="closetext">
					<h2>Even geduld alstublieft!</h2>
					We maken nu een connectie met Twitter!
				</div>
			</body>
			</html>
			<?php
		}
	}
	else
	{
		?>
		<script>
		self.close();
		</script>
		<?php
	}
	
?>

