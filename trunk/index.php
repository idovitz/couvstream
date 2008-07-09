<?php

include_once "includes/config.php";
include_once "includes/Auth.php";

# new auth object
$auth = new Auth($_POST["username"], $_POST["password"]);

$auth->checkNetwork();

# if not local redirect to https, else to http
$netBool = $auth->checkNetwork();
if($_SERVER["HTTPS"] != "on" && !$netBool) {
	header("Location: https://".$_SERVER["HTTP_HOST"]);
}elseif($_SERVER["HTTPS"] == "on" && $netBool){
	header("Location: http://".$_SERVER["HTTP_HOST"]);
}

if($_GET["logout"] == "1")
{
	$auth->logout();
}

if($auth->check() === true && !$_GET["groupblocked"])
{
	
	if(isset($_COOKIE["cid"]) && ($_COOKIE["cid"] === "-1" || $_COOKIE["cid"] === "-2")){
		$auth->registerAddress();
		header("Location: monitoring/");
	}elseif(isset($_COOKIE["cid"]) && $_COOKIE["cid"] === "0"){
		$auth->registerAddress();
		header("Location: admin/");
	}elseif(isset($_COOKIE["cid"])){
		header("Location: streams/speed.php");
	}else{
		header("Location: /");
	}
}elseif($auth->check() === false && $auth->errorTxt){
	$errBool = true;
	$class = ("error");
	$errorTxt = $auth->errorTxt;
}elseif($_GET["expired"]){
	$errBool = true;
	$class = ("error");
	$errorTxt = "Uw sessie is verlopen. Log opnieuw in.";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="nl"> 


<?

include("styles/".style_name."/includes/index.inc");

?>

</html>

