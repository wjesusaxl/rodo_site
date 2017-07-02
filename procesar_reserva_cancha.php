<?php

session_start();

date_default_timezone_set("America/Lima");

include ('clases/general.php');    
include ('clases/reserva_cancha.php');
include ('clases/enc_dec.php');
include ("clases/usuario.php");
include ("clases/transaccion.php");
include ("clases/turno_atencion.php");
include ("clases/caja.php");

$enlace_original = "reservas_cancha/reservar.php";

$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_centro = RetornarPOSTGET("id_centro", 0);
$operacion = RetornarPOSTGET("operacion", "");
$id_cliente = RetornarPOSTGET("id_cliente", 0);
$id_usuario = RetornarPOSTGET("id_usuario", 0);
$id_reserva = RetornarPOSTGET("id_reserva", 0);
//$id_reservax = RetornarPOSTGET("id_reservax", 0);
$hora_inicio = RetornarPOSTGET("hora_inicio", "");
$fecha_fin = RetornarPOSTGET("fecha_fin", "");
$fecha_inicio = RetornarPOSTGET("fecha_inicio", "");
$fecha_hora_inicio = RetornarPOSTGET("fecha_hora_inicio", "");
$fecha_hora_fin = RetornarPOSTGET("fecha_hora_fin", "");
$hora_fin = RetornarPOSTGET("hora_fin", "");

$nro_reservas = RetornarPOSTGET("nro_reservas", 0);
$nro_dia_reserva = RetornarPOSTGET("nro_dia_reserva", 0);

$comentarios = RetornarPOSTGET("comentarios", "");
$pago_adelantado_mn = RetornarPOSTGET("pago_adelantado_mn", 0);
$pago_mn = RetornarPOSTGET("pago_mn", 0);
$id_turno_atencion = RetornarPOSTGET("id_turno_atencion", 0);
$id_caja = RetornarPOSTGET("id_caja", 0);

$enlace_original = "$enlace_original?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";

if($operacion == "crear" || $operacion == "modificar")
{
    if($id_cliente > 0)
	{
		$resBLO = new ReservaCanchaBLO();
		$traBLO = new TransaccionBLO();
		
		if($operacion == "modificar")
			$res_n = $resBLO->RetornarXId($id_reserva);
		
		if($operacion == "crear")
		{
			$res_n = new ReservaCancha();
			$res_n->estado = 1;
			$res_n->id_usuario_creacion = $id_usuario;
			$res_n->fecha_hora_registro = date('Y-m-d H:i:s');
			$res_n->fecha_hora_inicio = $fecha_hora_inicio;
			$res_n->fecha_hora_fin = $fecha_hora_fin;
			$res_n->id_centro = $id_centro;
			$res_n->pago_adelantado = $pago_adelantado_mn;
			$res_n->auto_key = random_string();
            
            
		}
			
		$res_n->id_cliente = $id_cliente;
		$res_n->comentarios = strtoupper($comentarios);
		$res_n->fecha_hora_inicio = $fecha_hora_inicio;
		$res_n->fecha_hora_fin = $fecha_hora_fin;
		
		if($operacion == "crear")		
		{
			if($pago_adelantado_mn > 0)
			{
				
				$monto_total_mn = $pago_adelantado_mn;
				$monto_neto_mn = round($pago_adelantado_mn / 1.18, 2);
				$monto_impuesto_mn = round($monto_total_mn - $monto_neto_mn, 2);  
					
				$tra = new Transaccion();
				$tra->auto_key = random_string();
				if($id_caja > 0)
					$tra->id_caja = $id_caja;
				else
					$tra->id_caja = 3; //Caja Central - De Local
				$tra->id_centro = $id_centro;
				$tra->id_usuario = $id_usuario;
				$tra->id_transaccion_motivo = 3;  // Ventas Normales;
				$tra->id_transaccion_grupo = 1; // Adelanto Alquiler Cancha;
				$tra->fecha_hora_registro = date('Y-m-d H:i:s');
				$tra->flag_anulado = 0;
				$tra->flag_aprobado = 0;
				$tra->id_turno_atencion = $id_turno_atencion;
				$tra->monto_neto_mn = $monto_neto_mn;
				$tra->monto_impuesto_mn = $monto_impuesto_mn;
				$tra->monto_otros_impuestos_mn = 0;
				$tra->monto_total_mn = $monto_total_mn;
				$tra->comentarios = strtoupper($comentarios);
		
				$resultado = $traBLO->Registrar($tra);
				
				/*echo "TRA</br>";
				echo json_encode($tra)."</br>";*/
				
				if($resultado->isOK)
				{
					$id_transaccion = $resultado->id;
					$resultado = $resBLO->Registrar($res_n);
					if($resultado->isOK)
					{
						$id_reserva = $resultado->id;
						
						$rt = new ReservaCanchaTransaccion();
						$rt->id_reserva_cancha = $id_reserva;
						$rt->id_transaccion = $id_transaccion;
						
						$resBLO->RegistrarReservaCanchaTransaccion($rt);
					}
				}
			}
			else
				$resultado = $resBLO->Registrar($res_n);
		}
		
		if($operacion == "modificar" && $id_reserva > 0)
			$resultado = $resBLO->Actualizar($res_n);
		
		
		
		?>
		<script type="text/javascript">
			alert('<?php echo $resultado->mensaje;?>');
		</script>	
		<?php
		Redireccionar($op_original_key, $usr_key, $id_centro);
	}
} 

if($operacion == "crear_frecuente" || $operacion == "modificar_frecuente")
{
	//echo "Id Cliente: $id_cliente</br>";
	if($id_cliente > 0)
	{
		$estado_cancelar = 5;
		$resBLO = new ReservaCanchaBLO();
		$usrBLO = new UsuarioBLO();
		
		$session_key = $_SESSION['session_key'];
		
		$str = decrypt($usr_key, $session_key);
		$usuario = $usrBLO->RetornarUsuarioXLogin($str);
		if(!is_null($usuario))
		{
			$usr = $usuario->login;
			$id_usuario = $usuario->id;
		}
		else
		{ 
			$usr = "";
			$id_usuario = 0;
		}
		
		//echo "Nro. Reservas: $nro_reservas</br>";
		
		for( $i = 1; $i <= $nro_reservas; $i++)
		{
			$op_res = RetornarPOSTGET("operacion_reserva_$i", "");
			
			if($op_res == "cancelar_reserva")
			{
				$id_reserva = RetornarPOSTGET("id_reserva_$i", 0);
				if($id_reserva > 0)
				{
					$rx = $resBLO->RetornarXId($id_reserva);
					if(!is_null($rx))
					{
						$rx->estado = $estado_cancelar;
						$rx->comentarios = $rx->comentarios." CANCELADO MASIVAMENTE POR $usr";
						$rx->id_usuario_creacion = $id_usuario;
						$resBLO->Actualizar($rx);
					}
				}
					//$resBLO->ActualizarEstado($id_reserva, $estado_cancelar, "CANCELADO MASIVAMENTE POR $usr");
			}
			
			//echo "Operacion: $op_res</br>";
			
			if($op_res == "crear_reserva")
			{
				$res_n = new ReservaCancha();
				
				$res_n->id_cliente = $id_cliente;
				$res_n->fecha_hora_registro = date('Y-m-d H:i:s');
				$res_n->fecha_hora_inicio = RetornarPOSTGET("fecha_hora_inicio_$i", "");
				$res_n->fecha_hora_fin = RetornarPOSTGET("fecha_hora_fin_$i", "");
				$res_n->comentarios =  RetornarPOSTGET("comentarios_$i", "");
				$res_n->estado = 6;
				$res_n->id_centro = $id_centro;
				$res_n->id_usuario_creacion = $id_usuario;
				$res_n->pago_adelantado = 0;
				
				$resBLO->Registrar($res_n);
			}
		}
		?>
        <script type="text/javascript">
            alert('Cambios Guardados!');             
        </script>
        <?php
		Redireccionar($op_original_key, $usr_key, $id_centro);
	}
	
}

if($operacion == "query_cliente")
{
	if($id_cliente > 0)
	{
		$resBLO = new ReservaCanchaBLO();
		$fecha_hora_inicio = $fecha_inicio." 00:00:00";
		$fecha_hora_fin = $fecha_fin." 23:59:59";
		$lista_reservas = $resBLO->ListarReservaActivaXFechaIniyFechaFinIdCliente($id_cliente, $id_centro, $fecha_hora_inicio, $fecha_hora_fin);
		echo json_encode($lista_reservas);
	}
}

if($operacion == "query_horario")
{
	$resBLO = new ReservaCanchaBLO();
	$lista_reservas = $resBLO->ListarReservaActivaXFechaIniyFechaFin($id_centro, $fecha_hora_inicio, $fecha_hora_fin);
	echo json_encode($lista_reservas);
	
}

if($operacion == "generar_horarios")
{
	
	$resBLO = new ReservaCanchaBLO();
	
	$fecha_inicio_str = $fecha_inicio." 00:00:00";
	$fecha_fin_str = $fecha_fin." 23:59:59";
	
	$fecha_inicio = strtotime(date('Y-m-d H:i:s', strtotime($fecha_inicio_str)));
	$fecha_fin = strtotime(date('Y-m-d H:i:s', strtotime($fecha_fin_str)));
	$nro_dia_fecha_inicio = date("w", $fecha_inicio);

	if($nro_dia_reserva >= $nro_dia_fecha_inicio)
		$dias = $nro_dia_reserva - $nro_dia_fecha_inicio;	
	else
		$dias = 7 - ($nro_dia_fecha_inicio - $nro_dia_reserva);

	$fecha_reserva = add_date($fecha_inicio, $dias, 0, 0);
	
	$lista = array();
	
	$fecha_hoy = strtotime(date("Y-m-d H:i:s"));
	
	while($fecha_reserva <= $fecha_fin)
	{
		$lista_activas = NULL;
		
		$fecha_reserva_inicio = strtotime(date('Y-m-d H:i:s', strtotime(date("Y-m-d", $fecha_reserva)." ".$hora_inicio)));
		$fecha_reserva_fin = strtotime(date('Y-m-d H:i:s', strtotime(date("Y-m-d", $fecha_reserva)." ".$hora_fin)));
		
		$fecha_reserva_inicio_db = date('Y-m-d H:i:s',"$fecha_reserva_inicio");
		$fecha_reserva_fin_db = date('Y-m-d H:i:s',"$fecha_reserva_fin");
		
		$fecha_reserva_inicio_str = date("h:i A.","$fecha_reserva_inicio");
		$fecha_reserva_fin_str = date("h:i A.","$fecha_reserva_fin");
		
		$lista_activas = $resBLO->ListarReservaActivaXFechaIniyFechaFin($id_centro, $fecha_reserva_inicio_db, $fecha_reserva_fin_db);
		
		$res_n = new ReservaCancha();
		
		$res_n->id_cliente = $id_cliente;
		$res_n->fecha_hora_inicio = $fecha_reserva_inicio_db;
		$res_n->fecha_hora_fin = $fecha_reserva_fin_db;
		$res_n->fecha_hora_inicio_str = $fecha_reserva_inicio_str;
		$res_n->fecha_hora_fin_str = $fecha_reserva_fin_str;
		
		
		if($fecha_reserva >= $fecha_hoy)
		{
			if(!is_null($lista_activas))
			{
				if(count($lista_activas) > 0)
				{
					$res_n->estado = 0;
					$res_n->comentarios = "EXISTE(N) ".count($lista_activas). " RESERVA(S) ACTIVA(S).";	
				}
				else
				{
					$res_n->estado = 1;
					$res_n->comentarios = "";
				}
			}
			else
			{
				$res_n->estado = 0;
					$res_n->comentarios = "ERROR EN LA CONSULTA.";
			}
			
		}
		else 
		{
			$res_n->estado = 0;
			$res_n->comentarios = "NO SE PUEDE MODIFICAR RESERVAS PASADAS";
		}
		
		/*echo json_encode ($lista_activas);
	
		echo "</br></br>";*/
		
		
		$fecha_reserva = add_date($fecha_reserva, 7, 0, 0);
		
		$lista[] = $res_n;
	}
	
	
				
	echo json_encode ($lista);			
				
}


if($operacion == "cancelar")
{
	if($id_reserva > 0)
	{
	    
		$resBLO = new ReservaCanchaBLO();
		
		$res = $resBLO->RetornarXId($id_reserva);
        
		if(!is_null($res))
		{
			$res->estado = 5; 
			$res->id_usuario_creacion = $id_usuario;
			$resultado = $resBLO->Actualizar($res);
		}
	}
	
	?>
	<script type="text/javascript">
		alert('<?php echo $resultado->mensaje;?>');
	</script>	
	<?php
	Redireccionar($op_original_key, $usr_key, $id_centro);
	
}

if($operacion == "ingresar_pago_cerrar")
{
	$rcBLO = new ReservaCanchaBLO();
	$traBLO = new TransaccionBLO();
	
	$resultado = new OperacionResultado();
	$resultado->id = 0;
	$resultado->isOK = FALSE;
	$resultado->codigo = "03";
	
	
	if($id_reserva > 0)
	{
		
		$res = $rcBLO->RetornarXId($id_reserva);
		
		if(!is_null($res))
		{
			$res->estado = 3;
			$res->id_usuario_creacion = $id_usuario;
				
			if($operacion == "ingresar_pago_cerrar")
			{
				$monto_total_mn = $pago_mn;
				$monto_neto_mn = round($pago_mn / 1.18, 2);
				$monto_impuesto_mn = round($monto_total_mn - $monto_neto_mn, 2);  
					
				$tra = new Transaccion();
				$tra->auto_key = random_string();
				$tra->id_caja = $id_caja;
				$tra->id_centro = $id_centro;
				$tra->id_usuario = $id_usuario;
				$tra->id_transaccion_motivo = 4;  // Ventas Normales;
				$tra->id_transaccion_grupo = 1; // Alquiler Cancha;
				$tra->fecha_hora_registro = date('Y-m-d H:i:s');
				$tra->flag_anulado = 0;
				$tra->flag_aprobado = 0;
				$tra->id_turno_atencion = $id_turno_atencion;
				$tra->monto_neto_mn = $monto_neto_mn;
				$tra->monto_impuesto_mn = $monto_impuesto_mn;
				$tra->monto_otros_impuestos_mn = 0;
				$tra->monto_total_mn = $monto_total_mn;
				$tra->comentarios = strtoupper($comentarios);
				
				$resultado = $traBLO->Registrar($tra);
				
				if($resultado->isOK)
				{
					$id_transaccion = $resultado->id;
					
					$rt = new ReservaCanchaTransaccion();
					$rt->id_reserva_cancha = $id_reserva;
					$rt->id_transaccion = $id_transaccion;
						
					$rcBLO->RegistrarReservaCanchaTransaccion($rt);
					
					$rcBLO->Actualizar($res);
				}
			}
			else
				$resultado->mensaje = "Reserva NO encontrara";
		}
		else
			$resultado->mensaje = "Id Reserva No Puede ser 0";
	}
	?>
	<script type="text/javascript">
		alert('<?php echo $resultado->mensaje;?>');
	</script>	
	<?php
	Redireccionar($op_original_key, $usr_key, $id_centro);
	
}

/*echo "Operacion: $cerrar</br>";
echo "Id Reserva: $id_reserva</br>";*/

if($operacion == "cerrar" || $operacion == "no_completada")
{
	$resultado = new OperacionResultado();
	$resultado->id = 0;
	$resultado->isOK = FALSE;
	$resultado->codigo = "03";
	$rcBLO = new ReservaCanchaBLO();
	
	if($id_reserva > 0)
	{
		$res = $rcBLO->RetornarXId($id_reserva);
		if(!is_null($res))
		{
			if($operacion == "cerrar")
				$res->estado = 3;
			if($operacion == "no_completada")
				$res->estado = 7;
			
			$res->id_usuario_creacion = $id_usuario;
			$resultado = $rcBLO->Actualizar($res);
		}
		else
			$resultado->mensaje = "Reserva NO encontrara";
	}
	else
		$resultado->mensaje = "Id Reserva No Puede ser 0";
	
	?>
	<script type="text/javascript">
		alert('<?php echo $resultado->mensaje;?>');
	</script>	
	<?php
	Redireccionar($op_original_key, $usr_key, $id_centro);
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

function add_date($givendate,$day=0,$mth=0,$yr=0) 
{
	$cd = $givendate;
	$newdate = mktime(date('h',$cd),
    	date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
    	date('d',$cd)+$day, date('Y',$cd)+$yr);
	return $newdate;
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