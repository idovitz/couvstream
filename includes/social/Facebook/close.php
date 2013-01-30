<?php 
require 'facebook-platform/php/facebook.php';
include_once($_SERVER["DOCUMENT_ROOT"]."/includes/config.php");

$facebook = new Facebook(array(
		'appId'  => facebook_appid,
		'secret' => facebook_secret,
		'cookie' => true,
));

$access_token = $facebook->getAccessToken();
$user = $facebook->getUser();
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
		We maken nu een connectie met Facebook!
	</div>
</body>
</html>