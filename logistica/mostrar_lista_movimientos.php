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
include ("../clases/movimiento.php");
include ("../clases/anuncio.php");

if(isset($_GET["lista"]))
	$lista = $_GET["lista"];
else 
	$lista = "";

$opcBLO = new OpcionBLO();
$movBLO = new MovimientoBLO();

//$enlace_procesar = "../procesar_compra.php?id_centro=$id_centro&opcion_original_key=$opcion_original&usr_key=$usr_key";

$opcion_ver_movimiento_otro_usuario = "FC3L0K36";
$opcion_ver_movimiento = "L094VYF8";
$permiso_ver_movimiento = $opcBLO->ValidarOpcionXIdUsuario($opcion_ver_movimiento_otro_usuario, $id_usuario, $id_centro);


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
			
			#div_tabla_lista_movimientos { width: 1090px; border-collapse: collapse;  }
			#tabla_lista_movimientos thead th { color: #0099CC; font-size: 12px; font-family: Helvetica; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_movimientos tbody td { color: #585858; font-size: 11px; font-family: Helvetica; border-bottom: dotted 1px #0099CC; }
			
			#tabla_lista_movimientos tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_movimientos tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_movimientos { border-collapse: collapse; }
			
			#titulo { font-weight: bold; font-size: 14px; color: #585858; font-family: Helvetica;  }
			
			#div_titulo { margin-bottom: 15px;}
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
			.texto_2, .movimiento_operacion{ width: 100px; text-align: center; font-size: 11px; }
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
		
		function Redireccionar(opcion_key, movimiento_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro&op_original_key=$opcion_key&movimiento_key"; ?>" + movimiento_key;
		}
		
		
		$(function()
		{
			$(".movimiento_operacion").live("change", function()
			{
				$operacion = $(this).val();
				$fila = $(this).parent().parent();
				$movimiento_key = $fila.find(".movimiento_key").val();
				
				if($operacion == 1)
				{
					$url = "ver_movimiento.php?opcion_key=<?php echo "$opcion_ver_movimiento&usr_key=$usr_key&id_centro=$id_centro";?>&movimiento_key=" + $movimiento_key;
					//alert($url);
					window.open($url, "Movimiento [" + $movimiento_key +"]");
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
			<div id="div_tabla_lista_movimientos">
				<input type="hidden" id="permiso_ver_movimiento" value="<?php echo $permiso_ver_movimiento->isOK;?>"/>
				<div id="div_titulo"><span id="titulo">LISTA DE MOVIMIENTOS DE ALMACEN</span></div>
				<table id="tabla_lista_movimientos">
					<thead>
						<th width=20px>Id</th>
						<th width=60px>Codigo</th>
						<th width=120px>Motivo Mvto</th>
						<th width=115px>Fecha</th>
						<th width=80px>Usuario</th>
						<th width=120px>Almacen Origen</th>
						<th width=120px>Almacen Destino</th>
						<th width=220px>Comentarios</th>
						<th width=80px>Cod.Compra</th>
						<th>Operación</th>
					</thead>
					<tbody>
					<?php
					$lista_movimientos = $movBLO->ListarXIdCentro($id_centro);
					if(!is_null($lista_movimientos))
					{
						$i = 1;
						foreach($lista_movimientos as $m)
						{
							$fecha_hora = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($m->fecha_hora))));
							
						?>
						<tr>
							<td align="center"><?php echo $i;?></td>
							<td align="center"><input class="movimiento_key" type="hidden" value="<?php echo $m->movimiento_key;?>" /><?php echo $m->movimiento_key;?></td>
							<td align="center"><?php echo strtoupper($m->motivo);?></td>
							<td align="center"><?php echo $fecha_hora;?></td>
							<td align="center"><?php echo $m->usuario;?></td>
							<td align="center"><?php echo strtoupper($m->almacen_origen);?></td>
							<td align="center"><?php echo strtoupper($m->almacen_destino);?></td>
							<td><?php echo $m->comentarios;?></td>
							<td align="center"><?php echo $m->compra_key;?></td>
							<td>
								<select class="movimiento_operacion">
									<option value="0">Seleccione...</option>
									
									<?php
									if($m->id_usuario == $id_usuario || $permiso_ver_movimiento->isOK)
										$disabled = "";
									else
										$disabled = "disabled='disabled'";
									?>
									<option value="1" <?php echo $disabled;?>>Ver Movimiento</option>
								</select>
							</td>
						</tr>		
						<?php
						$i++;
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>