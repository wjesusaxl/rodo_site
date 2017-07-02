<?php



if(isset($_SESSION['session_key']))
	$session_key = $_SESSION['session_key'];

if($session_key == '' || $session_key == 'XX')
	header("Location: $global_logout_url?mensaje=No%20ha%20Iniciado%20Sesion");

$inactive = 12000;
$timeout = $_SESSION["rodo_timeout"];

//$session_life = time() - $_COOKIE['timeoutLOG'];
$session_life = time() - $timeout;

$msj_security = "Timeout: $timeout\n";
$msj_security = $msj_security. "Session Life: $session_life\n";

if($session_life > $inactive)
{
	//setcookie("timeoutLOG");
	unset($_SESSION["rodo_timeout"]);
	header("Location: $global_logout_url?mensaje=Sesion%20Expirada[$session_life]");
	//echo "Sesion Expirada";
}

$_SESSION["rodo_timeout"] = time();

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
	
function ValidarUsuario($op_key, $usr_id, $id_centro)
{
	$opBLO = new OpcionBLO();
	$permiso = $opBLO->ValidarOpcionXIdUsuario($op_key, $usr_id, $id_centro);
	
	if(!$permiso->isOK)
	{
		$mensaje = str_replace(" ", "%20", $permiso->mensaje);		
		header("Location: $global_logout_url?mensaje=ar$mensaje");
	}
}

if($usr_key != "")	
{

	$usrBLO = new UsuarioBLO();
	
	$str = decrypt($usr_key, $session_key);
	$usuario = $usrBLO->RetornarUsuarioXLogin($str);
	
	if(!is_null($usuario))
	{
		ValidarUsuario($opcion_key, $usuario->id, $id_centro);
		$id_usuario = $usuario->id;
	}
	else 
	{
		sleep(5);
		$usuario = $usrBLO->RetornarUsuarioXLogin($str);
		if(!is_null($usuario))
		{
			$id_usuario = $usuario->id;	
			ValidarUsuario($opcion_key, $usuario->id, $id_centro);
		}
		else		
		{
			sleep(5);
			$usuario = $usrBLO->RetornarUsuarioXLogin($str);
			if(is_null($usuario))
				header("Location: $global_logout_url?mensaje=Error%20de%20Verificacion%20de%20Usuario");
				//echo "Error</br>";
			else
				ValidarUsuario($opcion_key, $usuario->id, $id_centro);
		}
	}		
}
else
	
	header("Location: $global_logout_url?mensaje=$usr_key");
	
	
?>	