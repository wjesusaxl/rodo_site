<?php


include ('cuenta_venta.php');
include ('usuario.php');
include ('centro.php');

include('security.php');

$enlace = "registrar_ventas.php?key=$usr_key";

if(isset($_POST['tipo_busqueda_producto']))
	$tipo_busqueda_producto = $_POST['tipo_busqueda_producto'];
else 
	$tipo_busqueda_producto = 1;


if (isset($_GET['q'])) {
	$opcion = $_GET['q'];

	if ($opcion == 1) {
		$ventas = 'block';
		$reservas = 'none';
	} else {
		$ventas = 'block';
		$reservas = 'none';
	}
} else {
	$ventas = 'none';
	$reservas = 'none';
}

if(isset($_POST['nro_serie']))
	$nro_serie = $_POST['nro_serie'];
else
	$nro_serie = '';

if(isset($_POST['centro']))
	$centro = $_POST['centro'];
else
	$centro = 0;

if(isset($_POST['ubicacion']))
	$ubicacion = $_POST['ubicacion'];
else
	$ubicacion = 0;

if(isset($_POST['lugar_atencion']))
	$lugar_atencion = $_POST['lugar_atencion'];
else
	$lugar_atencion = 0;

if(isset($_GET['cta']))
	$cuenta = $_GET['cta'];
else
	if(isset($_POST['select_cuentas_abiertas']))
		$cuenta = $_POST['select_cuentas_abiertas'];
	else
		$cuenta = '';


if(isset($_GET['op']))
	$opcion = $_GET['op'];
else
{
	if(isset($_POST['operacion']))	
		$opcion = $_POST['operacion'];
	else
		$opcion = '';			
}


$cCtaVtaBLO = new CuentaVentaBLO();

if($opcion == 'crear_cuenta')
{	
	try
	{		
		$cCtaVta = new CuentaVenta();
		$cCtaVtaBLO = new CuentaVentaBLO(); 
		$cCtaVta->id_centro = $centro;
		$cCtaVta->id_usuario_creacion = $cUsr->id;
		$cCtaVta->id_lugar_atencion = $lugar_atencion;
		$cuenta_nueva = $cCtaVtaBLO->Registrar($cCtaVta);
		
		header("Location: $enlace&op=mostrar_cuenta&cta=$cuenta_nueva");
		
	}
	catch(Exception $e)
	{
		$error = $e->getMessage();
	}
}



if($opcion == 'cerrar_cuenta')
{
	$cCtaVtaBLO->CerrarCuenta($cuenta, $cUsr->id);	
	header("Location: $enlace");
}

if($opcion == 'cancelar_cuenta')
{
	$cCtaVtaBLO->CancelarCuenta($cuenta, $cUsr->id);
	header("Location: $enlace");
}

if(isset($_POST['nuevo_producto']))
	$nuevo_producto =$_POST['nuevo_producto'];
else
	$nuevo_producto = 0;
	
# Nuevo Item

if(isset($_POST['nuevo_categoria_producto']))
	$nuevo_categoria_producto = $_POST['nuevo_categoria_producto'];
else 
	$nuevo_categoria_producto = 0;
	


if(isset($_POST['nuevo_producto_promocion']))
	$nuevo_producto_promocion =$_POST['nuevo_producto_promocion'];
else
	$nuevo_producto_promocion = 0;

if(isset($_POST['precio_final']))
	$precio_final = $_POST['precio_final'];
	
if(isset($_POST['nuevo_producto_cantidad']))
	$nuevo_producto_cantidad = $_POST['nuevo_producto_cantidad'];
else
	$nuevo_producto_cantidad = 0;

if(isset($_POST['item_x_eliminar']))
	$item_x_eliminar = $_POST['item_x_eliminar'];
else
	$item_x_eliminar = 0;

if($opcion == 'mostrar_cuenta')
{
		
	$cCtaVtaBLO = new CuentaVentaBLO();
	$cuentas = $cCtaVtaBLO->RetornarCuentaVentaXKey($cuenta);
	
	if(count($cuentas) == 1)
	{
		$centro = $cuentas[0]->id_centro;		
		$ubicacion = $cuentas[0]->id_centro_ubicacion;
		$lugar_atencion = $cuentas[0]->id_lugar_atencion;
	}
	
	if($tipo_busqueda_producto == 1)
	{
		if($nro_serie != '')
		{
			$cProBLO = new ProductoBLO();							
			$producto = $cProBLO->RetornarProductoXNroSerie($nro_serie);
			if($producto != null)
				$nuevo_producto = $producto->id;
			else 
				$nuevo_producto = 0;															
		}
		
		if($nuevo_producto > 0  && $producto == null)
		{
			$cProBLO = new ProductoBLO();
			$producto = $cProBLO->RetornarProducto($nuevo_producto);							
		}
		
		if($producto != null)
		{
			
			$precio = $cProBLO->RetornarPrecio($centro, $nuevo_producto, $nuevo_producto_promocion);
			$lista = split(',', $producto->opcion_cantidad);
					
			if($nuevo_producto_cantidad == 0)
			{				
				$i = 0;
				foreach($lista as $l)
				{											
					$x = split(':', $l);
					if($producto->id_cantidad_default == $i)
						$nuevo_producto_cantidad = $x[0];
					$i ++;
				}				
			}
			else
				$nuevo_producto_precio_bruto_str = "--";		
			
			if($precio != NULL)
			{
				$nuevo_producto_precio_bruto = $precio->precio_bruto_mn;
				
				if($tipo_busqueda_producto == 1)
				{
					if($cuenta != '' && $nuevo_producto > 0 && $nuevo_producto_cantidad > 0)
					{
						$cta_item = new CuentaVentaItem();
						
						
						$cuentas = $cCtaVtaBLO->RetornarCuentaVentaXKey($cuenta);
						
						
					
						if(count($cuentas) == 1)
						{
							$cta_item->id_cuenta_venta = $cuentas[0]->id;			
							$cta_item->id_producto = $nuevo_producto;		
							$cta_item->id_usuario = $cUsr->id;
							$cta_item->id_promocion = $nuevo_producto_promocion;
							$cta_item->cantidad = $nuevo_producto_cantidad;
							$cta_item->comentarios = '';
							$cta_item->flag_anulacion = 0;
							$cta_item->id_centro = $centro;
							
							$cCtaVtaBLO->RegistrarItem($cta_item);
						}
					
					}
					
				}
								
				$nuevo_producto_precio_bruto_str = "S/. ".number_format($nuevo_producto_precio_bruto, 2);
				$nuevo_producto_precio_bruto_total = $precio->precio_bruto_mn * $nuevo_producto_cantidad;
				$nuevo_producto_precio_bruto_total_str = "S/. ".number_format($nuevo_producto_precio_bruto_total, 2);
			}
			else
				$nuevo_producto_precio_bruto_str = "--";
			
		}	
	}
}



if($opcion == 'grabar_item')
{
	$query_text = 'Cuenta: '.$cuenta;
	
	if($cuenta != '' && $nuevo_producto > 0 && $nuevo_producto_cantidad > 0)
	{
		$cta_item = new CuentaVentaItem();
		
		$cuentas = $cCtaVtaBLO->RetornarCuentaVentaXKey($cuenta);
	
		if(count($cuentas) == 1)
		{
			$cta_item->id_cuenta_venta = $cuentas[0]->id;			
			$cta_item->id_producto = $nuevo_producto;		
			$cta_item->id_usuario = $cUsr->id;
			$cta_item->id_promocion = $nuevo_producto_promocion;
			$cta_item->cantidad = $nuevo_producto_cantidad;
			$cta_item->comentarios = '';
			$cta_item->flag_anulacion = 0;
			$cta_item->id_centro = $centro;
			
			$cCtaVtaBLO->RegistrarItem($cta_item);
			
			header("Location: $enlace&op=mostrar_cuenta&cta=$cuenta");
		}
	
	}	
}

if ($opcion == 'eliminar_item')
{
	if($item_x_eliminar > 0)
	{
		
		$cuentas = $cCtaVtaBLO->RetornarCuentaVentaXKey($cuenta);
		
		if(count($cuentas > 0))
		{
			$cCtaVtaBLO->EliminarItem($cuentas[0]->id, $item_x_eliminar, $cUsr->id);
			header("Location: $enlace&op=mostrar_cuenta&cta=$cuenta");
			
		}
			
	}
	
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript">
		
			function IsNumeric(expression)
			{
			    return (String(expression).search(/^\d+$/) != -1);
			}

			
			function SeleccionarCentro()
			{				
				document.cuenta_venta.lugar_atencion.value = 0;
				document.cuenta_venta.ubicacion.value = 0;				
				document.cuenta_venta.submit();
			}
			
			function SeleccionarUbicacion()
			{
				document.cuenta_venta.lugar_atencion.value = 0;				
				document.cuenta_venta.submit();
			}
			
			function SeleccionarCuenta()
			{				
				var cta = document.cuenta_venta.select_cuentas_abiertas.value;
				if(cta != '')				
					window.location.href = "<?php echo $enlace; ?>&op=mostrar_cuenta&cta=" + cta;					
			}
			
			function CrearCuenta()
			{
				var flag_ok = true;
				if(document.cuenta_venta.centro.value == 0)
				{
					alert('No ha seleccionado Centro!');
					flag_ok = false;		
				}
				if(document.cuenta_venta.ubicacion.value == 0)
				{
					alert('No ha seleccionado Ubicacion Interna!');
					flag_ok = false;
				}
				if(document.cuenta_venta.lugar_atencion.value == 0)
				{
					alert('No ha seleccionado Lugar de Atencion!');
					flag_ok = false;
				}
				
				if(flag_ok)
				{
					var respuesta = confirm('Esta seguro de crear la cuenta?');
					if(respuesta)
					{
						document.cuenta_venta.operacion.value = 'crear_cuenta';
						document.cuenta_venta.submit();
					}	
				}
					
			}
			
			function CargarCuenta(id_centro, id_ubicacion, id_lugar_atencion)
			{
				document.cuenta_venta.centro.value = id_centro;
				document.cuenta_venta.ubicacion.value = id_ubicacion;
				document.cuenta_venta.lugar_atencion.value = lugar_atencion;				
			}				
			
			function Test()
			{
				
				alert('<?php echo $centro.'-'.$ubicacion.'-'.$lugar_atencion.'-'.$flag_crear_cuenta;?>');
			}
			
			function CargarDatos()
			{
				nro_serie = document.getElementById('nro_serie');
				if(nro_serie != null)
					nro_serie.focus();
					
				boton_agregar_cuenta_item = document.getElementById('boton_agregar_cuenta_item');
				if(boton_agregar_cuenta_item != null)
					if(document.cuenta_venta.nuevo_producto.value != 0)
						boton_agregar_cuenta_item.focus();
			}
			
			function ElegirNuevoProducto()
			{
				
				if(document.cuenta_venta.nuevo_producto.value != 0)
				{
					document.cuenta_venta.precio_final.value = 'original';
					document.cuenta_venta.operacion.value = 'mostrar_cuenta';					
					document.cuenta_venta.submit();
				}
				else
					alert('No ha elegido un producto valido!');

			}
			
			function BuscarNroSerie()
			{
				if(document.cuenta_venta.nro_serie.value != "")
				{
					document.cuenta_venta.precio_final.value = 'original';
					document.cuenta_venta.operacion.value = 'mostrar_cuenta';					
					document.cuenta_venta.submit();
				}
				else
					alert('No ha ingresado un Nro. de Serie valido!');
			}
			
			function ElegirNuevaCategoria()
			{
				if(document.cuenta_venta.nuevo_categoria_producto.value != 0)
				{
					document.cuenta_venta.operacion.value = 'mostrar_cuenta';					
					document.cuenta_venta.submit();				
				}
				else
					alert('No ha elegido un producto valido!');
			}
			
			function ElegirNuevoProductoPromocion()
			{
				document.cuenta_venta.operacion.value = 'mostrar_cuenta';				
				document.cuenta_venta.submit();				
			}
			
			function ElegirNuevaCantidad()
			{
				document.cuenta_venta.operacion.value = 'mostrar_cuenta';				
				document.cuenta_venta.nuevo_producto_cantidad.value;
				document.cuenta_venta.submit();
				
			}
			
			function AgregarCuentaItem()
			{
				var resultado = true;
				if(document.cuenta_venta.nuevo_producto.value == 0)
				{
					resultado = false;
					alert('No ha seleccionado un producto valido!');
				}
				
				if(!IsNumeric(document.cuenta_venta.nuevo_producto_cantidad.value))
				{
					resultado = false;
					alert('Ha ingresado un valor incorrecto para la cantidad!');
				}
				
				if(document.cuenta_venta.nuevo_producto_cantidad.value == 0)
				{
					resultado = false;
					alert('Cantidad no puede ser 0!');
				}
				
				if(resultado)
				{			
					document.cuenta_venta.operacion.value = 'grabar_item';
					document.cuenta_venta.submit();
				} 
				
			}
			
			function EliminarItem(item)
			{
				var respuesta = confirm('Esta seguro de eliminar el Item?');
					if(respuesta)
					{
						document.cuenta_venta.operacion.value = 'eliminar_item';
						document.cuenta_venta.item_x_eliminar.value = item; 
						document.cuenta_venta.submit();
					}
			}
			
			function SeleccionarOtraOpcion()
			{
				var otra_opcion = document.cuenta_venta.otra_opcion.value;				
				var op ;
				var msg;
								
				if(otra_opcion > 0)
				{
					if(otra_opcion == 1 || otra_opcion == 2)
					{
						if(otra_opcion == 1)
						{
							op = 'cerrar_cuenta';
							msg = 'Cerrar la Cuenta';		
						}
						if(otra_opcion == 2)
						{
							op = 'cancelar_cuenta';
							msg = 'Cancelar la Cuenta';
						}
						var respuesta = confirm('Esta seguro de '+msg+'?');
						if(respuesta)
						{
							document.cuenta_venta.operacion.value = op;
							document.cuenta_venta.submit();	
						}
						
					}
					if(otra_opcion == 3)
					{						
						window.location.href = "crear_producto.php?key=<?php echo $usr_key;?>";						
					}

				}
				
			}
			
			function CambiarTipoBusqueda()
			{				
				document.cuenta_venta.operacion.value = 'mostrar_cuenta';
				document.cuenta_venta.submit();	
			}
		</script>
	</head>
	<body onload="CargarDatos()">
		<?php 
		include("header.php");		
		?>
		<div id='cuenta' style="float: left; padding-top: 20px; width: 1000px; heigth: 100px; padding-left: 20px;" class="clase12">
			<form name="cuenta_venta" method="post" action="<?php echo $enlace; ?>">					
				<input name="precio_final" type="hidden" />
				<input name="operacion" type="hidden" />
				<input name="item_x_eliminar" type="hidden" />
				
				<table style="border: dotted 1px #3399FF; width:1000px; background-color: #E6F2FF;">
					<tr>
						<td><span class="clase12" style="font-weight: bold">Centro:</span></td>
						<td colspan="3">
							<select name="centro" style="width:100px;" class="clase12" onchange="SeleccionarCentro()">
								<option value=0>Seleccione...</option>
								<?php
									
								if($centro == 0)
									$selected = " selected='selected' ";
								else 
									$selected = '';
									
								$cCenBLO = new CentroBLO();
								$centros = $cCenBLO->Listar(); 
									
								foreach($centros as $c)
								{										
									if($c->id == $centro)
										$selected = " selected='selected' ";
									else 
										$selected = '';	
										
									echo "<option value=" . $c->id  . $selected . ">" . $c->descripcion . "</option>";
								}
								?>
							</select>
						</td>
															
					</tr>
						
					<tr>
						<td><span class="clase12" style="font-weight: bold">Ubicacion Interna:</span></td>
						<td colspan="3">
							<select name="ubicacion" style="width:200px;" class="clase12" onclick="SeleccionarUbicacion()">
								<option value=0>Seleccione...</option>
									<?php
									
									if($ubicacion == 0)
										$selected = " selected='selected' ";
									else 
										$selected = '';
									
									if($centro > 0)
									{
																															
										$ubicaciones = $cCenBLO->ListarUbicacionesXCentro($centro);											
										foreach ($ubicaciones as $ub)
										{ 
											if($ub->id == $ubicacion)
												$selected = " selected='selected' ";
											else
												$selected = '';
											echo "<option value=" . $ub->id . $selected . ">" . $ub->descripcion . "</option>";
										}
									}
									?>
							</select>
						</td>							
					</tr>
					<tr>
						<td><span class="clase12" style="font-weight: bold">Lugar Atencion:</span></td>
						<td colspan="3">
							<select name="lugar_atencion" style="width:200px;" class="clase12" onclick="submit()">
								<option value=0>Seleccione...</option>
								<?php
									
								if($lugar_atencion == 0)
									$selected = " selected='selected' ";
								else 
									$selected = '';
			
								if($centro > 0 && $ubicacion > 0)
								{
										
									$lugaresatencion = $cCenBLO->ListarLugaresAtencionXUbicacion($ubicacion);	
										
									foreach ($lugaresatencion as $la)
									{ 
										if($la->id == $lugar_atencion)
											$selected = " selected='selected' ";
										else
											$selected = '';
										echo "<option value=" . $la->id . $selected . ">" . $la->descripcion . "</option>";
									}
										
								}
								?>
							</select>
						</td>
					</tr>
						
						
					<tr>
						<td><span class="clase12" style="font-weight: bold;">Cuentas Abiertas:</span></td>
						<td colspan="3">
						<?php
								
						$cuentas = NULL;
						$nro_cuentas = 0;
									
						if($centro > 0)
						{
							$cuentas_habilitado = '';									
							$cuentas = $cCtaVtaBLO->RetornarCuentasVentaPendientesXLugarAtencion($centro, $ubicacion, $lugar_atencion);
						}

						if($cuentas != NULL)
						{
							$nro_cuentas = count($cuentas); 
							if($nro_cuentas > 0)										
								$cuentas_habilitado = '';									
							else 
								$cuentas_habilitado = 'disabled';
						}										
									
						?>
							<select name="select_cuentas_abiertas" style="width:500px;" class="clase12" onchange="SeleccionarCuenta()" <?php echo $cuentas_habilitado; ?>>
							<?php
							if($cuentas_habilitado == '')
							{
								if($nro_cuentas > 0)
								{
									echo "<option value=0>Se encuentra(n) $nro_cuentas cuenta(s) abierta(s). Seleccione... </option>";
									foreach($cuentas as $c)
									{
										if($c->auto_key == $cuenta)
											$selected = "selected = 'selected'";
										else 
											$selected = '';
										echo "<option value='$c->auto_key' $selected>[".str_pad($c->id, 5, '0', STR_PAD_LEFT)."]: $c->lugar_atencion_codigo - $c->cliente ($c->fecha_hora)</option>";
									}
								}
								else
									echo "<option value=0>No se encuentran cuentas abiertas...</option>";
							}
										
							?>
							</select>
						</td>
					</tr>
										
					<tr>
						<td colspan="4" align="center"> 
							<input type="button" name="boton_crear_cuenta" value="Crear Cuenta" class="clase12" onclick="CrearCuenta()" />
						</td>
					</tr>
				</table>
				<?php
				if($opcion == 'mostrar_cuenta')
				{
				$color = "#58D3F7";
						
				$cCtaVtaBLO = new CuentaVentaBLO();
				$cta_items = $cCtaVtaBLO->RetornarItemsXCuentaVentaKey($cuenta);
				?>
					
				<div style="float:left; width:1000px; border: dotted 1px #3399FF; background-color: #E6F2FF; margin-top: 10px;" align="left">									
					<table style="width:1000px;">
					<tr>
						<td colspan="8" height="45px">
							<span class="clase12">Para poder registrar una venta debe agregar un Item a la cuenta seleccionada. Antes seleccione el tipo de busqueda de producto: </span>							
							<select name="tipo_busqueda_producto" class="clase12" style="font-weight: bold;" onchange="CambiarTipoBusqueda()">
								<option value="1" <?php if($tipo_busqueda_producto == 1) echo "selected=selected";?>>Nro de Serie</option>
								<option value="2" <?php if($tipo_busqueda_producto == 2) echo "selected=selected";?>> Categoria de Producto</option>
							</select>
						</td>
					</tr>
						
					<?php
					if($tipo_busqueda_producto == 1)
					{?>
					<tr><td colspan="8"><hr></td></tr>
					<tr>							
						<td colspan="8" align="center">
							<span class="12" style="font-size:18px;">Nro de Serie:</span>
							<input id = "nro_serie" name="nro_serie" style="font-size:18px; text-align: center; width: 150px;" type="text" onchange="BuscarNroSerie()"/>
							<input id ="buscar_nro_serie" name="buscar_nro_serie" style="font-size:18px; text-align: center; width: 150px;" type="button" value="Buscar" onclick="BuscarNroSerie()"/>							
						</td>
					</tr>
					<tr><td colspan="8"><hr></td></tr>
						<?php
						if($nuevo_producto > 0)
						{?>
						<tr>
							<td colspan="8" align="center">
								
								<input name="nuevo_producto" type="hidden" value="<?php echo $nuevo_producto; ?>"/>
								<span class="clase12" style="font-size:20px; color: red;"><?php echo $producto->descripcion_corta." (".$producto->producto_categoria.")";?></span>																			
							</td>
		
						</tr>
						<tr>
							<td colspan="8" align="center">
								<span class="clase12" style="font-size:20px;">Cantidad:</span>
								<select class="clase12" name="nuevo_producto_cantidad" style="width:80px; font-size:20px;" align="center" onchange="ElegirNuevaCantidad()"/>
								<?php										
								$i = 0;
								echo "Cuenta Lista: ". count($lista);
								foreach($lista as $l)
								{											
									$x = split(':', $l);
									if($nuevo_producto_cantidad == $x[0])
										$selected = "selected = 'selected'";
									else
										$selected = "";
									if($i >= $producto->id_cantidad_default)	
										echo "<option value='$x[0]' $selected>$x[1]</option>";
									$i ++;
								}
								?>
								</select>
							</td>
						</tr>
						
						<tr>
							<td colspan="8" align="center">
								<select class="clase12" style="font-size: 20px;" name="nuevo_producto_promocion" onchange="ElegirNuevoProductoPromocion()">
								<?php
								if($nuevo_producto > 0)
								{
									
									$cProBLO = new ProductoBLO();
									$promociones = $cProBLO->RetornarPromocionesActivas($centro, $nuevo_producto);
									
									if(count($promociones) > 0)
									{
										echo "<option value='0'>Seleccione...</option>";
										foreach($promociones as $promo)
										{														
											$usr_promo = $cUsrBLO->RetornarPromocion($cUsr->id, $promo->id);														
											if($nuevo_producto_promocion == $promo->id)
												$selected = " selected = 'selected' ";
											else
												$selected = '';
											
											if($usr_promo != NULL)
											{
												if($usr_promo->flag_habilitado == 1)
													echo "<option value ='".$promo->id."'". $selected.">".$promo->codigo."</option>";
												else 
													echo "<option value ='".$promo->id."'". $selected." disabled='disabled'>".$promo->codigo."</option>";
											
											}
										}
									}
									else
										echo "<option value='0'>Sin promocion</option>";
											 
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="8" align="center">
								<span class="clase12" style="font-size:20px;">Total:</span>
								<span class="clase12" style="font-size:20px; color: #0099CC;"><?php echo $nuevo_producto_precio_bruto_total_str; ?></span>							
							</td>
						</tr>
						<tr>
							<td colspan="8" align="center">
								<!--input type="button" id = "boton_agregar_cuenta_item" name ="boton_agregar_cuenta_item" value="Agregar Item" style="font-size:20px;" class="clase12" onclick="AgregarCuentaItem()"/-->
							</td>
						</tr>
					<?php
						}?>
						<tr><td colspan="8"><hr></td></tr>
					<?php
					}											
					if($tipo_busqueda_producto == 2)
					{?>
						<tr>														
							<td colspan="2" align="center" width="300px"><span style="font-weight:bold" class="clase12">Categoria</span></td>
							<td align="center" width="300px"><span style="font-weight:bold" class="clase12">Producto</span></td>								
							<td align="center" width="70px"><span style="font-weight:bold" class="clase12">Cantidad</span></td>
							<td align="center" width="80px"><span style="font-weight:bold" class="clase12">Precio</span></td>
							<td align="center" width="80px"><span style="font-weight:bold" class="clase12">Total</span></td>
							<td colspan=2 align="center" width="150px"><span style="font-weight:bold" class="clase12">Promocion</span></td>
						</tr>
						<?php
						if($i % 2 == 0)
							$color = "#58D3F7";
						else
							$color = "#F2F5A9";
						?>
						<tr bgcolor="<?php echo $color;?>">		
							<td colspan = 2>
								<select class="clase12" name="nuevo_categoria_producto" onchange="ElegirNuevaCategoria()" style="width:250px;">
									<option value=0>Seleccione...</option>
									<?php
									$cProCatBLO = new ProductoCategoriaBLO();
									$prodcats = $cProCatBLO->Listar();  
									
									foreach ($prodcats as $cat) 
									{
										if($nuevo_categoria_producto == $cat->id)
											$selected = " selected='selected' ";
										else
											$selected = '';
										
										echo "<option value='".$cat->id."'".$selected.">".$cat->descripcion."</option>";
									}
																			
									?>											
								</select>
							</td>
							
							<td>
								<select class="clase12" name="nuevo_producto" onchange="ElegirNuevoProducto()" style="width:310px;">
									<option value=0>Seleccione...</option>
									<?php
									
									if($nuevo_categoria_producto > 0)
									{											
										$cProBLO = new ProductoBLO();
										$productos = $cProBLO->ListarXCategoria($nuevo_categoria_producto);
										 	
										if(count($productos) > 0)
										{
											foreach ($productos as $p) 
											{
												if($p->id == $nuevo_producto)
												{
													$selected = " selected='selected' ";
													$nuevo_producto_unidad_medida = $p->codigo_unidad_medida;	
												}
												else
														$selected = '';												
												echo "<option value='".$p->id."'".$selected.">".$p->descripcion_corta."</option>";

											}
										}
									}
									?>
								</select>	
							</td>
																
							<?php
							if($nuevo_producto > 0)
							{
								$producto = $cProBLO->RetornarProducto($nuevo_producto);										
								
								$precio = $cProBLO->RetornarPrecio($centro, $nuevo_producto, $nuevo_producto_promocion);
								if($precio != NULL)
								{
									$nuevo_producto_precio_bruto = $precio->precio_bruto_mn;
									$nuevo_producto_precio_bruto_str = "S/. ".number_format($nuevo_producto_precio_bruto, 2);
									$nuevo_producto_precio_bruto_total = $precio->precio_bruto_mn * $nuevo_producto_cantidad;
									$nuevo_producto_precio_bruto_total_str = "S/. ".number_format($nuevo_producto_precio_bruto_total, 2);
								}
								else
									$nuevo_producto_precio_bruto_str = "--";
								}
								?>								
								<td>
									<select class="clase12" name="nuevo_producto_cantidad" style="width:80px;" align="center" onchange="ElegirNuevaCantidad()"/>
									<?php										
									$lista = split(',', $producto->opcion_cantidad);
									foreach($lista as $l)
									{
										$x = split(':', $l);
										if($nuevo_producto_cantidad == $x[0])
											$selected = "selected = 'selected'";
										else
											$selected = "";							
										echo "<option value='$x[0]' $selected>$x[1]</option>";
									}
									?>
									</select>
								</td>
								<td>										
									<input type="text" name="nuevo_producto_precio_bruto" readonly="readonly" align="center" value="<? echo $nuevo_producto_precio_bruto_str; ?>" style="width:70px;"/>
								</td>
								<td>										
									<input type="text" name="nuevo_producto_precio_bruto_total" readonly="readonly" align="center" value="<? echo $nuevo_producto_precio_bruto_total_str; ?>" style="width:70px;"/>
								</td>
								<td colspan=3>
									<select name="nuevo_producto_promocion" onchange="ElegirNuevoProductoPromocion()">
									<?php
									if($nuevo_producto > 0)
									{
										$promociones = $cProBLO->RetornarPromocionesActivas($centro, $nuevo_producto);
										if(count($promociones) > 0)
										{
											echo "<option value='0'>Seleccione...</option>";
											foreach($promociones as $promo)
											{														
												$usr_promo = $cUsrBLO->RetornarPromocion($cUsr->id, $promo->id);														
												if($nuevo_producto_promocion == $promo->id)
													$selected = " selected = 'selected' ";
												else
													$selected = '';
												
												if($usr_promo != NULL)
												{
													if($usr_promo->flag_habilitado == 1)
														echo "<option value ='".$promo->id."'". $selected.">".$promo->codigo."</option>";
													else 
														echo "<option value ='".$promo->id."'". $selected." disabled='disabled'>".$promo->codigo."</option>";
												
												}
											}
										}
										else
											echo "<option value='0'>Sin promocion</option>";
												 
									}
									?>
									</select>
								</td>
							</tr>
						<?php
						}?>
							
							<tr>
								<td colspan=2><span style="font-weight: bold; font-size:11px;">Otras Opciones:    </span>
									<select class="clase12" style="width:160px;" name="otra_opcion" onchange="SeleccionarOtraOpcion()">
										<option value=0 disabled=disabled>Seleccione...</option>
										<option value=1>Cerrar cuenta</option>
										<option value=2>Cancelar cuenta</option>										
									</select>
								</td>
								<td colspan=7 align="right">
								<?php
								if($tipo_busqueda_producto == 2)
								{?>
									<input type="button" name ="boton_agregar_cuenta_item" value="Agregar Item" class="clase12" onclick="AgregarCuentaItem()"/>
								<?php
								}?>
								</td>								
							</tr>
							<?php
							$i = 1;
							$total_bruto_mn = 0.0;
							if(count($cta_items) > 0)
							{
							?>	
								<tr>
									<td align="center" width="20px"><span style="font-weight:bold" class="clase12">Id</span></td>
									<td align="center" width="300px"><span style="font-weight:bold" class="clase12">Categoria</span></td>
									<td align="center" width="300px"><span style="font-weight:bold" class="clase12">Producto</span></td>								
									<td align="center" width="70px"><span style="font-weight:bold" class="clase12">Cantidad</span></td>								
									<td align="center" width="80px"><span style="font-weight:bold" class="clase12">Precio</span></td>
									<td align="center" width="80px"><span style="font-weight:bold" class="clase12">Total</span></td>
									<td colspan=2 align="center" width="150px"><span style="font-weight:bold" class="clase12">Promocion</span></td>
								</tr>
								<?
								foreach ($cta_items as $ci) 
								{
									if($i % 2 == 0)
										$color = "#58D3F7";
									else
										$color = "#F2F5A9";
								
									$a = $i % 1;	
									?>
									<tr bgcolor="<?php echo $color;?>">
								
									<?php
									$total_bruto_mn += $ci->precio_bruto_mn * $ci->cantidad;
									?>
										<td><span style="font-weight:bold; font-size:11px;"><?php echo $ci->id; ?></span></td>
										<td><input type="text" readonly="readonly" style="width:250px; font-size:11px; text-align:center;" value='<?php echo $ci->producto_categoria; ?>'/></td>
										<td><input type="text" readonly="readonly" style="width:310px; font-size:11px; text-align:center;" value='<?php echo $ci->producto_descripcion_corta; ?>'/></td>
										<td><input type="text" readonly="readonly" style="width:60px; font-size:11px; text-align:center;" value='<?php echo number_format($ci->cantidad,1); ?>'/></td>									
										<td><input type="text" readonly="readonly" style="width:70px; font-size:11px; text-align:center;" value='<?php echo "S/. ". number_format($ci->precio_bruto_mn, 2) ;?>'/></td>
										<td><input type="text" readonly="readonly" style="width:70px; font-size:11px; text-align:center;" value='<?php echo "S/. ". number_format($ci->precio_bruto_mn * $ci->cantidad, 2); ?>'/></td>									
										<td><input type="text" readonly="readonly" style="text-align:center; font-size:11px;" value='<?php echo $ci->promocion_codigo; ?>'/></td>
										<td><img src="images/delete.png" onmouseover="this.style.cursor='pointer';" alt="Eliminar Item" onclick="EliminarItem(<?php echo $ci->id; ?>)" /></td>
									</tr>
									<?php
									$i++;
								}
							}
							else
							{						
							?>
							<tr>
								<td colspan="8"><hr /></td>						
							</tr>						
							<tr>
								<td colspan="8"><span>No hay items en la cuenta</span></td>
							</tr>						
							<tr>
								<td colspan="8"><hr /></td>
							</tr>
							<?
							}
							if($i % 2 == 0) 
								$color = "#58D3F7";
							else
								$color = "#F2F5A9";							
							?>
							<tr bgcolor="<?php echo $color; $i++;?>">
								<td colspan=4>
								<td align=right><span style="font-size:12px; font-weight: bold">Total:</span></td>								
								<td colspan=3>
									<input type="text" readonly="readonly" style="width:70px; font-weight:bold; font-size:11px; text-align:center;"value='<?php echo "S/. ". number_format($total_bruto_mn, 2); ?>'/></td>
								</td>								
							</tr>
							<?php
							}
							?>				
						</table>
					</div>
				</form>
		</div>		
	</body>
</html>