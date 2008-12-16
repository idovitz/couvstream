<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="CouvStream" />
	<meta name="keywords" content="CouvStream" />
	<link href="styles/<? echo style_name; ?>/img/favicon.ico" rel="SHORTCUT ICON" />
	<title><? echo longname; ?></title>
	<link rel="stylesheet" type="text/css" href="styles/<? echo style_name; ?>/style.css" />
</head>

<body>
		<?
		if($errBool === true)
		{
			echo '<div class="'.$class.'"><p class="expired">'.$errorTxt.'</p></div>';
		}
		?>

	<div class="container">
		<form action="/" method="post">
			<fieldset>
			<legend>Inloggen <? echo systemname; ?></legend>
				<p class="topmargin"><label for="name">Naam:</label> <input type="text" id="name" name="username" /></p>
				<p class="topmarginPW"><label for="e-mail">Wachtwoord:</label> <input type="password" id="e-mail" name="password" /><br /></p>
				<p class="submit"><input type="submit" value="Inloggen" /></p>
			</fieldset>
			<div class="footer">
				<p class="copywrite">&copy; 2007 IJsselland Ziekenhuis</p>
			</div>	
			
		</form>
	</div>
</body>