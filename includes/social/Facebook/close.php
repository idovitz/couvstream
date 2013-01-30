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
<link rel='stylesheet' type='text/css' href='/styles/<? echo style_name; ?>/video.css' />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script>
	setTimeout(function(){self.close();},5000);
</script>
<body>
	<img class="closeimg" src="/styles/<? echo style_name; ?>/img/login.jpg"/>
	<div class="closetext">
	<h2>Even geduld alstublieft!</h2>
	We maken nu een connectie met Facebook!
	</div>
</body>
