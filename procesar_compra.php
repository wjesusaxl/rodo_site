<?php

session_start();

date_default_timezone_set("America/Lima");

include ("clases/compra.php");
include ("clases/comprobante_pago.php");
include ("clases/transaccion.php");
include ("clases/enc_dec.php");
include ("clases/general.php");
include ("clases/movimiento.php");
include ("clases/stock.php");
include ("clases/almacen.php");
include ("clases/caja.php");

$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$operacion = RetornarPOSTGET("operacion", "");

$id_usuario = RetornarPOSTGET("id_usuario", 0);
$id_caja = RetornarPOSTGET("id_caja", 0);
$id_proveedor = RetornarPOSTGET("id_proveedor", 0);
$id_centro = RetornarPOSTGET("id_centro", 0);
$id_compra = RetornarPOSTGET("id_compra", 0);
$id_compra_tipo = RetornarPOSTGET("id_compra_tipo", 0);
$id_comprobante_pago_tipo = RetornarPOSTGET("id_comprobante_pago_tipo", 0);
$id_tipo_documento = RetornarPOSTGET("id_tipo_documento", 0);
$nro_documento = RetornarPOSTGET("prov_nro_documento", "");
$nro_comprobante = RetornarPOSTGET("nro_comprobante", "");

$monto_percepcion_mn = RetornarPOSTGET("monto_percepcion_mn", 0);
$nro_items = RetornarPOSTGET("nro_items", 0);
$id_transaccion_motivo = RetornarPOSTGET("id_transaccion_motivo", 0);
$id_transaccion_grupo = RetornarPOSTGET("id_transaccion_grupo", 0);
$fecha = RetornarPOSTGET("fecha", 0);


/*if($operacion == "query_compra_tipo")
{
	$lista = NULL;
	
	if($id_compra_tipo > 0 && $id_usuario > 0)
	{
				
		$compraBLO = new CompraBLO($id_centro);
		$obj = $compraBLO->RetornarTipoXId($id_compra_tipo);
		
		$transBLO = new TransaccionBLO();
		$usuario_transacciones_grupo = $transBLO->ListarGruposTransaccionHabilitadosXIdUsuarioIdCentro($id_usuario, $id_centro);
		$permiso = FALSE;
		
		if(!is_null($obj))
		{
			if(!is_null($usuario_transacciones_grupo))
			{
				foreach($usuario_transacciones_grupo as $gt)
				{
					if($gt->id_transaccion_grupo == $obj->id_transaccion_grupo)
						$permiso = TRUE;
				}	
			}
			
			if($permiso)
			{
				$lista = array();
				$lista[] = $obj;
			}
				
		}
	}
	
	echo json_encode($lista);
}*/



if($operacion == "crear")
{
	if($id_proveedor > 0 && $nro_items > 0)
	{
		$compraBLO = new CompraBLO($id_centro);
		$compBLO = new ComprobantePagoBLO();
		$tranBLO = new TransaccionBLO();
		
		$monto_neto_mn = 0;
		$monto_impuesto_mn = 0;
		$monto_tota_mn = 0;
		
		$compra_items = array();
		
		for($i = 1; $i <= $nro_items; $i++)
		{
			$item = new CompraItem();
			
			$item->id_producto = $_POST["id_producto_$i"];
					
			$item->precio_total_unitario_mn = $_POST["precio_total_unitario_mn_$i"];
			$item->precio_neto_unitario_mn = round($item->precio_total_unitario_mn / 1.18, 2);			
			$item->impuesto_unitario_mn = round(($item->precio_total_unitario_mn -  $item->precio_neto_unitario_mn), 2);
			
			$item->cantidad = $_POST["cantidad_$i"];
			$item->flag_anulado = 0;
			
			$monto_neto_mn = $monto_neto_mn + $item->precio_neto_unitario_mn * $item->cantidad;
			$monto_impuesto_mn = $monto_impuesto_mn + $item->impuesto_unitario_mn * $item->cantidad;
			
			$compra_items[] = $item;
			
		}
		
		$monto_total_mn = $monto_neto_mn + $monto_impuesto_mn;
		
		$comprobante = new ComprobantePago();
		$comprobante->auto_key = random_string();
		$comprobante->id_centro = $id_centro;
		$comprobante->id_tipo_origen = 2;
		$comprobante->fecha_hora_registro = date('Y-m-d H:i:s');
		$comprobante->id_comprobante_pago_tipo = $id_comprobante_pago_tipo;
		$comprobante->nro_comprobante = $nro_comprobante;
		$comprobante->id_comp_pago_agente_tipo = 1;
		$comprobante->id_comp_pago_agente = $id_proveedor;
		$comprobante->id_tipo_documento = $id_tipo_documento;
		$comprobante->nro_documento = $nro_documento;
		$comprobante->monto_neto_mn = $monto_neto_mn;
		$comprobante->monto_impuesto_mn = $monto_impuesto_mn;
		$comprobante->monto_percepcion_mn = $monto_percepcion_mn;
		$comprobante->monto_total_mn = $monto_total_mn + $monto_percepcion_mn;
		$comprobante->flag_anulado = 0;
		$comprobante->flag_post = 0;
		
		$resultado = $compBLO->Registrar($comprobante);
		
		if($resultado->isOK)
		{
			$tran = new Transaccion();
			
			$tran->auto_key = random_string();
			$tran->fecha_hora_registro = date('Y-m-d H:i:s');
			$tran->id_centro = $id_centro;
			$tran->id_usuario = $id_usuario;
			$tran->id_transaccion_grupo = $id_transaccion_grupo;
			$tran->id_caja = $id_caja;
			$tran->monto_neto_mn = $monto_neto_mn;
			$tran->monto_impuesto_mn = $monto_impuesto_mn;
			$tran->monto_otros_impuestos_mn = $monto_percepcion_mn;
			$tran->monto_total_mn = $monto_total_mn + $monto_percepcion_mn;
			$tran->flag_anulado = 0;
			$tran->flag_aprobado = 0;
			$tran->id_transaccion_motivo = 2; // Compra Normal
			
			$res_tran = $tranBLO->Registrar($tran);
			

			if($res_tran->isOK)
			{
				$compra = new Compra();
			
				$compra->id_centro = $id_centro;
				$compra->compra_key = random_string();
				$compra->fecha = $fecha;
				$compra->fecha_hora_registro = date('Y-m-d H:i:s');
				$compra->id_compra_tipo = $id_compra_tipo;
				
				$compra->id_usuario = $id_usuario;
				$compra->id_caja = $id_caja;
				$compra->id_comprobante_pago = $resultado->id;
				$compra->flag_anulada = 0;
				$compra->id_proveedor = $id_proveedor;
				$compra->id_transaccion = $res_tran->id;
				
				
				$res_compra = $compraBLO->Registrar($compra);
				
				if($res_compra->isOK)
				{
					foreach ($compra_items as $ci)
					{
						$ci->id_compra = $res_compra->id;
						$compraBLO->RegistrarItem($ci);
					}
				}
				
				$mensaje = $res_compra->mensaje;
				
			}
			else 
				$mensaje = $res_tran->mensaje;
			
			
		}
		else
			$mensaje = "Indices con Valor 0";
		
	}

	?>
	<script type="text/javascript">
		alert('<?php echo strtoupper($mensaje);?>');
	</script>	
					
	<?php
		Redireccionar($op_original_key, $usr_key, $id_centro);
}

//echo "ID Compra: $id_compra</br>";

if($operacion == "ingresar_almacen")
{
	
	if($id_compra > 0)
	{
		//echo "ID Compra: $id_compra</br>";
		$movBLO = new MovimientoBLO();
		$comBLO = new CompraBLO();
		$stkBLO = new StockBLO();
		$almBLO = new AlmacenBLO();
		
		$compra = $comBLO->RetornarXId($id_compra);
		
		if(!is_null($compra))
		{
			$mov = new Movimiento();
			
			$alm_destino = $almBLO->RetornarPrincipalXIdCentro($id_centro);
			
			if(!is_null($alm_destino))
			{
				$mov->id_centro = $id_centro;
				$mov->movimiento_key = random_string();
				$mov->fecha_hora = date('Y-m-d H:i:s');
				$mov->id_usuario = $id_usuario;
				$mov->id_motivo = 2;
				$mov->id_almacen_origen = NULL;
				$mov->id_almacen_destino = $alm_destino->id;
				$mov->comentarios = "COMPRA";
				
				$resultado = $movBLO->Registrar($mov);
				
				if($resultado->isOK)
				{
				
					$compra->id_movimiento = $resultado->id;
					
					$comBLO->Modificar($compra);
					$mov->id = $resultado->id;
					$movBLO->GenerarMovimientoCompra($compra->id);
					
					$stkBLO->ActualizarStock();
					
					?>
			        <script type="text/javascript">
			        	alert("<?php echo "Compra Ingresada en ".strtoupper($alm_destino->descripcion);?>")			                         
			        </script>
			        <?php
				}
			}
		
			
		
		}
		
		
	}

	Redireccionar($op_original_key, $usr_key, $id_centro);
	 
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