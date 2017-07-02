<?php

session_start();

date_default_timezone_set("America/Lima");

include("clases/turno_atencion.php");
include("clases/enc_dec.php");
include("clases/cuenta_venta.php");
include("clases/caja.php");
include("clases/transaccion.php");
//$fecha_hora_str = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($fecha_hora))));


$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_caja = RetornarPOSTGET("id_caja", 0);
$id_centro = RetornarPOSTGET("id_centro", 0);
$operacion = RetornarPOSTGET("operacion", "");
$id_usuario = RetornarPOSTGET("id_usuario", 0);
$monto_inicial_mn = RetornarPOSTGET("monto_inicial_mn", 0);
$id_almacen = RetornarPOSTGET("id_almacen", 0);
$id_turno = RetornarPOSTGET("id_turno", 0);
$id_caja = RetornarPOSTGET("id_caja", 0);
	
if($operacion == "crear")
{
	$taBLO = new TurnoAtencionBLO();
	$ta = new TurnoAtencion();
	
	$ta->auto_key = random_string();
	$ta->id_caja = $id_caja;
	$ta->id_usuario = $id_usuario;
	$ta->id_centro = $id_centro;
	$ta->id_estado = 1;
	$ta->id_almacen = $id_almacen;
	$ta->saldo_inicial_mn = $monto_inicial_mn;
	$ta->fecha_hora_inicio = date('Y-m-d H:i:s');
	
	$taBLO->Registrar($ta);
	
	Redireccionar($op_original_key, $usr_key, $id_centro);
}

if($operacion == "query_cuentas_monto_total")
{
	$turnos = NULL;
	
	if($id_turno > 0)
	{
		$turnos = array();
		
		$taBLO = new TurnoAtencionBLO();
		$turno = $taBLO->RetornarXId($id_turno);
		
		$turno->total_ingreso_efectivo_mn = $taBLO->RetornarMontoTotalCuentaVentaXIdTurnoAtencion($id_turno);
		
		$turnos[] = $turno;
		
		echo json_encode($turnos);
	}	
}

if($operacion == "cerrar")
{
	
	if($id_turno > 0)
	{
		$taBLO = new TurnoAtencionBLO();
		$traBLO = new TransaccionBLO();
		$ctaBLO = new CuentaVentaBLO();
		
		$ta = $taBLO->RetornarXId($id_turno);
		if(!is_null($ta))
		{
			$lista_cuentas_abiertas = $ctaBLO->ListarCuentasAbiertasXIdTurnoAtencion($ta->id);
			
			if(!is_null($lista_cuentas_abiertas))
			{
				if(count($lista_cuentas_abiertas) == 0)
				{
					$ta->fecha_hora_fin =  date('Y-m-d H:i:s');
			
					$monto_total_ventas_mn = $taBLO->RetornarMontoTotalCuentaVentaXIdTurnoAtencion($id_turno);
					
					$total_ingreso_efectivo_mn = 0;
					$total_egreso_efectivo_mn = 0;
					
					$lista_transacciones_extra = $traBLO->ListarXIdTurnoAtencion($ta->id);
					if(!is_null($lista_transacciones_extra))
						foreach($lista_transacciones_extra as $tx)
						{
							if($tx->transaccion_factor > 0)
								$total_ingreso_efectivo_mn += $tx->monto_total_mn;
							if($tx->transaccion_factor < 0)
								$total_egreso_efectivo_mn += $tx->monto_total_mn;
						}
					
					$ta->total_ingreso_efectivo_mn = is_null($monto_total_ventas_mn) ? 0 : $monto_total_ventas_mn;
					$ta->total_ingreso_efectivo_mn = $ta->total_ingreso_efectivo_mn + $total_ingreso_efectivo_mn;
					$ta->total_egreso_efectivo_mn = $total_egreso_efectivo_mn;
					$ta->total_transacciones_mn = $ta->saldo_inicial_mn + $ta->total_ingreso_efectivo_mn - $ta->total_egreso_efectivo_mn;
					
					$ta->id_estado = 2;
					
					$taBLO->Modificar($ta);
					
					$ta = $taBLO->RetornarXId($id_turno);
					
					$mensaje = "TURNO $ta->auto_key"." ".strtoupper($ta->estado).".";
				}
				else 
					$mensaje = "No se puede Cerrar el Turno. Existe(n) ".count($lista_cuentas_abiertas)." Cuenta(s) Abierta(s)";
			}
			else 
				$mensaje = "No se puede Cerrar el Turno. Error Buscando Cuentas Abiertas";
		}
		else
			$mensaje = "No se encuentra el Turno!";
	}
	
	?>
		<script type="text/javascript">
			alert("<?php echo $mensaje;?>");
		</script>            
	<?php
	
	Redireccionar($op_original_key, $usr_key, $id_centro);
}

if($operacion == "query_turnos_activos_usuario")
{
	$lista_turnos_activos = NULL;
	
	if($id_caja > 0 && $id_usuario > 0)
	{
		$taBLO = new TurnoAtencionBLO();
		$lista_turnos_activos = $taBLO->ListarTurnosActivosXIdUsuario($id_caja, $id_usuario);
		
		if(!is_null($lista_turnos_activos))
			if(count($lista_turnos_activos) == 0)
				$lista_turnos_activos = NULL;				
	}
	
	echo json_encode($lista_turnos_activos);
}

if($operacion == "query_turnos_activos")
{
	$lista_turnos_activos = NULL;
	
	if($id_caja > 0)
	{
		$taBLO = new TurnoAtencionBLO();
		$lista_turnos_activos = $taBLO->ListarTurnosActivos($id_caja);
		
		if(!is_null($lista_turnos_activos))
			if(count($lista_turnos_activos) == 0)
				$lista_turnos_activos = NULL;				
	}
	
	echo json_encode($lista_turnos_activos);
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
    echo "Redireccionando...";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
}
?>