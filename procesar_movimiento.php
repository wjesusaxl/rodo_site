<?php

session_start();

date_default_timezone_set("America/Lima");

include("clases/movimiento.php");
include("clases/general.php");



$id_movimiento_motivo = RetornarPOSTGET("id_movimiento_motivo", 0);

$id_usuario_conf = RetornarPOSTGET("id_usuario_conf", 0);
$id_centro_conf = RetornarPOSTGET("id_centro_conf", 0);
$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$operacion = RetornarPOSTGET("operacion", "");
$id_centro = RetornarPOSTGET("id_centro", 0);
$motivo_habilitado = RetornarPOSTGET("motivo_habilitado", NULL);

if($operacion == "query_movimiento_motivo")
{
	$motivo = NULL;
	if($id_movimiento_motivo > 0)
	{
		$movBLO = new MovimientoBLO();
		$motivo = $movBLO->RetornarMovimientoMotivoXId($id_movimiento_motivo);
	}
	
	echo json_encode($motivo);
}

if($operacion == "asignar_permisos")
{
	if($id_usuario_conf > 0)
	{
		$movBLO = new MovimientoBLO();
		$lista_motivos = $movBLO->ListarMotivoTodos();
		
		foreach($lista_motivos as $m)
		{
			$mu = $movBLO->RetornarMotivoUsuarioXIdUsuarioIdMotivo($id_usuario_conf, $m->id, $id_centro);
			
			$mu_n = new MovimientoMotivoUsuario();
			$mu_n->id_movimiento_motivo = $m->id;
			$mu_n->id_usuario = $id_usuario_conf;
			$mu_n->id_centro = $id_centro;
			$mu_n->flag_habilitado = 0;
			
			if(!is_null($motivo_habilitado))
				foreach($motivo_habilitado as $mh)
					if($mh == $m->id)
						$mu_n->flag_habilitado = 1;
			
			if(!is_null($mu))
			{
				$mu_n->id = $mu->id;
				$movBLO->ModificarMotivoUsuario($mu_n);
			}
			else
			{
				if($mu_n->flag_habilitado == 1) 
					$movBLO->RegistrarMotivoUsuario($mu_n);
			}
		}
        
        ?>
        <script type="text/javascript">
            alert('Permisos Actualizados para el Usuario!');             
        </script>
        <?php
        Redireccionar($op_original_key, $usr_key, $id_centro);        
    }
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

function Redireccionar($opcion_key, $usr_key, $id_centro)
{
    echo "Redireccionando..";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
}

?>