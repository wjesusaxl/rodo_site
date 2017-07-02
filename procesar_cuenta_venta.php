<?php

session_start();

date_default_timezone_set("America/Lima");

include("clases/cuenta_venta.php");
include("clases/enc_dec.php");
include("clases/general.php");
include("clases/lugar_atencion.php");
include("clases/test.php");
include("clases/producto.php");
include("clases/stock.php");
include("clases/movimiento.php");
include("clases/turno_atencion.php");
include("clases/usuario.php");
include("clases/cliente.php");
include("clases/transaccion.php");
include("clases/caja.php");

if(isset($_SESSION['session_key']))
	$session_key = $_SESSION['session_key'];

$id_usuario = RetornarPOSTGET("id_usuario", 0);
$id_turno_atencion = RetornarPOSTGET("id_turno_atencion", 0);
$id_centro = RetornarPOSTGET("id_centro", 0);
$operacion = RetornarPOSTGET("operacion", "");
$id_lugar_atencion = RetornarPOSTGET("id_lugar_atencion", 0);

$id_caja = RetornarPOSTGET("id_caja", 0);
$id_cuenta_venta = RetornarPOSTGET("id_cuenta_venta", 0);
$id_cliente = RetornarPOSTGET("id_cliente", 0);
$id_producto = RetornarPOSTGET("id_producto", 0);
$id_centro_ubicacion = RetornarPOSTGET("id_centro_ubicacion", 0);
$cantidad = RetornarPOSTGET("cantidad", 0);
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_almacen = RetornarPOSTGET("id_almacen", 0);

$id_cuenta_venta_item = RetornarPOSTGET("id_cuenta_venta_item", 0);
$cantidad = RetornarPOSTGET("cantidad", "");  //VALIDAR QUE EN VERDAD SEA DIFERENTE DE '', EL VALOR 0 NO ES DEFAULT
$flag_anulado = RetornarPOSTGET("flag_anulado", ""); //VALIDAR QUE EN VERDAD SEA DIFERENTE DE '', EL VALOR 0 NO ES DEFAULT
$id_producto_precio = RetornarPOSTGET("id_producto_precio", 0);

$id_cuenta_venta_gral = RetornarPOSTGET("id_cuenta_venta_gral", 0);

if($operacion == "crear_cuenta_venta")
{
	
	$cvBLO = new CuentaVentaBLO();
	$movBLO = new MovimientoBLO();
	
	$cta = new CuentaVenta();
	
	$cta->auto_key = random_string();
	$cta->id_cliente = 1;
	$cta->estado = 1;
	$cta->fecha_hora = date('Y-m-d H:i:s');
	$cta->id_centro = $id_centro;
	$cta->id_lugar_atencion = $id_lugar_atencion;
	$cta->id_usuario_creacion = $id_usuario;
	$cta->id_turno_atencion = $id_turno_atencion;
	$cta->id_caja = $id_caja;
	$cta->id_centro_ubicacion = $id_centro_ubicacion;
	$cta->id_almacen = $id_almacen;
	
	$mov = new Movimiento();
	
	$mov->id_almacen_origen = $id_almacen;
	$mov->id_centro = $id_centro;
	$mov->movimiento_key = random_string();
	$mov->fecha_hora = date('Y-m-d H:i:s');
	$mov->id_usuario = $id_usuario;
	$mov->id_motivo = 3; // Salida Normal
	$mov->comentarios = "";
	
	$movBLO->Registrar($mov);
	
	$mov_n = $movBLO->RetornarXKey($mov->movimiento_key);
	
	if(!is_null($mov_n))
	{
		$cta->id_movimiento = $mov_n->id;
		$cvBLO->Registrar($cta);	
		$mensaje = "Cuenta Creada!";
	}
	else
		$mensaje = "No se pudo crear el Movimiento ni la Cuenta!";
		
	?>
      	<script type="text/javascript">
			alert('<?php echo $mensaje;?>');             
        </script>
	<?php
	Redireccionar($op_original_key, $usr_key, $id_centro); 
}

if($operacion == "actualizar_info_cuenta_venta")
{
	$lista = array();
	
	if($id_cuenta_venta > 0)
	{
		$cvBLO = new CuentaVentaBLO();
		$laBLO = new LugarAtencionBLO();
		$cliBLO = new ClienteBLO();
		
		$cv = $cvBLO->RetornarXId($id_cuenta_venta);
		
		if(!is_null($cv))
		{
			
			$cv->estado = 1;
			$la = $laBLO->RetornarXId($id_lugar_atencion);
			
			if($id_lugar_atencion > 0)
			{
				if(!is_null($la))			
					$cv->id_lugar_atencion = $id_lugar_atencion;
				else
				{
					$cv->estado = 0;
					$cv->comentarios = "No se encontro el Lugar de Atencion";	
				}	
			}
			
			
			if($id_cliente > 0)
			{
				$cli = $cliBLO->RetornarClienteXId($id_cliente);
				if(!is_null($cli))				
					$cv->id_cliente = $cli->id;
				else
				{
					$cv->estado = 0;
					$cv->comentarios = "No se encontro el Cliente";	
				}
					
			}
			
			if($cv->estado > 0)
			{
				$cvBLO->Modificar($cv);
			
				$cv_n = $cvBLO->RetornarXId($cv->id);
				$cv_n->estado = $cv_n->estado;
				$cv_n->comentarios = $cv_n->comentarios;
				
				$lista[] = $cv_n;	
			}
			else
				$lista[] = $cv;
			
		}
		else
		{
			$cv = new CuentaVenta();
			$cv->estado = 0;
			$cv->comentarios = "No se encontro la Cuenta";
			
			$lista[] = $cv;
		}
				
		
		
		echo json_encode($lista);
	}
}

if($operacion == "registrar_item")
{
	if($id_producto > 0)
	{
		$cvBLO = new CuentaVentaBLO();
		$pBLO = new ProductoBLO();
		$movBLO = new MovimientoBLO();
		$stkBLO = new StockBLO();
		$usrBLO = new UsuarioBLO();
		
		$cvd = new CuentaVentaItem();
		$cvd->fecha_hora = date('Y-m-d H:i:s');
		$cvd->estado = 1;
		
		//$str = decrypt($usr_key, $session_key);
		//$usuario = $usrBLO->RetornarUsuarioXLogin($str);
		
		//$cvd->id_usuario = $usuario->id;
		
		$cvd->id_usuario = $id_usuario;
		
		if($cvd->id_usuario > 0)
		{
			$cta = $cvBLO->RetornarXId($id_cuenta_venta);
		
			if(!is_null($cta))
			{
				$pro = $pBLO->RetornarProductoXId($id_producto);
				
				$cvd->id_cuenta_venta = $cta->id;
				
				if(!is_null($pro))
				{
					$px = $pBLO->RetornarPrecioXIdProductoIdCentroIdPrecioTipo($pro->id, $id_centro, 1);
					$stk = $stkBLO->RetornarStockXIdProductoIdAlmacen($pro->id, $cta->id_almacen);
					
					if($pro->id_producto_categoria > 0)
						$id_categoria_producto_general = $pro->id_producto_categoria;
					if($pro->id_producto_categoria2 > 0)
						$id_categoria_producto_general = $pro->id_producto_categoria2;
					if($pro->id_producto_categoria3 > 0)
						$id_categoria_producto_general = $pro->id_producto_categoria3;
					
					
					if(!is_null($px))
					{
						$cvd->id_producto = $pro->id;
						$cvd->descripcion_corta = strtoupper($pro->descripcion_corta);	
						$cvd->marca = strtoupper($pro->marca);
						$cvd->nro_serie = strtoupper($pro->nro_serie);
						$cvd->opcion_cantidad = $pro->opcion_cantidad;
						$cvd->cantidad_default = $pro->id_cantidad_default;
						
						$cvd->id_producto_precio = $px->id;
						$cvd->precio_neto_mn = $px->precio_neto_mn;
						$cvd->impuesto_mn = $px->impuesto_mn;
						$cvd->precio_total_mn = $px->precio_total_mn;
						
						$i = 0;
										
						$lista_precios = $pBLO->ListarPreciosXIdProducto($pro->id, $id_centro);
						
						$array_precios = "";
						
						foreach($lista_precios as $prec)
						{
							
							if($prec->codigo == "")
								$prec->codigo = strtoupper($prec->producto_precio_tipo);
							
							if($i == 0)
								$array_precios = "$prec->id,$prec->codigo,$prec->precio_total_mn";
							else								
								$array_precios = "$array_precios;$prec->id,$prec->codigo,$prec->precio_total_mn";							
							$i++;
						}
						
						$cvd->array_precios = $array_precios;
						
						if($id_categoria_producto_general) // Productos (NO Servicios)
						{
						
							if(!is_null($stk))
							{
								$mov_item_auto_key = random_string();
								$cvd_item_auto_key = random_string();
								
								$cvd->auto_key = $cvd_item_auto_key;
								$cvd->cod_precio = strtoupper($px->codigo);
								
								$cvd->flag_anulado = 0;
								
								$opcion_cantidad_arr = explode(",", $pro->opcion_cantidad);
								$cantidad_arr = explode(":", $opcion_cantidad_arr[$pro->id_cantidad_default]);
								$cvd->cantidad = $cantidad_arr[0];
								
								if($cvd->cantidad <= $stk->cantidad)
								{
									$mov_item = new MovimientoItem();
									$mov_item->id_movimiento = $cta->id_movimiento;
									$mov_item->id_producto = $pro->id;
									$mov_item->cantidad = $cvd->cantidad;
									$mov_item->auto_key = $mov_item_auto_key;
									$mov_item->flag_anulado = 0;
									
									$movBLO->RegistrarItem($mov_item);
									
									$mov_item_n = $movBLO->RetornarItemXKey($mov_item_auto_key);
									
									if(!is_null($mov_item_n))
									{
										
										$cvd->id_movimiento_detalle = $mov_item_n->id;
										$cvBLO->RegistrarItem($cvd);
										
										$cvd_n = $cvBLO->RetornarItemXItemKey($cvd->auto_key);
										$cvd_n->array_precios = $cvd->array_precios;
										$cvd = $cvd_n;
										
										$stkBLO->ActualizarStock();
									}
									else
									{
										$cvd = new CuentaVentaItem();
										$cvd->estado = 0;
										$cvd->comentarios = "No se pudo crear el Mov.Item!";
									}
										
								}
								else
								{
									$cvd = new CuentaVentaItem();
									$cvd->estado = 0;
									$cvd->comentarios = "Cantidad Excede el Stock de Almacen!";
								}
								
							}
							else
							{
								$cvd = new CuentaVentaItem();
								$cvd->estado = 0;
								$cvd->comentarios = "Stock No Encontrado!";
							}
							
						}
						
					}
					else
					{
						$cvd = new CuentaVentaItem();
						$cvd->estado = 0;
						$cvd->comentarios = "Producto sin Precio Normal!";
					}	
					
				}
				else
				{
					$cvd = new CuentaVentaItem();
					$cvd->estado = 0;
					$cvd->comentarios = "No se ha encontrado Producto!";
				}
				
			}
			else
			{
				$cvd = new CuentaVentaItem();
				$cvd->estado = 0;
				$cvd->comentarios = "No se ha encontrado Cuenta!";
			}
		}
		else
		{
			$cvd = new CuentaVentaItem();
			$cvd->estado = 0;
			$cvd->comentarios = "No se ha encontrado Cliente!";
		}
		
		$cvds = array();
		
		$cvds[] = $cvd;
		
		echo json_encode($cvds);
		
		
	}
}

if($operacion == "modificar_item")
{
	if($id_cuenta_venta_item > 0)
	{
				
		$cvBLO = new CuentaVentaBLO();		
		$proBLO = new ProductoBLO();
		$movBLO = new MovimientoBLO();		
		$stkBLO = new StockBLO();
		
		$cvi = $cvBLO->RetornarCuentaVentaItemXId($id_cuenta_venta_item);
		$cvi->estado = 1;
		$cvis = array();
		
		if(!is_null($cvi))
		{
			
			$mi = $movBLO->RetornarItemXId($cvi->id_movimiento_detalle);
			
			if(!is_null($mi))
			{
				if($cantidad != "")
				{
					$cvi->cantidad = $cantidad;
					$mi->cantidad = $cantidad;	
				}
				
				if($id_producto_precio > 0)
				{
					$px = $proBLO->RetornarPrecioXId($id_producto_precio);
					
					if(!is_null($px))
					{
						
						$cvi->id_producto_precio = $id_producto_precio;
						$cvi->precio_neto_mn = $px->precio_neto_mn;
						$cvi->impuesto_mn = $px->impuesto_mn;
						$cvi->precio_total_mn = $px->precio_total_mn;	
					}
					else
					{
						$cvi->estado = 0;
						$cvi->comentarios = "No se ha encontrado el Precio Seleccionado.";
					}
					
					$lista_precios = $proBLO->ListarPreciosXIdProducto($cvi->id_producto, $id_centro);
							
					$array_precios = "";
					
					foreach($lista_precios as $prec)
					{
						
						if($prec->codigo == "")
							$prec->codigo = strtoupper($prec->producto_precio_tipo);
								
						if($i == 0)
							$array_precios = "$prec->id,$prec->codigo,$prec->precio_total_mn";
						else								
							$array_precios = "$array_precios;$prec->id,$prec->codigo,$prec->precio_total_mn";							
						$i++;
					}
					
					$cvi->array_precios = $array_precios;
					
					
				}
				
			}
			
			if($flag_anulado != "")
			{
				$cvi->flag_anulado = $flag_anulado;
				$mi->flag_anulado = $flag_anulado;
				if($flag_anulado == 1)				
					$cvi->id_usuario_anulacion = $id_usuario;
				
			}
							
			$cvBLO->ModificarItem($cvi);
			$movBLO->ModificarItem($mi);
			
			$cvi_n = $cvBLO->RetornarCuentaVentaItemXId($cvi->id);
			
			if(!is_null($cvi_n))
			{
				$cvi_n->estado = $cvi->estado;
				$cvi_n->comentarios = $cvi->comentarios;
				$cvi_n->array_precios = $cvi->array_precios;
				
				$stkBLO->ActualizarStock();
				$cvis[] = $cvi_n;
			}
			else 
				$cvis[] = $cvi;
 			
		}
		else
		{
			$cvi = new CuentaVentaItem();
			$cvi->estado = 0;
			$cvi->comentarios = "No se ha encontrado el Item Seleccionado.";			
			
			$cvis[] = $cvi;
		}
		
		echo json_encode($cvis);
	}
	
}

if($operacion == "cerrar_cuenta" || $operacion == "cancelar_cuenta")
{
	if($id_cuenta_venta_gral > 0)
	{
		$ctaBLO = new CuentaVentaBLO();
		$taBLO = new TurnoAtencionBLO();		
		$traBLO = new TransaccionBLO();
		$stkBLO = new StockBLO();
		
		$cta = $ctaBLO->RetornarXId($id_cuenta_venta_gral);
		
		if(!is_null($cta))
		{
			if($operacion == "cerrar_cuenta")
			{
				$monto_neto_mn = 0;
				$impuesto_mn = 0;
				$monto_total_mn = 0;
				
				$lista_items = $ctaBLO->ListarItemsXIdCuentaVenta($id_cuenta_venta_gral);
				
				if(!is_null($lista_items))
				{
					foreach($lista_items as $i)
					{
						$i = new CuentaVentaItem();
						$monto_neto_mn = $monto_neto_mn + $i->precio_neto_mn * $i->cantidad;
						$impuesto_mn = $impuesto_mn + $i->impuesto_mn * $i->cantidad;
						$monto_total_mn = $monto_total_mn + $i->precio_total_mn * $i->cantidad; 
					}
					
				}
				
				$cta->total = $monto_total_mn;				
				
				$turno = $taBLO->RetornarXId($cta->id_turno_atencion);
				
				if(!is_null($turno))
				{
					$tra = new Transaccion();
					$tra->auto_key = random_string();
					$tra->id_centro = $id_centro;
					$tra->id_usuario = $id_usuario;
					$tra->id_transaccion_grupo = 1; // Venta Normal;
					$tra->id_transaccion_motivo = 1; // Venta Normal;
					$tra->id_caja = $turno->id_caja;
					//$tra->id_turno_atencion = $turno->id;
					$tra->monto_neto_mn = $monto_neto_mn;
					$tra->monto_impuesto_mn = $impuesto_mn;
					$tra->monto_total_mn = $monto_total_mn;
					$tra->monto_otros_impuestos_mn = 0;
					$tra->flag_anulado = 0;
					$tra->flag_aprobado = 1;
					$tra->fecha_hora_registro = date('Y-m-d H:i:s');
					
					//echo json_encode($tra)."</br>";
					
					$resultado = $traBLO->Registrar($tra);
					
					if($resultado->isOK)
					{
						$cta->id_transaccion = $resultado->id;
						$cta->estado = 2;
						$cta->id_usuario_cierre = $id_usuario;
						$cta->fecha_hora_cierre = date('Y-m-d H:i:s');
						
						$ctaBLO->Modificar($cta);
						
						$cta = $ctaBLO->RetornarXId($id_cuenta_venta_gral);
						
						$mensaje = "TRANSACCION iD: $resultado->id CREADA. CUENTA ".strtoupper($cta->desc_estado);
					}
					else
						$mensaje = "NO se puedo crear la Transaccion. $resultado->mensaje";							
				}
							
			}
			
			if($operacion == "cancelar_cuenta")
			{
				$cta->estado = 3;
				$cta->id_usuario_cierre = $id_usuario;
				$cta->fecha_hora_cierre = date('Y-m-d H:i:s');					
				
				$ctaBLO->Modificar($cta);				
				$ctaBLO->CancelarCuenta($id_cuenta_venta_gral);
				
				$stkBLO->ActualizarStock();
				
				$mensaje = "CUENTA $cta->desc_estado";
			}
				
		}
		?>
      	<script type="text/javascript">
			alert('<?php echo $mensaje;?>');             
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