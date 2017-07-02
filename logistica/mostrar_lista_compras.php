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
include ("../clases/movimiento.php");
include ("../clases/anuncio.php");

if(isset($_GET["lista"]))
	$lista = $_GET["lista"];
else 
	$lista = "";

$opcBLO = new OpcionBLO();
$comBLO = new CompraBLO($id_centro);
$almBLO = new AlmacenBLO();
$movBLO = new MovimientoBLO();
$opcBLO = new OpcionBLO();

$enlace_procesar = "../procesar_compra.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";

$opcion_ver_compra = "7QC1PD71";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		
		<style type="text/css">
			body { background-color: #F1F1F1; }
			#div_main {  width: 1100px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
			border-radius: 10px 10px 10px 10px;}
			
			.sin_info { font-size: 11px; color:#585858; font-family: Helvetica; padding-left: 4px; }
			.etiqueta { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica; }
			
			#div_tabla_lista_compras { width: 1090px; border-collapse: collapse;  }
			#tabla_lista_compras thead th { color: #0099CC; font-size: 12px; font-family: Helvetica; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_compras tbody td { color: #585858; font-size: 11px; font-family: Helvetica; border-bottom: dotted 1px #0099CC; }
			
			#tabla_lista_compras tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_compras tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }
			
			#titulo { font-weight: bold; font-size: 14px; color: #585858; font-family: Helvetica;  }
			
			#div_titulo { margin-bottom: 15px;}
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
			.texto_2, .compra_operacion{ width: 100px; text-align: center; font-size: 11px; }
			.texto_3 { width: 150px; text-align: center; font-size: 11px; }
			.texto_3_5 { width: 180px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4_5 { width: 230px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_10 { width: 400px; text-align: center; font-size: 11px; }
			
				
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		
		$(function()
		{
			$(".compra_operacion").live("change", function()
			{
				$opcion = $(this).val();
				$fila = $(this).parent().parent();
				$compra_key = $fila.find(".compra_key").val();
				
				if($opcion == 1)
				{
					$url = "ver_compra.php?opcion_key=<?php echo "$opcion_ver_compra&usr_key=$usr_key&id_centro=$id_centro";?>&compra_key=" + $compra_key;
					//alert($url);
					window.open($url, "Compra [" + $compra_key +"]");
				}
				
				if($opcion == 2)
				{
					$id_compra = $fila.find(".id_compra").val();
					$("#operacion").val("ingresar_almacen");
					$("#id_compra").val($id_compra);
					$("#compra").submit();
				}
				
			});
			
		});
			
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
	<form id="compra" method="POST" action="<?php echo $enlace_procesar; ?>">
		<input type="hidden" id="id_compra" name="id_compra" />
		<input type="hidden" id="operacion" name="operacion" />
		<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>" />
		<div id="div_tabla_lista_compras">
			<div id="div_titulo"><span id="titulo">LISTA DE COMPRAS</span></div>
			<table id="tabla_lista_compras">
				<thead>
					<th width=20px>Id</th>
					<th width=60px>Codigo</th>
					<th width=150px>Tipo</th>
					<th width=60px>Fecha</th>
					<th width=80px>Usuario</th>
					<th width=200px>Proveedor</th>
					<th width=70px>RUC/DNI</th>
					<th width=100px>Comprobante</th>
					<th width=60px>M.Total S/.</th>
					<th width=90px>Estado</th>
					<th width=80px>Operación</th>
				</thead>
				<tbody>
				<?php
				$lista_compras = $comBLO->ListarTodos($id_centro);
				if(!is_null($lista_compras))
				{
					foreach($lista_compras as $c)
					{
						//$fecha_hora = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($c->fecha))));
						$fecha_hora = date("d-m-Y", strtotime(date('Y-m-d H:i:s', strtotime($c->fecha))));
						
						$almacenes = $almBLO->ListarAlmacenEntradaXIdUsuario($id_usuario, $id_centro);
						$html_opcion = "";
						
						if(!is_null($c->id_movimiento))
							$estado = "Ingresado";
						else
							$estado = "Por Ingresar";
					
						if($c->flag_anulada)
							$estado = "Anulada";
						
						$html_opcion = "";
						
						if($opcBLO->ValidarOpcionXIdUsuario($opcion_ver_compra, $id_usuario, $id_centro));
							$html_opcion = $html_opcion."<option value=\"1\">Ver Compra</option>\n";
						
						if(count($almacenes) > 0 && is_null($c->id_movimiento))
							$html_opcion = $html_opcion."<option value=\"2\">Ingresar a Almacen</option>\n";
					?>
					<tr>
						<td align="center"><?php echo $c->id;?></td>
						<td align="center">
							<input class="compra_key" type="hidden" value="<?php echo $c->compra_key;?>" />
							<input class="id_compra" type="hidden" value="<?php echo $c->id;?>" />
							<?php echo $c->compra_key;?>
						</td>
						<td align="center"><?php echo strtoupper($c->compra_tipo);?></td>
						<td align="center"><?php echo $fecha_hora;?></td>
						<td align="center"><?php echo $c->usuario;?></td>
						<td><?php echo $c->nombre_comercial;?></td>
						<td align="center"><?php echo $c->nro_documento;?></td>
						<td align="center"><?php echo $c->nro_comprobante;?></td>
						<td align="center"><b><?php echo number_format($c->monto_total_mn, 2);?></b></td>
						<td align="center"><b><?php echo strtoupper($estado);?></b></td>
						<td>
							<select class="compra_operacion">
								<option value="0">Seleccione...</option>
								
								<?php echo $html_opcion;?>
							</select>
						</td>
					</tr>		
					<?php
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