<?php 
	session_start();
	$_SESSION['oauth_verifier'] = "";
	$_SESSION["oauth_verifier"] = $_REQUEST['oauth_verifier'];	
	if($_SESSION["oauth_verifier"])
	{
		echo "<h2>Even gedult alstublieft!</h2>";
		echo "Dit scherm wordt automatisch gesloten.";
		?>
		<script>
		setTimeout(function(){self.close()},1000);
		</script>
		<?php
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

