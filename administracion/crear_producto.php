<?php

include ('cuenta_venta.php');
include ('usuario.php');
include ('centro.php');

include('security.php');
$enlace = "crear_producto.php?key=$usr_key";
	
$catBLO = new ProductoCategoriaBLO();
$prodBLO = new ProductoBLO();

if(isset($_POST['opcion']))
	$opcion = $_POST['opcion'];
else
	if(isset($_GET['opcion']))
		$opcion = $_GET['opcion'];
	else
		$opcion = 0;

if(isset($_POST['pais']))
	$pais = $_POST['pais'];
else
	$pais = 0;

if(isset($_POST['categoria_1']))
	$categoria_1 = $_POST['categoria_1'];
else
	$categoria_1 = 0;

if(isset($_POST['categoria_2']))
	$categoria_2 = $_POST['categoria_2'];
else
	$categoria_2 = 0;

if(isset($_POST['categoria_3']))
	$categoria_3 = $_POST['categoria_3'];
else
	$categoria_3 = 0;

if(isset($_POST['categoria_4']))
	$categoria_4 = $_POST['categoria_4'];
else
	$categoria_4 = 0;

if(isset($_POST['categoria']))
	$categoria = $_POST['categoria'];
else
	$categoria = 0;
	
if(isset($_POST['marca']))
	$marca = $_POST['marca'];
else
	$marca = 0;
	
if(isset($_POST['categoria_x']))
	$categoria_x = $_POST['categoria_x'];
else
	$categoria_x = 0;

if(isset($_POST['categoria_nueva']))
	$categoria_nueva = $_POST['categoria_nueva'];
else
	$categoria_nueva = 0;

if(isset($_POST['producto_x']))
	$producto_x = $_POST['producto_x'];
else
	$producto_x = 0;

if(isset($_POST['codigo']))
	$codigo = $_POST['codigo'];
else
	$codigo = "";

if(isset($_POST['descripcion_corta']))
	$descripcion_corta = $_POST['descripcion_corta'];
else
	$descripcion_corta = "";

if(isset($_POST['descripcion_larga']))
	$descripcion_larga = $_POST['descripcion_larga'];
else
	$descripcion_larga = "";

if(isset($_POST['nro_serie']))
	$nro_serie = $_POST['nro_serie'];
else
	$nro_serie = "";

if(isset($_POST['dimension']))
	$dimension = $_POST['dimension'];
else
	$dimension = "";

if(isset($_POST['unidad_medida']))
	$unidad_medida = $_POST['unidad_medida'];
else 
	$unidad_medida = 0;
	

if(isset($_POST['opcion_cantidad']))
	$opcion_cantidad = $_POST['opcion_cantidad'];
else
	$opcion_cantidad = '';
	
if(isset($_POST['operacion']))
	$operacion = $_POST['operacion'];
else
	$operacion = '';
	
$resultado = "";

$categoria = 0;
if($categoria_4 > 0)
	$categoria = $categoria_4;
if($categoria_3 > 0)
	$categoria = $categoria_3;
if($categoria_2 > 0)
	$categoria = $categoria_2;
if($categoria_1 > 0)
	$categoria = $categoria_1;
		
if($operacion == "crear_producto")
{
	$prod = new Producto();
	$prod->id_producto_categoria = $categoria;
	$prod->codigo = $codigo;
	$prod->descripcion_corta = $descripcion_corta;
	$prod->descripcion_larga = $descripcion_larga;
	$prod->id_pais_origen = $pais;
	$prod->nro_serie = $nro_serie;
	$prod->dimension = $dimension;
	$prod->id_unidad_medida = $unidad_medida;
	$prod->opcion_cantidad = $opcion_cantidad;
	$prod->id_marca = $marca;
	$prod->id_usuario = $cUsr->id;
	
	
	$prodBLO->Crear($prod);
	$resultado = "Producto Creado!";
	
	header("Location: crear_producto.php?key=$usr_key&opcion=1");
}

if($operacion == "comenzar_editar_producto")
{
	if($producto_x > 0)
	{		
		$prod = $prodBLO->RetornarProducto($producto_x);
		
		if(count($prod) > 0)
		{
			
			$categoria_nueva = $prod->id_producto_categoria;
			$codigo = $prod->codigo;
			$descripcion_corta = $prod->descripcion_corta;
			$descripcion_larga = $prod->descripcion_larga;
			$pais = $prod->id_pais_origen;
			$nro_serie = $prod->nro_serie;
			$dimension = $prod->dimension;
			$opcion_cantidad = $prod->opcion_cantidad;
			$unidad_medida = $prod->id_unidad_medida;
			$marca = $prod->id_marca;
		}	
	}	
}

$opcion_cantidad_arr = array();

if($opcion_cantidad != '')
{
	
	$ops = split(",", $opcion_cantidad);
	
	foreach($ops as $op)
	{
		$o = split(":", $op);
		$opcion_cantidad_arr[] = array("valor"=>$o[0], "etiqueta"=>$o[1]);
	}
}
else
{
	$opcion_cantidad_arr[] = array("valor"=>0, "etiqueta"=>0);
	$opcion_cantidad_arr[] = array("valor"=>1, "etiqueta"=>1);
	$opcion_cantidad_arr[] = array("valor"=>2, "etiqueta"=>2);
	$opcion_cantidad_arr[] = array("valor"=>3, "etiqueta"=>3);
	$opcion_cantidad_arr[] = array("valor"=>4, "etiqueta"=>4);
	$opcion_cantidad_arr[] = array("valor"=>5, "etiqueta"=>5);
}

$op_cant = "";
$i = 0;
foreach($opcion_cantidad_arr as $oc)
{
	if($i == 0)
		$op_cant = $oc["valor"].":".$oc["etiqueta"];
	else
		$op_cant = $op_cant.",".$oc["valor"].":".$oc["etiqueta"];
	$i++;
}

$opcion_cantidad = $op_cant;

if($operacion == "modificar_producto")
{
	$prod = new Producto();
	$prod->id = $producto_x;
	$prod->id_producto_categoria = $categoria_nueva;
	$prod->codigo = $codigo;
	$prod->descripcion_corta = $descripcion_corta;
	$prod->descripcion_larga = $descripcion_larga;
	$prod->id_pais_origen = $pais;
	$prod->nro_serie = $nro_serie;
	$prod->dimension = $dimension;
	$prod->id_unidad_medida = $unidad_medida;
	$prod->opcion_cantidad = $opcion_cantidad;
	$prod->id_marca = $marca;
	$prod->id_usuario = $cUsr->id;
	
	$prodBLO->Modificar($prod);
	
	$resultado = "Producto Modificado!";
	
	header("Location: crear_producto.php?key=$usr_key");
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
			
			function CargarOpcionCantidad()
			{
				var etiqueta, valor;
				var opcion_cantidad;
				
				for(i = 0; i <= 5 ; i++ )
				{
					etiqueta = document.getElementById('op_cantidad_etiqueta' + i).value;
					valor = document.getElementById('op_cantidad_valor' + i).value;
					
					if(i == 0)
						opcion_cantidad = valor + ':' + etiqueta;
					else
						opcion_cantidad += ',' + valor + ':' + etiqueta;
				}
								
				document.producto.opcion_cantidad.value = opcion_cantidad;
			}
			
			function Desconectarse()
			{
				window.location.href = "login.php";
			}
			
			function CrearProducto()
			{
				var resultado = true;
				var msg = "";
				
				CargarOpcionCantidad();
				
				if(document.producto.categoria_4.value == 0)
				{
					alert("No ha seleccionado Categoria 4 de Producto!");
					resultado = false;
				}
				if(document.producto.categoria_3.value == 0)
				{
					alert("No ha seleccionado Categoria 3 de Producto!");
					resultado = false;
				}
				if(document.producto.codigo.value == '')
				{
					alert("No ha Ingresado Codigo de Producto!");
					resultado = false;
				}
				if(document.producto.descripcion_corta.value == '')
				{
					alert("No ha Ingresado Descripcion Corta de Producto!");
					resultado = false;
				}
				if(document.producto.descripcion_larga.value == '')
				{
					alert("No ha Ingresado Descripcion Larga de Producto!");
					resultado = false;
				}
				if(document.producto.pais.value == 0)
				{
					alert("No ha Seleccionado Pais de Origen!");
					resultado = false;
				}
				if(document.producto.dimension.value == '')
				{
					alert("No ha Ingresado Dimension del Producto!");
					resultado = false;
				}
				if(document.producto.unidad_medida.value == 0)
				{
					alert("No ha Seleccionado Unidad de Medida!");
					resultado = false;
				}
								
				if(document.producto.categoria_4.value > 0)
					document.producto.categoria.value = document.producto.categoria_4.value;
				if(document.producto.categoria_3.value > 0)
					document.producto.categoria.value = document.producto.categoria_3.value;
				if(document.producto.categoria_2.value > 0)
					document.producto.categoria.value = document.producto.categoria_2.value;
				if(document.producto.categoria_1.value > 0)
					document.producto.categoria.value = document.producto.categoria_1.value;
				
				document.producto.operacion.value = "crear_producto";
				
				if(resultado)
					document.producto.submit();				
			}
			
			function SeguirEditandoProducto()
			{
				document.producto.operacion.value = "seguir_editando_producto";				
			}
			
			function ComenzarEditarProducto()
			{				
				document.producto.operacion.value = "comenzar_editar_producto";
				document.producto.opcion.value = 2;				
				document.producto.submit();
			}
			function ModificarProducto()
			{
				CargarOpcionCantidad();
				
				document.producto.operacion.value = "modificar_producto";
				document.producto.opcion.value = 2;
				document.producto.submit();
			}
			
			function MostrarResultado()
			{
				var resultado = '<?php echo $resultado;?>';
				if(resultado != '')
					alert(resultado);
			}

		</script>
	</head>
	<body onload="MostrarResultado()">
		
		<?php 
			include("header.php");		
		?>
		<div id="producto" style="float: left; padding-left: 20px; padding-top: 20px; width: 1000px; font-family: Helvetica;">
			
			<form name="producto" action="<?php echo $enlace; ?>" method="POST" >				
				<input name="categoria" type="hidden" value=<?php echo $categoria; ?>/>
				<input name="opcion_cantidad" type="hidden" value="<?php echo $op_cant; ?>"/>
				<input name="operacion" type="hidden" />		
				<table id="tb_producto" style="border: dotted 1px #3399FF; width:1000px; background-color: #E6F2FF; color: #585858;" class="clase12">
					<tr style="height: 40px;">
						<td colspan="4" align="center">
							<span style="font-weight: bold; font-size:14px;">CREACION Y MODIFICACION DE PRODUCTOS</span>	
						</td>						
					</tr>
					<tr>
						<td width="200px;">
							<span style="font-weight: bold;">Opcion General:</span>
						</td>
						<td>
							<select name="opcion" style="width:200px; font-size: 11px;" onchange="submit()">
								<option value="0" disabled="disabled">Seleccione...</option>
								<option value="1" <? if($opcion == 1) echo "selected = 'selected'";?>>Crear Producto</option> 
								<option value="2" <? if($opcion == 2) echo "selected = 'selected'";?>>Editar Producto</option>								
							</select>
						</td>
						<td colspan="2">							
						</td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<?php
					if($opcion == 2)
					{?>
						
					<tr>						
						<td>
							<span style="font-weight: bold;">Seleccione Categoria de Producto:</span>
						</td>
						<td style="width: 200px;" colspan="3">
							<select name="categoria_x" style="font-size: 12px; width: 200px;" onchange="submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$categorias = $catBLO->Listar();
								if(count($categorias) > 0)
								{
																		
									foreach ($categorias as $cat)
									{
										if($cat->id == $categoria_x)
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
							<select name="producto_x" style="font-size: 12px; width: 400px;" onchange="ComenzarEditarProducto();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$productos = $prodBLO->ListarXCategoria($categoria_x);
								if(count($productos) > 0)
								{
																		
									foreach ($productos as $prod)
									{
										if($prod->id == $producto_x)
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
					<?}					
					if($opcion == 1)
					{						
					?>					
					<tr>
						<td colspan="4">
							<span style="font-weight: bold; font-size:13px;">Categorias de Productos</span>	
						</td>						
					</tr>
					<tr>
						<td style="width: 100px;">
							<span style="font-weight: bold;">Categoria 4:</span>
						</td>
						<td style="width: 200px;">
							<select name="categoria_4" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad();	submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$categorias = $catBLO->ListarCategoriasPrincipales();
								if(count($categorias) > 0)
								{
																		
									foreach ($categorias as $cat)
									{
										if($cat->id == $categoria_4)
											$selected = "selected = 'selected'";
										else
											$selected = "";  									
										echo "<option value='$cat->id' $selected>$cat->descripcion</option>";
									}										
								}							
							?>	
							</select>
						</td>						
						<td style="width: 100px;">
							<span style="font-weight: bold;">Categoria 3:</span>
						</td>
						<td style="width: 200px;">
							<select name="categoria_3" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad();	submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
								if($categoria_4 > 0)
								{
									$categorias = $catBLO->ListarCategoriaXCategoriaPadre($categoria_4);
									if(count($categorias) > 0)
									{
										foreach ($categorias as $cat)
										{
											if($cat->id == $categoria_3)
												$selected = "selected = 'selected'";
											else
												$selected = "";  									
											echo "<option value='$cat->id' $selected>$cat->descripcion</option>";											
										}										
									}
								}
															
							?>	
							</select>
						</td>
					</tr>
					<tr>
						<td style="width: 80px;">
							<span style="font-weight: bold;">Categoria 2:</span>
						</td>
						<td style="width: 200px;">
							<select name="categoria_2" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad();	submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$categorias = $catBLO->ListarCategoriaXCategoriaPadre($categoria_3);
								if(count($categorias) > 0)
								{
																		
									foreach ($categorias as $cat)
									{
										if($cat->id == $categoria_2)
											$selected = "selected = 'selected'";
										else
											$selected = "";  									
										echo "<option value='$cat->id' $selected>$cat->descripcion</option>";
									}										
								}							
							?>	
							</select>
						</td>						
						<td style="width: 80px;">
							<span style="font-weight: bold;">Categoria 1:</span>
						</td>
						<td style="width: 200px;">
							<select name="categoria_1" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad();	submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
								if($categoria_2 > 0)
								{
									$categorias = $catBLO->ListarCategoriaXCategoriaPadre($categoria_2);
									if(count($categorias) > 0)
									{
										foreach ($categorias as $cat)
										{
											if($cat->id == $categoria_1)
												$selected = "selected = 'selected'";
											else
												$selected = "";  									
											echo "<option value='$cat->id' $selected>$cat->descripcion</option>";											
										}										
									}
								}
															
							?>	
							</select>
						</td>						
					</tr>					
					<?}
					if(($operacion == 'comenzar_editar_producto' || $operacion == 'seguir_editando_producto') && $opcion != 1)
					{?>
					<tr><td colspan="4"><hr></td></tr>
					<tr>
						<td style="width: 100px;">
							<span style="font-weight: bold;">Nueva Categoria: </span>
						</td>
						<td style="width: 200px;">
							<select name="categoria_nueva" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad(); SeguirEditandoProducto(); submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$categorias = $catBLO->Listar();
								if(count($categorias) > 0)
								{
																		
									foreach ($categorias as $cat)
									{
										if($cat->id == $categoria_nueva)
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
					<tr><td colspan=4><hr></td></tr>
						
					<?}
					if($opcion == 1 || $operacion == 'comenzar_editar_producto' || $operacion == 'seguir_editando_producto')
					{
					?>
					<tr>
						<td colspan="4">
							<span style="font-weight: bold; font-size:13px;">Detalle de Producto</span>	
						</td>						
					</tr>
					<tr>
						<td style="width: 100px;">
							<span style="font-weight: bold;">Marca:</span>
						</td>
						<td style="width: 200px;" colspan="3">
							<select name="marca" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad(); SeguirEditandoProducto(); submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
							
								$marcas = $prodBLO->listarMarcas();
								if(count($marcas) > 0)
								{
																		
									foreach ($marcas as $m)
									{
										if($m->id == $marca)
											$selected = "selected = 'selected'";
										else
											$selected = "";  									
										echo "<option value='$m->id' $selected>$m->nombre</option>";
									}										
								}							
							?>	
							</select>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight: bold;">Codigo (max 15 crs.):</span></td>
						<td>
							<input type="text" style="font-size:11px;" maxlength="15" name="codigo" value="<?php echo $codigo; ?>"/>
						</td>
						<td><span style="font-weight: bold;">Descripcion Corta:</span></td>
						<td>
							<input style="width:200px; font-size: 11px;" type="text" name="descripcion_corta" value="<? echo $descripcion_corta; ?>"/>
						</td>
					</tr>
					<tr>
						<td>
							<span style="font-weight: bold;">Descripcion Larga:</span>	
						</td>
						<td colspan = 3>
							<input style="width:300px; font-size: 11px;" type="text" name="descripcion_larga" value="<? echo $descripcion_larga; ?>"/>
						</td>
					</tr>
					<tr>
						<td style="width: 80px;">
							<span style="font-weight: bold;">Pais Origen:</span>
						</td>
						<td style="width: 200px;">
							<select name="pais" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad(); SeguirEditandoProducto(); submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
								$paBLO = new PaisBLO();
								$paises = $paBLO->Listar();
									if(count($paises) > 0)
									{
										foreach ($paises as $pa)
										{
											if($pa->id == $pais)
												$selected = "selected = 'selected'";
											else
												$selected = "";  									
											echo "<option value='$pa->id' $selected>$pa->nombre</option>";											
										}										
									}
															
							?>	
							</select>
						</td>
						<td><span style="font-weight: bold;">Nro Serie:</span></td>
						<td>
							<input style="width:135px; font-size: 11px;" type="text" maxlength="20" name="nro_serie" value="<? echo $nro_serie; ?>"/>
						</td>
						
					</tr>
					<tr>
						<td><span style="font-weight: bold;">Dimension:</span></td>
						<td>
							<input style="width:80px; font-size: 11px;" type="text" maxlength="20" name="dimension" value="<? echo $dimension; ?>"/>
						</td>
						<td><span style="font-weight: bold;">Unidad Medida:</span></td>
						<td style="width: 200px;">
							<select name="unidad_medida" style="font-size: 12px; width: 200px;" onchange="CargarOpcionCantidad(); SeguirEditandoProducto(); submit();">
								<option disabled="disabled">Seleccione...</option>
							<?php
								$uBLO = new UnidadMedidaBLO();
								$unidades = $uBLO->Listar();
									if(count($unidades) > 0)
									{
										foreach ($unidades as $u)
										{
											if($u->id == $unidad_medida)
												$selected = "selected = 'selected'";
											else
												$selected = "";  									
											echo "<option value='$u->id' $selected>$u->descripcion [$u->codigo]</option>";											
										}										
									}
															
							?>	
							</select>
						</td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<tr>
						<td><span style="font-weight: bold;">Opcion Cantidad</span></td>
						<td colspan="3">
							<table >
								<tr>
									<td style="width:32px;"><span style="font-weight:bold; font-size:11px;">Valor</span></td>
									<td style="width:102px;"><span style="font-weight:bold; font-size:11px;">Etiqueta</span></td>
								</tr>
								<tr>
									<td style="width:32px;"><input style="width:30px; font-size:11px;" name="op_cantidad_valor0" disabled="disabled" id="op_cantidad_valor0" value="<?php echo $opcion_cantidad_arr[0]['valor'];?>"/></td>
									<td style="width:102px;"><input style="width:100px; font-size:11px;" name="op_cantidad_etiqueta0" id="op_cantidad_etiqueta0" value="<?php echo $opcion_cantidad_arr[0]['etiqueta'];?>"/></td>
								</tr>
								<tr>
									<td style="width:32px;"><input style="width:30px; font-size:11px;" name="op_cantidad_valor1" id="op_cantidad_valor1" value="<?php echo $opcion_cantidad_arr[1]['valor'];?>"/></td>
									<td style="width:102px;"><input style="width:100px; font-size:11px;" name="op_cantidad_etiqueta1" id="op_cantidad_etiqueta1" value="<?php echo $opcion_cantidad_arr[1]['etiqueta'];?>"/></td>
								</tr>
								<tr>
									<td style="width:32px;"><input style="width:30px; font-size:11px;" name="op_cantidad_valor2" id="op_cantidad_valor2" value="<?php echo $opcion_cantidad_arr[2]['valor'];?>"/></td>
									<td style="width:102px;"><input style="width:100px; font-size:11px;" name="op_cantidad_etiqueta2" id="op_cantidad_etiqueta2" value="<?php echo $opcion_cantidad_arr[2]['etiqueta'];?>"/></td>
								</tr>
								<tr>
									<td style="width:32px;"><input style="width:30px; font-size:11px;" name="op_cantidad_valor3" id="op_cantidad_valor3" value="<?php echo $opcion_cantidad_arr[3]['valor'];?>"/></td>
									<td style="width:102px;"><input style="width:100px; font-size:11px;" name="op_cantidad_etiqueta3" id="op_cantidad_etiqueta3" value="<?php echo $opcion_cantidad_arr[3]['etiqueta'];?>"/></td>
								</tr>
								<tr>
									<td style="width:32px;"><input style="width:30px; font-size:11px;" name="op_cantidad_valor4" id="op_cantidad_valor4" value="<?php echo $opcion_cantidad_arr[4]['valor'];?>"/></td>
									<td style="width:102px;"><input style="width:100px; font-size:11px;" name="op_cantidad_etiqueta4" id="op_cantidad_etiqueta4" value="<?php echo $opcion_cantidad_arr[4]['etiqueta'];?>"/></td>
								</tr>
								<tr>
									<td style="width:32px;"><input style="width:30px; font-size:11px;" name="op_cantidad_valor5" id="op_cantidad_valor5" value="<?php echo $opcion_cantidad_arr[5]['valor'];?>"/></td>
									<td style="width:102px;"><input style="width:100px; font-size:11px;" name="op_cantidad_etiqueta5" id="op_cantidad_etiqueta5" value="<?php echo $opcion_cantidad_arr[5]['etiqueta'];?>"/></td>
								</tr>								
							</table>
						</td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<?php
					}
					if($opcion == 1)
					{
					?>
					<tr><td colspan="4"><input style="font-size:11px;" type="button" name="crear_producto" value="Crear Producto" onclick="CrearProducto()"/></td></tr>
					<?}
					if(($operacion == 'comenzar_editar_producto' || $operacion == 'seguir_editando_producto') && $opcion != 1)
					{?>
					<tr><td colspan="4"><input style="font-size:11px;" type="button" name="modificar_producto" value="Modificar Producto" onclick="ModificarProducto()"/></td></tr>	
					<?}?>
					
					
				</table>
			</form>
		</div>
	</body>
</html>
	
