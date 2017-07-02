<?php

session_start();

include ('clases/enc_dec.php');
include ('clases/general.php');
include ('clases/opcion.php');
include ('clases/usuario.php');


if(isset($_GET['usr_key']))
	$usr_key = $_GET['usr_key'];
else
	header("Location: logout.php?mensaje=Acceso%20No%20Autorizado");

if(isset($_GET['id_centro']))
	$id_centro = $_GET["id_centro"];
else
	$id_centro = 0;
    
if(isset($_SESSION["session_key"]))
    $session_key = $_SESSION["session_key"];

$login = decrypt($usr_key, $session_key);

if(isset($_GET['op_original_key']))
    $op_original_key = "&op_original_key=".$_GET['op_original_key'];
else 
    $op_original_key = "";
	
if(isset($_GET['reserva_key']))
    $reserva_key = "&reserva_key=".$_GET['reserva_key'];
else 
    $reserva_key = "";

if(isset($_GET['opcion_key']))
{
	$opcion_key =  $_GET['opcion_key'];
	
	$opcBLO = new OpcionBLO();
	$opcion = $opcBLO->RetornarOpcionXKey($opcion_key);
    
    $usrBLO = new UsuarioBLO();
    $usuario = $usrBLO->RetornarUsuarioXLogin($login);
    
    if($opcion != null)
	{
		$enlace = $opcion->enlace;
        
        $permiso = $opcBLO->ValidarOpcionXIdUsuario($opcion_key, $usuario->id, $id_centro);
        
		if($permiso->isOK)
		{
		    if(str_replace("?", "*", $enlace) == $enlace)
                $enlace = "Location: $enlace?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro".$op_original_key.$reserva_key;
            else 
                $enlace = "Location: $enlace&opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro".$op_original_key.$reserva_key;
			header($enlace);
		}
		else 		
			header("Location: logout.php?mensaje=".str_replace(" ", "%20", $permiso->mensaje));
		 
	}
	else
		header("Location: logout.php?mensaje=Opcion%20Desconocida");
}
else
		header("Location: $global_logout_url?mensaje=Acceso%20No%20Autorizado");

?>