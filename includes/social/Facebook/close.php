<?php 
require 'facebook-platform/php/facebook.php';

$facebook = new Facebook(array(
		'appId'  => '404853396236151',
		'secret' => '6ba9f4eda5ff7e8d3a74b430086e1648',
		'cookie' => true,
));

$access_token = $facebook->getAccessToken();
$user = $facebook->getUser();

echo "<h2>Even gedult alstublieft!</h2>";
echo "Dit scherm wordt automatisch gesloten.";
?>
<script type='text/javascript'>
setTimeout(function(){self.close()},1000);
</script>
