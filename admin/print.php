<?php

include_once "includes/Users.php";

$users = new Users();
$user = $users->getUser($_GET["uid"]);

include_once("../styles/".style_name."/includes/print.inc");

?>
