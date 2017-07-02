<?php

if(isset($_COOKIE['session_key']))
$session_key = $_COOKIE['session_key'];

if($session_key == '' || $session_key == 'XX')
	header("Location: $global_logout_url?mensaje=No%20ha%20Iniciado%20Sesion");

$inactive = 12000;
$inactive = 60;
$timeout = $_COOKIE['timeout'];

$session_life = time() - $_COOKIE['timeoutLOG'];

$msj_security = "Timeout: ".$_COOKIE['timeoutLOG']."\n";
$msj_security = $msj_security. "Session Life: $session_life\n";

if($session_life > $inactive)
{
	setcookie("timeoutLOG");
	header("Location: $global_logout_url?mensaje=Sesion%20Expirada[$session_life]");	
}

setcookie("timeoutLOG", time());

if(isset($_GET['op_original_key']))
	$op_original_key = $_GET['op_original_key'];
else
	$op_original_key = "";

if(isset($_GET['opcion_key']))
    $opcion_key = $_GET['opcion_key'];
else
    $opcion_key = "";

if(isset($_GET['usr_key']))
	$usr_key = $_GET['usr_key'];
else 
	$usr_key = "";

if(isset($_GET['id_centro']))
    $id_centro = $_GET['id_centro'];
else
    $id_centro = 0;
	
function ValidarUsuario($op_key, $usr_id)
{
	$opBLO = new OpcionBLO();	
	$permiso = $opBLO->ValidarOpcionXIdUsuario($op_key, $usr_id);
	
	if(!$permiso->isOK)
	{
		$mensaje = str_replace(" ", "%20", $permiso->mensaje);		
		header("Location: $global_logout_url?mensaje=$mensaje");
	}
}

if($usr_key != "")	
{	
	$usrBLO = new UsuarioBLO();
	
	$str = decrypt($usr_key, $session_key);
	$usuario = $usrBLO->RetornarUsuarioXLogin($str);
	if($usuario != NULL)
		ValidarUsuario($opcion_key, $usuario->id);
	else 
	{
		sleep(5);
		$usuario = $usrBLO->RetornarUsuarioXLogin($str);
		if($usuario == NULL)
		{
			sleep(5);
			$usuario = $usrBLO->RetornarUsuarioXLogin($str);
			if($usuario == NULL)
				header("Location: $global_logout_url?mensaje=Error%20de%20Verificacion%20de%20Usuario");
			else
				ValidarUsuario($opcion_key, $usuario->id);
		}
		else
			ValidarUsuario($opcion_key, $usuario->id);
	}	
}
else
	header("Location: $global_logout_url?mensaje=Acceso%20No%20Autorizado");
	//header("Location: $global_logout_url?mensaje=$usr_key");
	
?>	