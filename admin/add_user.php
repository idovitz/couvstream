<?php

include_once "includes/Cameras.php";
include_once "includes/Users.php";

$cameras = new Cameras();

if($_POST["name"]) $name = $_POST["name"];
if($_GET["name"]) $name = $_GET["name"];
if($_POST["cid"]) $cid = $_POST["cid"];
if($_GET["cid"]) $cid = $_GET["cid"];
if($_POST["duedate"]) $duedate = $_POST["duedate"];
if($_GET["duedate"]) $duedate = $_GET["duedate"];

# get values for editing
if($_GET["edit"] == 1)
{
	$users = new Users();
	$user = $users->getUser($_GET["uid"]);
	
	$name = $user["name"];
	$cid = $user["cid"];
}

# delete user
if($_GET["delete"])
{
	$users = new Users();
	$users->delUser($_GET["delete"]);
}

# save new user
if($name && $cid && $duedate && $_GET["edit"] < 1)
{
	$users = new Users();
	$addresult = $users->addUser($name, $cid, $duedate);
	if($addresult === true)
	{
		header("Location: index.php");
	}
	elseif($addresult === false)
	{
		$add = "uid";
	}
	elseif($addresult === "fout")
	{
		?>
		<script type="text/javascript" language="javascript">
			alert("Speciale karakters en nummers zijn niet toegestaan");
		</script>
		<?php 
	}	
	else
	{
		$add = "cid";
		$deluid = $addresult["uid"];
		$deluser = $users->getUser($deluid);
	}
}

if($_GET["edit"] == 2)
{
	$users = new Users();
	$editResult = $users->updateUser($name, $cid, $duedate);
	if($editResult === true)
	{
		header("Location: index.php");
	}else{
		$add = "cid";
		$deluid = $editResult["uid"];
		$deluser = $users->getUser($deluid);
	}
}

?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/styles/<? echo style_name; ?>/img/favicon.ico" rel="SHORTCUT ICON" />
	<link rel="stylesheet" type="text/css" href="/styles/<? echo style_name; ?>/admin.css" />
	<title><? echo systemname; ?> Beheer</title>
	<script type="text/javascript" language="javascript">
	<!--
	function validateForm()
	{
		with (document.add)
		{
			var alertMsg = "De volgende verplicht velden\nzijn niet ingevult:\n";
			if (name.value == "") alertMsg += "\nNaam kind";
			if (cid.options[cid.selectedIndex].value == "") alertMsg += "\nWebcam";
			if (alertMsg != "De volgende verplicht velden\nzijn niet ingevult:\n")
			{
				alert(alertMsg);
				return false;
			}else{
				return true;
			}
		}
	}
	
	function confirmRemove()
	{
		var r=confirm("<? echo $deluser["name"]; ?> is al toegewezen aan deze camera.\nKies voor OK als u <? echo $deluser["name"]; ?> wilt verwijderen.\n");
		if(r == true)
		{
			window.location="add_user.php?delete=<? echo $deluid."&name=".$name."&cid=".$cid."&duedate=".$duedate."&edit=".$_GET["edit"] ?>";
		}
	}

	-->
	</script>
</head>

<body <? if($add=="cid"){ echo 'onload="confirmRemove()"'; }elseif($add=="uid"){ echo 'onload="alert(\'De gebruikersnaam bestaat al.\nKies een andere naam en sla opnieuw op.\')"'; } ?>>
<div class="container">
	<div class="header">
		<h1 class="header">Kind toevoegen</h1>
	</div>
	<div class="left">
		
<?
	include("includes/menu.php")
?>
	</div>
	<div class="content">
	<form action="add_user.php<? if($_GET["edit"]) echo '?edit=2'; ?>" method="POST" name="add" onsubmit="return validateForm()">
			<fieldset>
				<p><label for="name">Naam kind: <? if($_GET["edit"]) echo $name; ?></label> <input<? if($_GET["edit"]) echo ' type="hidden" '; ?> type="text" maxlength="16" size="16" id="name" name="name" value="<? if($_GET["edit"]) echo $name; ?>" /></p>
				<p><label for="name">Beschikbaarheid: </label><input type="text" maxlength="2" size="2" id="duedate" name="duedate" value="7" /> dagen</p>
				<p><label for="e-mail">Webcam:  <select size="1" name="cid">
												<option value=""<? if(!$cid && $add == "cid") echo ' selected="selected"'; ?>>-Selecteer-</option>
												<?
												
												foreach($cameras->getCameras() as $camera)
												{
													$sel = "";
													if($cid && $cid == $camera["cid"] && $add != "cid")
													{
														$sel = ' selected="selected"';
													}
													echo '<option value="'.$camera["cid"].'"'.$sel.'>'.$camera["name"].'</option>';
												}
												
												?>
											</select></label></p>
				<p><input type="submit" value="Opslaan" /></p>
				
			</fieldset>
		</form><p class="enlarge_fieldset_adduser"></p>
	</div>
	<div class="footer">
		<?
			include("includes/footer.php")
		?>
	</div>
</div>

</body>

</html>

