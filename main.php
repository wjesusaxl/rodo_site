<?php

session_start();

$global_login_url = "login.php";
$global_logout_url = "logout.php";
$global_images_folder = "images/";

include ('clases/enc_dec.php');
include ('clases/general.php');
include ('clases/usuario.php');
include ('clases/centro.php');
include ('clases/opcion.php');
include ('clases/security.php');
include ("clases/anuncio.php");

/*$id_usuario = $usuario->id;
	
$cenBLO = new CentroBLO();
$opcBLO = new OpcionBLO();
	
if($id_centro > 0 && $id_usuario > 0)
{
	$centro =$cenBLO->RetornarXId($id_centro);		
}*/

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		
		<style type="text/css" >
			body { background-color: #F1F1F1; }
			
		</style>
		
		<script language="JavaScript" src="js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="js/jquery.cookie.js"></script>
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
			
		</script>
	</head>
	<body>
	<?php 
		include("header.php");		
	?>
	<div style=" height: 420px; padding-left:20px;" align="center";>
		<div style="vertical-align: middle; width: 560px; height: 420px; border: dotted 1px #3399FF;" align="center">
			<div style="width:540px; height: 410px; padding-top: 10px;">
				<img src="images/interior2.png" />
			</div>
		</div>			
	</div>
	
	
	<div style="width:100px; float: left;">
		
		
	</div>
	</body>
</html>