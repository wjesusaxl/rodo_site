<?php

session_start();

date_default_timezone_set("America/Lima");

include ("clases/transaccion.php");
include ("clases/caja.php");
include ("clases/enc_dec.php");
include ("clases/general.php");
//include ("clases/test.php");

$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_centro = RetornarPOSTGET("id_centro", 0);
$id_usuario_conf = RetornarPOSTGET("id_usuario_conf", 0);
$usuario_transaccion_grupo_habilitado = RetornarPOSTGET("usuario_transaccion_grupo_habilitado", NULL);
$usuario_transaccion_motivo_habilitado = RetornarPOSTGET("usuario_transaccion_motivo_habilitado", NULL);
$id_transaccion_grupo = RetornarPOSTGET("id_transaccion_grupo", 0);
$operacion = RetornarPOSTGET("operacion", "");
$id_transaccion_motivo = RetornarPOSTGET("id_transaccion_motivo", 0);
$id_caja = RetornarPOSTGET("id_caja", 0);
$id_turno_atencion = RetornarPOSTGET("id_turno_atencion", NULL);
$monto_mn = RetornarPOSTGET("monto_mn", 0);
$comentarios = RetornarPOSTGET("comentarios", "");


if($operacion == "asignar_permisos")
{
	if($id_usuario_conf > 0)
	{
		$tranBLO = new TransaccionBLO();
		$lista_grupos = $tranBLO->ListarGrupoTransaccionTodos();
		
		foreach($lista_grupos as $tg)
		{
			$utg_n = new UsuarioGrupoTransaccion();	
			
			$utg_n->id_centro = $id_centro;
			$utg_n->id_usuario = $id_usuario_conf;
			$utg_n->id_transaccion_grupo = $tg->id;
			$utg_n->flag_habilitado = 0;
			
			$utg = $tranBLO->RetornarUsuarioGrupoTransaccionXIdUsuarioIdGrupoTransaccionIdCentro($id_usuario_conf, $tg->id, $id_centro);
			
			if(!is_null($usuario_transaccion_grupo_habilitado))			
				foreach($usuario_transaccion_grupo_habilitado as $utgh)
					if($utgh == $tg->id)
						$utg_n->flag_habilitado = 1;
			
			if(!is_null($utg))
			{
				$utg_n->id = $utg->id;
				$tranBLO->ModificarUsuarioGrupoTransaccion($utg_n);
			}
			else
			{
				if($utg_n->flag_habilitado == 1)
					$tranBLO->RegistrarUsuarioGrupoTransaccion($utg_n);
			}
		}
		
		//Redireccionar($opcion_key, $usr_key, $id_centro);
		
	}
}

if($operacion == "asignar_permisos_motivo_transaccion")
{
	if($id_usuario_conf > 0)
	{
		$tranBLO = new TransaccionBLO();
		$lista_motivos = $tranBLO->ListarMotivoTransaccionTodos();
		
		foreach($lista_motivos as $tm)
		{
			$utm_n = new UsuarioMotivoTransaccion();
			
			$utm_n->id_centro = $id_centro;
			$utm_n->id_usuario = $id_usuario_conf;
			$utm_n->id_transaccion_motivo = $tm->id;
			$utm_n->flag_habilitado = 0;
			
			$utm = $tranBLO->RetornarUsuarioMotivoXIdUsuarioIdUsuarioIdMotivo($id_usuario_conf, $tm->id, $id_centro);
			
			if(!is_null($usuario_transaccion_motivo_habilitado))			
				foreach($usuario_transaccion_motivo_habilitado as $utmh)
					if($utmh == $tm->id)
						$utm_n->flag_habilitado = 1;
					
			if(!is_null($utm))
			{
				$utm_n->id = $utm->id;
				$tranBLO->ModificarUsuarioMotivoTransaccion($utm_n);
			}
			else			
				if($utm_n->flag_habilitado == 1)				
					$tranBLO->RegistrarUsuarioMotivoTransaccion($utm_n);
					
		}
		
		//Redireccionar($opcion_key, $usr_key, $id_centro);
		
	}
}


if($operacion == "test")
{
	$tranBLO = new TransaccionBLO();
	
	$utm = $tranBLO->RetornarUsuarioMotivoXIdUsuarioIdUsuarioIdMotivo($id_usuario_conf, 1, 1);
}





if($operacion == "transaccionmotivoxtransacciongrupo")
{
	$lista_motivos = NULL;
	if($id_transaccion_grupo > 0)
	{
		
		$traBLO = new TransaccionBLO();
		$lista_motivos = $traBLO->ListarMotivosXIdTransaccionGrupo($id_transaccion_grupo);
					
	}
	
	echo json_encode($lista_motivos);
}

if($operacion == "crear")
{
	if($id_transaccion_motivo > 0)
	{
		$traBLO = new TransaccionBLO();
		$tra = new Transaccion();
		
		$tra->auto_key = random_string();
		$tra->id_caja = $id_caja;
		$tra->id_centro = $id_centro;
		$tra->id_usuario = $id_usuario;
		$tra->id_transaccion_motivo = $id_transaccion_motivo;
		$tra->id_transaccion_grupo = $id_transaccion_grupo;
		$tra->fecha_hora_registro = date('Y-m-d H:i:s');
		$tra->flag_anulado = 0;
		$tra->flag_aprobado = 0;
		$tra->id_turno_atencion = $id_turno_atencion;
		$tra->monto_neto_mn = $monto_mn;
		$tra->monto_impuesto_mn = 0;
		$tra->monto_otros_impuestos_mn = 0;
		$tra->monto_total_mn = $monto_mn;
		$tra->comentarios = strtoupper($comentarios);
		
		//echo json_encode($tra)."</br>";
	
		$resultado = $traBLO->Registrar($tra);
		
		?>
			<script type="text/javascript">
				alert("<?php echo strtoupper($resultado->mensaje);?>");
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