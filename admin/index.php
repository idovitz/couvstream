<?php

include_once "../includes/config.php";
include_once "includes/Users.php";
include_once "includes/Cameras.php";

if($_GET["delete"])
{
	$users = new Users();
	$users->delUser($_GET["delete"]);
}

$cameras = new Cameras();
$streams = $cameras->countStreams();

/*echo "<pre>";
print_r($streams);
echo "</pre>";*/

?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/styles/<? echo style_name; ?>/img/favicon.ico" rel="SHORTCUT ICON" />
	<link rel="stylesheet" type="text/css" href="/styles/<? echo style_name; ?>/admin.css" />
	<title><? echo systemname; ?> Beheer</title>
	
	<script LANGUAGE="JavaScript">

	<!--
		function confirmSubmit() 
		{
			var agree=confirm("Weet u zeker dat u deze gebruiker wilt verwijderen");
			if (agree)
				return true ;
			else
			return false ;
		}
	// -->
	</script>

</head>

<body>



<div class="container">
	<div class="header">
		<h1 class="header"><? echo systemname; ?> Beheer</h1>
	</div>
	<div class="left">
		
<?
	include("includes/menu.php")
?>
	</div>
	<div class="content">
		<fieldset>
		<div class="recordset">
			<p class="header"></p>
			<p class="header">naam kind</p>
			<p class="header">camera</p>
			<p class="header">aanlegdatum</p>
			<p class="header">verloopdatum</p>
			<p class="header">aantal kijkers</p>
			<?
			$usrs = new Users();
			$i = 0;
			$style = array("even","odd");
			foreach($usrs->getUsers() as $user)
			{
				$streamcount = 0;
				$camstr = 'cam'.$user["cid"];
				
				if(isset($streams->$camstr)){
					$streamcount = count($streams->$camstr);
				}
				
				echo  '<p class="'.$style[$i%2].'"><a href="index.php?delete='.$user["uid"].'" onclick="return confirmSubmit()"><img src="/styles/'.style_name.'/img/admin/delete.gif" alt="verwijderen" hspace="2" /></a> <a href="add_user.php?edit=1&uid='.$user["uid"].'"><img src="/styles/'.style_name.'/img/admin/edit.gif" alt="bewerken" hspace="2" /></a> <a href="print.php?uid='.$user["uid"].'" target="_blank"><img src="/styles/'.style_name.'/img/admin/print.gif" alt="voorwaarden printen" hspace="2" /></a></p><p class="'.$style[$i%2].'">'.$user["name"].'</p><p class="'.$style[$i%2].'">'.$user["camname"].'</p><p class="'.$style[$i%2].'">'.$user["startdate"].'</p><p class="'.$style[$i%2].'">'.$user["expiredate"].'</p><p class="'.$style[$i%2].'">'.$streamcount.'</p>';
				$i++;
			}
			?>
		</div>
		</fieldset>
		<br /><br /><br /><br /><br /><br /><br /><br />
	</div>
	<div class="footer">
		<?
			include("includes/footer.php")
		?>
	</div>
</div>

</body>

</html>

