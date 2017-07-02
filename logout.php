<?php

if(isset($_GET['mensaje']))
	$mensaje = $_GET['mensaje'];
else
	$mensaje = ""; 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>.:RODO:.</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!--link rel="stylesheet" href="styles/main.css" type="text/css" /-->
		<!-- Date: 2012-07-06 -->
		
		<style media="screen" type="text/css">
			.logout_div { font-family: Helvetica; font-size: 12px; }
			.logout_titulo { font-family: Helvetica; font-size: 12px; color: red;}
			.logout_div_redirect { font-family: Helvetica; font-size: 12px; }
		</style>
	</head>
	<body>
		<div class="logout_div">
			<div class="logout_titulo" align="center">
				<?php echo $mensaje; ?>
			</div>
			<div class="logout_div_redirect">
				<a href="login.php" title="Ir a página de Acceso al Sistema" class="logout_redirect">Ir a Login</a>
			</div>	
		</div>
		
	</body>
</html>

