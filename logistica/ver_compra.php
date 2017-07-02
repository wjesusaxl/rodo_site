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
include ("../clases/compra.php");
include ("../clases/almacen.php");
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_almacen.php?id_centro=$id_centro&opcion_original_key=$opcion_original&usr_key=$usr_key";

if(isset($_GET["compra_key"]))
	$compra_key = $_GET["compra_key"];
else 
	$compra_key = "";
	
if(isset($_GET["mostrar"]))
	$mostrar = $_GET["mostrar"];
else 
	$mostrar = "";

$opcBLO = new OpcionBLO();
$comBLO = new CompraBLO($id_centro);
$almBLO = new AlmacenBLO();

if($compra_key != "")
{
	$compra = $comBLO->RetornarXKey($compra_key);
	if(!is_null($compra))
	{
		$fecha_hora = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($compra->fecha_hora_registro))));
		$id_compra = $compra->id;	
	}
	else
		$id_compra = 0;
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
			
			#etiqueta_operacion { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica; }
			#etiqueta_almacen_destino { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica; }
			
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
			#div_almacen_destino { width: 380px}
		</style>
		
		
		<script type="text/javascript">
		
		$(function()
		{
			$("#opcion").change(function()
			{
				$id_operacion = $(this).val(); 
				
				if($id_operacion == 1) // Generar
				{
					$("#div_almacen_destino").css("display", "block");
				}
				if($id_operacion == 0)
					$(".div_opcion").css("display", "none");
			})
			
			$("#ingresar_almacen").click(function()
			{
				$("#operacion").val("ingresar_almacen");
				$id_almacen_destino = $("#id_almacen_destino").val();
				if($id_almacen_destino > 0)
					if(confirm("¿Desea Ingresar los productos Listados hacia el Almacén de Destino?"))
						$("#compra").submit();
			})
			
		})
		
			
		</script>
	</head>
	<body>		
	
	<div id="div_main" align="center">
	<form id="compra" method="POST" action="<?php echo $enlace_procesar; ?>">
		<input name="id_compra" type="hidden" value="<?php echo $id_compra;?>"/>
		<input name="operacion" id="operacion" type="hidden" />
		<input name="id_usuario" id="id_usuario" type="hidden" value="<?php echo $id_usuario;?>"/>
		<div id="div_info" align="center">
			<table id="tabla_info">
				<tr>
					<td><span class="etiqueta">COMPRA:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo $compra->compra_key;?></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">FECHA:</span></td>
					<td width="10px" align="center"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo $fecha_hora;?></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">TIPO COMPRA:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($compra->compra_tipo);?></span></td>
				</tr>
				<tr>
					<td><span class="etiqueta">CENTRO:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($compra->centro);?></span></td>	
					<td width="50px"></td>
					<td><span class="etiqueta">USUARIO:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($compra->usuario_nombres." ".$compra->usuario_apellidos);?></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">TIPO COMPROBANTE PAGO:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($compra->comprobante_pago_tipo);?></span></td>
					
				</tr>
				<tr>
					<td><span class="etiqueta">PROVEEDOR:</span></td>
					<td width="10px"></td>
					<td class="td_valor" colspan="5" align="center"><span class="valor"><?php echo $compra->razon_social;?></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">PROVEEDOR N.DOC:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($compra->proveedor_tipo_documento.": ".$compra->nro_documento);?></span></td>
					
				</tr>
				<tr>
					<td><span class="etiqueta">MONTO NETO S/.:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><b><span class="valor_3"><?php echo number_format($compra->monto_neto_mn, 2);?></span></b></td>
					<td width="50px"></td>
					<td><span class="etiqueta">I.G.V S/.:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><b><span class="valor_3"><?php echo number_format($compra->monto_impuesto_mn, 2);?></span></b></td>
					<td width="50px"></td>
					<td><span class="etiqueta">RETENCION S/.:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><b><span class="valor_3"><?php echo number_format($compra->monto_percepcion_mn, 2);?></span></b></td>
				</tr>
				<tr>
					<td><span class="etiqueta">MONTO TOTAL S/.:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center"><b><span class="valor_3"><?php echo number_format($compra->monto_total_mn, 2);?></span></b></td>
					<td width="50px"></td>
					<td><span class="etiqueta">NRO.COMP:</span></td>
					<td width="10px"></td>
					<td class="td_valor" align="center" ><b><span class="valor_3"><?php echo $compra->nro_comprobante;?></span></b></td>
					<td colspan="2"></td>
					<td><span id="etiqueta_operacion" class="compra_operacion"><?php  echo $mostrar=="opciones"? "OPCIÓN:" : "";?></span></td>
					<td></td>
					<td>
						<select id="opcion" class="compra_operacion" style="display:<?php echo $mostrar=="opciones"? "block;" : "none;";?>">
							<option value="0">Seleccione...</option>
							<option value="1">Generar Ingreso Almacen</option>
						</select>
					</td>
				</tr>
				
			</table>
			<div id="div_almacen_destino" class="div_opcion">
				<table>
					<tr>
					<td><span id="etiqueta_almacen_destino" >ALM. DESTINO:</span></td>
					<td width="10px"></td>
					<td>
						<select id="id_almacen_destino" name="id_almacen_destino" class="almacen_destino">
							<option value="0">Seleccione...</option>
							<?php
							//echo "ID Usuario: $id_usuario - $id_centro</br>";
							$lista = $almBLO->ListarAlmacenEntradaXIdUsuario($id_usuario, $id_centro);
							if(!is_null($lista))
								foreach($lista as $au)
									echo "<option value=\"$au->id\">".strtoupper($au->almacen)."</option>";
							
							?>
						</select>
					</td>
					<td>
						<input type="button" id="ingresar_almacen" class="texto_2" value="Ingresar" />
					</td>
				</tr>
				</table>
			</div>
			
		</div>
		<div id="div_lista_productos">
			<div id="div_titulo">
				<span id="titulo">LISTA DE PRODUCTOS</span>
			</div>
			<table id="tabla_lista_productos">
				<thead>
					<th width=20px class="cabecera" align="center">#</th>
					<th width=250px class="cabecera" align="center"><b>Categoría</b></th>
					<th width=250px class="cabecera" align="center"><b>Producto</b></th>
					<th width=100px class="cabecera" align="center"><b>Marca</b></th>
					<th width=60px class="cabecera" align="center"><b>Cantidad</b></th>
					<th width=75px align="center" class="cabecera"><b>P.Neto.U. S/.</b></th>
					<th width=75px align="center" class="cabecera"><b>I.G.V. U. S/.</b></th>
					<th width=75px align="center" class="cabecera"><b>P.Total.U. S/.</b></th>
					<th width=75px class="cabecera" align="center"><b>P.Total S/.</b></th>
				</thead>
				<tbody>
				<?php
				if(!is_null($compra))
				{
					$lista = $comBLO->ListarItemsXIdCompra($compra->id);
					if(!is_null($lista))
					{
						$cont = 1;
						foreach($lista as $i)
						{?>
						<tr height=25px>
							<td align="center"><?php echo $cont;?></td>
							<td><?php echo strtoupper($i->producto_categoria);?></td>
							<td><b><?php echo strtoupper($i->descripcion_corta);?></b></td>
							<td align="center"><?php echo strtoupper($i->marca);?></td>
							<td align="center"><?php echo $i->cantidad;?></td>
							<td align="center"><?php echo number_format($i->precio_neto_unitario_mn, 2);?></td>
							<td align="center"><?php echo number_format($i->impuesto_unitario_mn, 2);?></td>
							<td align="center"><?php echo number_format($i->precio_total_unitario_mn, 2);?></td>
							<td align="center"><b><?php echo number_format($i->precio_total_unitario_mn * $i->cantidad, 2);?></b></td>
						</tr>
						<?php
						$cont++;		
						}
					}
				}
				?>
				</tbody>
			</table>
		</div>
		
	</form>
	</div>
	
	</body>
</html>