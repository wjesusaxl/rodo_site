<?php
include ('cuenta_venta.php');
include ('usuario.php');
include ('centro.php');
include ('security.php');
require_once('calendar/tc_calendar.php');

$enlace = "asignar_precio.php?key=$usr_key";
	

if(isset($_POST['opcion']))
	$opcion = $_POST['opcion'];
else
	if(isset($_GET['opcion']))
		$opcion = $_GET['opcion'];
	else
		$opcion = 0;
	
if($opcion == 2)
	header("Location: crear_producto.php?key=$usr_key");

if($opcion == 4)
	header("Location: main.php?q=1&key=$usr_key;");

$catBLO = new ProductoCategoriaBLO();
$prodBLO = new ProductoBLO();

$cenBLO = new CentroBLO();
	
if(isset($_POST['operacion']))
	$operacion = $_POST['operacion'];
else
	$operacion = "";

if(isset($_POST['categoria']))
	$categoria = $_POST['categoria'];
else
	$categoria = 0;

if(isset($_POST['producto']))
	$producto = $_POST['producto'];
else
	$producto = 0;

if(isset($_POST['producto_precio']))
	$producto_precio = $_POST['producto_precio'];
else
	$producto_precio = 0;
	
if(isset($_POST['precio_bruto_mn']))
	$precio_bruto_mn = $_POST['precio_bruto_mn'];
else
	$precio_bruto_mn = 0;

if(isset($_POST['impuesto_mn']))
	$impuesto_mn = $_POST['impuesto_mn'];
else
	$impuesto_mn = 0;
	
if(isset($_POST['precio_neto_mn']))
	$precio_neto_mn = $_POST['precio_neto_mn'];
else
	$precio_neto_mn = 0;
	
if(isset($_POST['fecha_inicio']))
	$fecha_inicio = $_POST['fecha_inicio'];
else
	$fecha_inicio = '';

if(isset($_POST['fecha_fin']))
	$fecha_fin = $_POST['fecha_fin'];
else
	$fecha_fin = '';

if(isset($_POST['centro']))
	$centro = $_POST['centro'];
else
	$centro = 0;


if($operacion == "mostrar_precio")
{
	if($producto > 0)
	{
		$prod = $prodBLO->RetornarProducto($producto);
		$producto_desc = $prod->descripcion_corta;
		$precio_bruto_mn = 0;
		$precio_neto_mn = 0;
		$impuesto_mn = 0;
	}
	
}
	
if($operacion == "editar_precio")
{
	
	if($producto_precio > 0)
	{		
		$precio = $prodBLO->RetornarPrecioXId($producto_precio);
		//echo "Producto: $precio->producto";
		if($precio != NULL)
		{
			$producto_desc = $precio->producto;
			$centro = $precio->id_centro;
			$precio_bruto_mn = $precio->precio_bruto_mn;
			$impuesto_mn = $precio->impuesto_mn;
			$precio_neto_mn = $precio->precio_neto_mn;
			$fecha_inicio = $precio->fecha_inicio;
			$fecha_fin = $precio->fecha_fin;

		}
	}	
}

if($operacion == "modificar_precio")
{
	if($producto_precio > 0)
	{
		$precio = new ProductoPrecio();		
		$precio->id = $producto_precio;
		$precio->precio_bruto_mn = $precio_bruto_mn;
		$precio->impuesto_mn = $impuesto_mn;
		$precio->precio_neto_mn = $precio_neto_mn;
		$precio->fecha_inicio = $fecha_inicio;
		$precio->fecha_fin = $fecha_fin;
		$precio->id_usuario = $cUsr->id;
		
		$prodBLO->ModificarPrecio($precio);
		
		header("Location: $enlace");			
	}
}

if($operacion == "agregar_precio")
{
	if($producto > 0)
	{
		$precio = new ProductoPrecio();
		$precio->id_centro = $centro;		
		$precio->id_producto = $producto;
		$precio->precio_bruto_mn = $precio_bruto_mn;
		$precio->impuesto_mn = $impuesto_mn;
		$precio->precio_neto_mn = $precio_neto_mn;
		$precio->fecha_inicio = $fecha_inicio;
		$precio->fecha_fin = $fecha_fin;
		$precio->id_usuario = $cUsr->id;
				
		$prodBLO->CrearPrecio($precio);
		
		header("Location: $enlace");
			
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
		<link href="calendar/calendar.css" rel="stylesheet" type="text/css" />
		<script language="javascript" src="calendar/calendar.js"></script>
		<script type="text/javascript">
		
			/*function IsNumeric(expression)
			{
			    return (String(expression).search(/^\d+$/) != -1);
			}*/
			
			function IsNumeric(n)
	        {
	            var n2 = n;
	            n = parseFloat(n);
	            return (n!='NaN' && n2==n);
	        }
			
			function MostrarPrecio()
			{
				document.producto.operacion.value = 'mostrar_precio';
				document.producto.submit();
			}
			
			function EditarPrecio(producto_precio)
			{				
				document.producto.operacion.value = 'editar_precio';
				document.producto.producto_precio.value = producto_precio;
				document.producto.submit();
			}
			
			function Desconectarse()
			{
				window.location.href = "login.php";
			}
			
			function Cancelar()
			{
				document.producto.operacion.value = 'mostrar_precio';
				document.producto.submit();
			}
			
			
			function CalcularMontos()
			{
				
				var precio_bruto_mn = document.producto.precio_bruto_mn.value;
				var precio_neto_mn = 0;
				var impuesto_mn = 0;
				var respuesta = true;
				if(IsNumeric(precio_bruto_mn))
				{					
					precio_neto_mn = precio_bruto_mn / 1.18;
					precio_neto_mn = Math.round(precio_neto_mn * 100)/100;
					
					impuesto_mn = precio_bruto_mn - precio_neto_mn;
					
					document.producto.precio_neto_mn.value = precio_neto_mn;
					
					document.producto.impuesto_mn.value = Math.round(impuesto_mn * 100)/100;
					
				}
				else
				{
					alert('Valor no correcto!');
					document.producto.precio_bruto_mn.value = 0.0;
					respuesta = false;
				}
				
				return respuesta;
			}
			
			function ModificarPrecio(producto_precio)
			{
				document.producto.fecha_inicio.value = document.getElementById("fecha_inicio").value;
				document.producto.fecha_fin.value = document.getElementById("fecha_fin").value;
				if(CalcularMontos())
				{
					var respuesta = confirm('Esta seguro de cambiar el Precio?');
					if(respuesta)
					{
						
						document.producto.operacion.value = 'modificar_precio';
						document.producto.producto_precio.value = producto_precio;
						document.producto.submit();
					}
				}
				
			}
			
			function AgregarPrecio()
			{
				document.producto.fecha_inicio.value = document.getElementById("fecha_inicio").value;
				document.producto.fecha_fin.value = document.getElementById("fecha_fin").value;
				if(CalcularMontos())
				{
					if(document.producto.precio_bruto_mn.value > 0)
					{
						if(document.producto.centro.value > 0)
						{
							var respuesta = confirm('Esta seguro de agregar el Precio?');
							if(respuesta)
							{
								document.producto.operacion.value = 'agregar_precio';					
								document.producto.submit();
							}	
						}
						else
							alert('No ha seleccionado un Centro Valido');
					}
					else
						alert('El Precio Bruto del producto debe ser mayor a 0!');
				}				
			}

		</script>
		<style type="text/css">
			
			/* table styles */
			.grid {
				border:0;
				padding:0;
				margin:0 0 1em;				
				border-top:1px dotted #336; 
				float:left;
				clear:left;
				}
			.fila {
				border:0;
				padding:0;
				margin:0;
				border-left:1px dotted #336;
				}
			.columna {border:0;padding:2px 6px;margin:0;border-right:1px dotted #336;border-bottom:1px dotted #336;background-color:#EAEEF3;}
			td[axis='number'], td[axis='date'] {text-align:right;}
			.cabecera {white-space:no-wrap;background-color:#B4C4D1;padding:2px 20px; border-left:1px dotted #336;}
			
			tfoot td {border-top:0;}
			thead th {border-bottom:1px dotted #003;}
			.odd td {background-color:#E8ECF1;}
			.even td {background-color:#DDE5EB;}
			
			
		</style>
	</head>
	<body>
		<?php
			include('header.php');
		?>	
		<div id="producto" style="float: left; padding-left: 20px; padding-top: 20px; width: 1000px; font-family: Helvetica;">
			
			<form name="producto" action="<?php echo $enlace; ?>" method="POST" >				
				<input name="operacion" type="hidden" />
				<input name="producto_precio" type="hidden" />
				<table id="tb_producto" style="border: dotted 1px #3399FF; width:1000px; background-color: #E6F2FF; color: #585858;" class="clase12">
					
					<tr style="height: 40px;">
						<td colspan="4" align="center">
							<span style="font-weight: bold; font-size:14px;">ASIGNACION DE PRECIOS A PRODUCTOS</span>	
						</td>						
					</tr>					
					<tr>
						<td colspan="4">
							<hr>
						</td>
					</tr>
					<tr>						
						<td>
							<span style="font-weight: bold;">Seleccione Categoria de Producto:</span>
						</td>
						<td style="width: 200px;" colspan="3">
							<select name="categoria" style="font-size: 12px; width: 200px; font-family:Arial;" onchange="submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$categorias = $catBLO->Listar();
								if(count($categorias) > 0)
								{
																		
									foreach ($categorias as $cat)
									{
										if($cat->id == $categoria)
											$selected = "selected = 'selected'";
										else
											$selected = "";  									
										echo "<option value='$cat->id' $selected>$cat->descripcion</option>";
									}										
								}							
							?>	
							</select>
						</td>
					</tr>
						
					<tr>
						<td><span style="font-weight: bold;">Seleccione Producto:</span></td>
						<td style="width: 400px;" colspan="3">
							<select name="producto" style="font-size: 12px; width: 400px; font-family: Arial;" onchange="MostrarPrecio();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$productos = $prodBLO->ListarXCategoria($categoria);								
								if(count($productos) > 0)
								{
																		
									foreach ($productos as $prod)
									{
										if($prod->id == $producto)										
											$selected = "selected = 'selected'";
										else
											$selected = "";  									
										echo "<option value='$prod->id' $selected>[$prod->codigo]: $prod->descripcion_corta ($prod->descripcion_larga)</option>";
									}										
								}							
							?>	
							</select>
						</td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<tr><td><span style="font-weight: bold;">Precio:</span></td></tr>
					<tr>
						<td colspan="4" align="center">
							<table cellpadding="0" cellspacing="0" class="grid">
								<thead class="cabecera">
									<tr class="fila">
										<th class="columna" id="producto" style="width:250px;"><span style="font-size: 11px;" >Producto</span></th>
										<th class="columna" id="centro" style="width:90px;"><span style="font-size: 11px;" >Centro</span></th>
										<th class="columna" id="fecha_inicio" style="width:145px;"><span style="  font-size: 11px;" >Fecha Inicio</span></th>
										<th class="columna" id="fecha_fin" style="width:145px;"><span style=" font-size: 11px;" >Fecha Fin</span></th>
										<th class="columna" id="precio_bruto_mn" style="width:85px;"><span style=" font-size: 11px;" >Precio Bruto</span></th>
										<th class="columna" id="impuesto_mn" style="width:85px;"><span style=" font-size: 11px;" >Impuesto</span></th>
										<th class="columna" id="precio_neto_mn" style="width:80px;"><span style=" font-size: 11px;" >Precio Neto</span></th>
										<th class="columna" id="opcion_editar" style="width:20px;"><span style=" font-size: 11px;" ></span></th>
									</tr>									
								</thead>
								<tbody>
									<?php
									if($producto > 0)
									{
										$precios = $prodBLO->RetornarPreciosXProducto($producto);
										if($precios != NULL)
										{
											
											foreach ($precios as $px) 
											{
												$f_ini = date_create($px->fecha_inicio);
												$f_fin = date_create($px->fecha_fin);
												?>
												
												<tr class="fila">
													<td class="columna" align="center"><span style="font-size:11px;"><? echo $px->producto; ?></span></td>
													<td class="columna" align="center"><span style="font-size:11px;"><? echo $px->centro; ?></span></td>
													<td class="columna" align="center"><span style="font-size:11px;"><? echo date_format($f_ini, 'd-m-Y'); ?></span></td>
													<td class="columna" align="center"><span style="font-size:11px;"><? echo date_format($f_fin, 'd-m-Y'); ?></span></td>
													<td class="columna" align="center"><span style="font-size:11px;"><? echo "S/. ".number_format($px->precio_bruto_mn,2); ?></span></td>
													<td class="columna" align="center"><span style="font-size:11px;"><? echo "S/. ".number_format($px->impuesto_mn,2); ?></span></td>
													<td class="columna" align="center"><span style="font-size:11px;"><? echo "S/. ".number_format($px->precio_neto_mn,2); ?></span></td>
													<td class="columna" align="center"><img src="images/edit.png" onmouseover="this.style.cursor='pointer'" onclick="EditarPrecio(<?php echo $px->id;?>)" title="Click para editar precio" /></td>
												</tr>	
											<?}
											
										}	
									}
									?>
									<tr><td colspan="8"><hr></tr>
									<tr class="fila">
										<td class="columna"><input style="width:255px; text-align:center; font-family:Arial; font-size: 11px;" readonly="readonly" value="<?php echo $producto_desc; ?>" /></td>
										<td class="columna">
											
											<?php
											$centros = $cenBLO->ListarTodos();
											
											if($operacion == "editar_precio")
												$disabled = "disabled = 'disabled'";
											else
												$disabled = '';
											if($centros != NULL)
											{												
												?>
												<select <? echo $disabled; ?> name="centro" style="width:100px; font-family:Arial; font-size:11px;">
													<option disabled="disabled">Seleccione</option>
													<?php	
													foreach ($centros as $c) 
													{
														if($c->id == $centro)													
															$selected = "selected = 'selected'";
														else
															$selected = "";																								 
														
														?>
													<option value="<? echo $c->id;?>" <?php echo $selected; ?>><? echo $c->descripcion; ?></option>
													<?}
											}
										?>
											</select>	
										</td>
										<td align="center" class="columna" style="font-size:12px;">
											<input name="precio_fecha_inicio" type="hidden"/>
											
											<?php
											
											$myCalendar = new tc_calendar("fecha_inicio", true, false);
											$myCalendar->setIcon("calendar/images/iconCalendar.gif");
											if($operacion == "editar_precio")
											{																								
												$f_ini = date_create($fecha_inicio);
												$myCalendar->setDate(date_format($f_ini,'d'), date_format($f_ini,'m'), date_format($f_ini,'Y'));
											}
											else 
												$myCalendar->setDate(date('d'), date('m'), date('Y'));
											$myCalendar->setPath("calendar/");
											$myCalendar->setYearInterval(2000, 2015);
											$myCalendar->dateAllow('2012-01-01', '2015-03-01');
											$myCalendar->setDateFormat('d-m-Y');
											$myCalendar->setAlignment('left', 'bottom');
											//$myCalendar->setSpecificDate(array("2011-04-01", "2011-04-04", "2011-12-25"), 0, 'year');
											//$myCalendar->setSpecificDate(array("2011-04-10", "2011-04-14"), 0, 'month');
											//$myCalendar->setSpecificDate(array("2011-06-01"), 0, '');
											$myCalendar->writeScript();
											?>
										</td>
										<td align="center" class="columna" style="font-size:12px;">
											<?php
											$myCalendar = new tc_calendar("fecha_fin", true, false);
											$myCalendar->setIcon("calendar/images/iconCalendar.gif");
											if($operacion == "editar_precio")
											{
												$f_fin = date_create($fecha_fin);
												$myCalendar->setDate(date_format($f_fin,'d'), date_format($f_fin,'m'), date_format($f_fin,'Y'));
											}
											else 
												$myCalendar->setDate(date('d'), date('m'), date('Y'));
											$myCalendar->setPath("calendar/");
											$myCalendar->setYearInterval(2000, 2015);
											$myCalendar->dateAllow('2012-01-01', '2015-03-01');
											//$myCalendar->setDateFormat('j F Y');
											$myCalendar->setDateFormat('d-m-Y');
											$myCalendar->setAlignment('left', 'bottom');											
											$myCalendar->writeScript();
											?>
										</td>
										<td class="columna" align="center">
											<span style="font-family:Arial; font-size:11px;">S/.</span>
											<input name="precio_bruto_mn" style="text-align:center; width:58px; font-family:Arial; font-size: 11px;" value="<?php echo number_format($precio_bruto_mn,2) ;?>" />
										</td>
										<td class="columna" align="center">
											<span style="font-family:Arial; font-size:11px;">S/.</span>
											<input name="impuesto_mn" readonly="readonly" style="text-align:center; width:58px; font-family:Arial; font-size: 11px;" value="<?php echo number_format($impuesto_mn,2) ;?>"/>
										</td>
										<td class="columna" align="center">
											<span style="font-family:Arial; font-size:11px;">S/.</span>
											<input name="precio_neto_mn" readonly="readonly" style="text-align:center; width:58px; font-family:Arial; font-size: 11px;" value="<?php echo number_format($precio_neto_mn,2) ;?>"/>	
										</td>
										<td class="columna" align="center">
											<img src="images/calc.png" style="width:13px; height: 15px;" onmouseover="this.style.cursor='pointer'" onclick="CalcularMontos()" title="Click para calcular el Impuesto y el Precio Neto" />											
										</td>
									</tr>
									
									<?php									
									if($operacion == "editar_precio")
									{?>
									<tr class="fila">										
										<td colspan="6" align="right">
											<input style="font-size:11px; font-family:Arial;" type="button" value="Modificar Precio" onclick="ModificarPrecio(<?php echo $producto_precio; ?>)" />
										</td>
										<td colspan="2" align="right">
											<input style="font-size:11px; font-family:Arial;" type="button" value="Cancelar" onclick="Cancelar()" />
										</td>							
									</tr>
									<?}
									else									
									{?>
									<tr class="fila">										
										<td colspan="6" align="right">
											<input style="font-size:11px; font-family:Arial;" type="button" value="Agregar Precio" onclick="AgregarPrecio()" />
										</td>
										<td colspan="2" align="right">
											<input style="font-size:11px; font-family:Arial;" type="button" value="Cancelar" onclick="Cancelar()" />
										</td>										
									</tr>
									<?}?>									
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>
	
