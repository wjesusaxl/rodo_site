<?php

session_start();

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

include ('../clases/enc_dec.php');
include ('../clases/general.php');
include ('../clases/usuario.php');
include ('../clases/centro.php');
include ('../clases/opcion.php');
include ('../clases/security.php');
include ("../clases/almacen.php");
include ("../clases/stock.php");
include ("../clases/producto.php");
include ("../clases/anuncio.php");

$almBLO = new AlmacenBLO();
$stkBLO = new StockBLO();
$proBLO = new ProductoBLO();

$enlace_procesar="stock_x_almacen.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";

if(isset($_POST["id_almacen"]))
	$id_almacen = $_POST["id_almacen"];
else
	$id_almacen = 0;

if($id_almacen > 0)
	$almacen = $almBLO->RetornarXId($id_almacen);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		<style type="text/css">
			
			body { background-color: #F1F1F1; }
			#div_main {  width: 1100px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
			border-radius: 10px 10px 10px 10px;}
			
			.sin_info { font-size: 11px; color:#585858; font-family: Helvetica; padding-left: 4px; }
			.etiqueta { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica;}
			.valor { font-size: 11px; color:#585858; font-family: Helvetica; }
			.valor_2 { font-size: 11px; color:#585858; font-family: Helvetica;color: #0099CC; }
			.valor_3 { font-size: 13px; color:#585858; font-family: Helvetica;color: #0099CC; font-weight: bold; }
			.td_valor { border-bottom: dotted 1px #0099CC;}
			
			.etiqueta_operacion { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica; }
			
			#div_lista_productos #div_info { width: 800px; border-collapse: collapse;  }
			#tabla_lista_productos tbody td { color: #585858; font-size: 11px; font-family: Helvetica;  }
			
			/*#tabla_lista_productos tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_productos tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }*/
			
			#tabla_lista_productos { border-collapse: collapse; }
			#tabla_lista_productos td{ border-bottom: dotted 1px #0099CC; font-size: 12px; }
			#tabla_lista_productos thead th { color: #0099CC; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; font-size: 12px; font-family: Helvetica; }
			
			#tabla_lista_productos tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_productos tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }
			
			#tabla_lista_productos tbody tr:hover { background-color:#F5FCA6; }
			
			#titulo { font-weight: bold; font-size: 14px; color: #585858; font-family: Helvetica;  }
			
			#div_titulo { margin-bottom: 15px; margin-top: 15px; }
			
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
			.texto_2 { width: 100px; text-align: center; font-size: 11px; }
			.texto_3, .compra_operacion, #id_almacen_destino { width: 150px; text-align: center; font-size: 11px; }
			.texto_3_5 { width: 180px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4_5 { width: 230px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_10 { width: 400px; text-align: center; font-size: 11px; }
			
			#div_info { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 850px; padding-top: 10px; padding-bottom: 10px; }
			
			.div_opcion { border: dotted 1px #0099CC; font-family: Helvetica; border-radius: 10px 10px 10px 10px; display: none; }
			#div_almacen { width: 400px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; margin-bottom: 20px; }
			#div_lista_categorias { width: 1060px; margin-top: 20px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; 
				margin: 0 auto; overflow: hidden; padding-left: 20px;  }
			
			.categoria { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 500px; float: left; margin-right: 20px; margin-bottom: 20px; 
				padding-bottom: 5px;  }
			.titulo_categoria { border-bottom: dotted 1px #0099CC; font-family: Helvetica; font-size: 13px; font-weight: bold; color: #FFFFFF; 
				margin-bottom: 10px; background-color: #0099CC; border-radius: 8px 8px 0px 0px;  }
			
			.tabla_lista_productos { border-collapse: collapse; }
			.tabla_lista_productos thead th { font-family: Helvetica; font-size: 12px; color: #0099CC; border-bottom: dotted 1px #0099CC; }
			.tabla_lista_productos tbody tr td { font-family: Helvetica; font-size: 11px; color: #585858; border-bottom: dotted 1px #0099CC; }
			
			#div_almacen_seleccionado { margin-top: 20px; margin-bottom: 20px;}
			#almacen_seleccionado { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #585858; }
			
			.cantidad_2 { color: #54A805; font-weight: bold; }
			.cantidad_1 { color: #FAD148; font-weight: bold; }
			.cantidad_0 { color: #FC4401; font-weight: bold; }
			
		</style>
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		
		
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		$(function()
		{
			$("#mostrar_stock").click(function()
			{
				if($("#id_almacen").val() > 0)
				{
					$("#almacen").submit();
				}
				else
					alert("No ha seleccionado Almacén")
			});
			
		})
		
			
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");
	?>
	<div id="div_main" align="center">
		<form id="almacen" name="almacen" action="<?php echo $enlace_procesar;?>" method="POST">
			
			<div id="div_almacen">
				<table id="tabla_almacen">
					<tr>
						<td><span class="etiqueta_operacion">Almacen:</span></td>
						<td width=20px></td>
						<td>
							<select id="id_almacen" name="id_almacen" class="texto_3">
							<?php
							
							$lista_almacenes = $almBLO->ListarAlmacenXIdUsuarioIdCentroHabilitado($id_usuario, $id_centro);
							
							
							if(!is_null($lista_almacenes))
							{
								echo "<option value=\"0\">Seleccione</option>\n";
								foreach($lista_almacenes as $a)
								{
									if($id_almacen == $a->id_almacen)
										$selected = "selected='selected'";
									else
										$selected = "";
									echo  "<option value=\"$a->id_almacen\" $selected>$a->almacen</option>\n";
								}
							}
								
							?>
							</select>
						</td>
						<td width=20px></td>
						<td>
							<?php
								if(!is_null($lista_almacenes))
								{
									if(count($lista_almacenes) > 0)
										$readonly = "";
									else
										$readonly = "readonly='readonly'";
								}
								else									
									$readonly = "readonly='readonly'";
							?>
							<input id="mostrar_stock" class="texto_2" value="Mostrar" type="button" <?php echo $readonly; ?> />
						</td>
					</tr>
				</table>
			</div>
			<div id="div_lista_categorias" style="display: <?php echo $id_almacen > 0 ? "block" : "none";?>">
				<div id="div_almacen_seleccionado">
					<span id="almacen_seleccionado"><?php echo "Stock en: ".strtoupper($almacen->descripcion);?></span>
				</div>
				
			<?php
			
			
			$stock = $stkBLO->ListarXIdAlmacen($id_almacen);
			$categorias = $proBLO->ListarCategoriasConProductos();
			
			if(!is_null($categorias))
			{
				foreach($categorias as $c)
				{?>
				<div class="categoria" align="center">
					<div class="titulo_categoria">
					<?php echo strtoupper($c->descripcion); $cont = 0;?>
					</div>
					<div class="div_lista_productos">
						<table class="tabla_lista_productos">
							<thead>
								<th width=120px>Marca</th>
								<th colspan="2" width=260px>Producto</th>								
								<th width=50px >Cant.</th>
								<th width=50px >Cant.Min</th>
							</thead>						
						<?php
						
						if(count($stock)> 0)
						{?>							
							
							<?php
							foreach($stock as $s)
							{
								if($s->id_producto_categoria == $c->id)
								{
									$clase_cantidad = "cantidad_0";
									
									if($s->cantidad >= 10)
										$clase_cantidad = "cantidad_1";
									if($s->cantidad >= 20)
										$clase_cantidad = "cantidad_2";
																	
									?>									
									<tr>
										<td align="center"><?php echo strtoupper($s->marca);?></td>
										<td width="2px"></td>
										<td><b><?php echo strtoupper($s->descripcion_corta);?></b></td>
										<td align="center"><span class="<?php echo $clase_cantidad;?>"><?php echo number_format($s->cantidad, 1);?></span></td>
										<td align="center"><?php echo $s->cantidad_minima;?></td>
									</tr>								
								<?php
								$cont ++;
								}								
							}							
							if($cont == 0)
								echo "<tr><td colspan=5 >No hay Stock de esta Categoría.</td></tr>";
						}
						else 
							echo "<tr><td colspan=5 >No hay Stock de esta Categoría.</td></tr>";
						?>		
						</table>						
					</div>
				</div>
				<?php	
				}
			}
			?>	
			</div>
		</form>
	</div>
	
	</body>
</html>