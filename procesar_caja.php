<?php

session_start();

date_default_timezone_set("America/Lima");

include ('clases/opcion.php');
include ('clases/general.php');
include ('clases/enc_dec.php');
include ("clases/usuario.php");
include ('clases/caja.php');

$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$operacion = RetornarPOSTGET("operacion", "");
$id_usuario = RetornarPOSTGET("id_usuario", 0);
$id_caja = RetornarPOSTGET("id_caja", 0);
$id_centro = RetornarPOSTGET("id_centro", 0);
$id_usuario_conf = RetornarPOSTGET("id_usuario_conf", 0);
$caja_habilitado = RetornarPOSTGET("caja_habilitado", NULL);
$caja_responsable = RetornarPOSTGET("caja_responsable", NULL);
$caja_ingreso = RetornarPOSTGET("caja_ingreso", NULL);
$caja_salida = RetornarPOSTGET("caja_salida", NULL);

//echo "Usuario Conf: $id_usuario_conf</br>";

if($operacion == "asignar_permisos")
{
    if($id_usuario > 0)
    {
    	
        $caBLO = new CajaBLO();
                
        $lista_cajas = $caBLO->ListarCajaXIdCentro($id_centro);
		
		foreach($lista_cajas as $ca)
		{
			$cu = $caBLO->RetornarCajaUsuarioXIdCajaIdUsuario($ca->id, $id_usuario_conf);
			
			$cu_n = new CajaUsuario();
			$cu_n->id_caja = $ca->id;
			$cu_n->id_usuario = $id_usuario_conf;
			$cu_n->habilitado = 0;
			$cu_n->flag_responsable = 0;
			$cu_n->flag_ingreso = 0;
			$cu_n->flag_salida = 0;
			
			if(!is_null($caja_habilitado))
				foreach($caja_habilitado as $ch)
					if($ch == $ca->id)
						$cu_n->habilitado = 1;
			
			if(!is_null($caja_responsable))
				foreach($caja_responsable as $cr)
					if($cr == $ca->id)
						$cu_n->flag_responsable = 1;
			
			if(!is_null($caja_ingreso))
				foreach($caja_ingreso as $ce)
					if($ce == $ca->id)
						$cu_n->flag_ingreso = 1;
			
			if(!is_null($caja_salida))
				foreach($caja_salida as $cs)
					if($cs == $ca->id)
						$cu_n->flag_salida = 1;
			
			if(!is_null($cu))
			{
				$cu_n->id = $cu->id;
				$caBLO->ModificarCajaUsuario($cu_n);
			}
			else
			{
				if($cu_n->habilitado == 1 || $cu_n->flag_responsable == 1 || $cu_n->flag_ingreso == 1 || $cu_n->flag_salida == 1) 
					$caBLO->RegistrarCajaUsuario($cu_n);
			}
						
		}
        
        ?>
        <script type="text/javascript">
            alert('Permisos Actualizados para el Usuario!');             
        </script>
        <?php        
    }
    Redireccionar($op_original_key, $usr_key, $id_centro);
    
}

if($operacion == "query")
{
	if(isset($_GET['login']))
		$login = $_GET['login'];
	else 
		$login = "";
	
	if(isset($_GET['nombres']))
		$nombres = $_GET['nombres'];
	else 
		$nombres = "";
    
    if(isset($_GET['apellidos']))
        $apellidos = $_GET['apellidos'];
    else 
        $apellidos = "";
    
    if(isset($_GET['dni']))
        $dni = $_GET['dni'];
    else 
        $dni = "";
    
    $objBLO = new UsuarioBLO();
	$obj = new Usuario();
	
	if($login != "")
		$filtro .= " AND login like '%$login%'";
		
	if($nombres != "")
		$filtro .= " AND nombres like '%$nombres%'";
    
    if($apellidos != "")
        $filtro .= " AND apellidos like '%$apellidos%'";
    
    if($dni != "")
        $filtro .= " AND dni like '%$dni%'";
    
    if(strlen($filtro) > 0)
		$filtro = substr($filtro, 5);
	else
		$filtro = "";
    
    $lista = $objBLO->Listar($filtro);
	echo json_encode($lista);
}

function Redireccionar($opcion_key, $usr_key, $id_centro)
{
    echo "Redireccionando..";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
}

function RetornarPOSTGET($value, $default)
{
	if(isset($_GET[$value]))
		$q = $_GET[$value];
	else
		if(isset($_POST[$value]))
			$q = $_POST[$value];
		else
			$q = $default;
	
	return $q;
}

?>