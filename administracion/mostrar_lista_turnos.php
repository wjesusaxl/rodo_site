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
include ("../clases/turno_atencion.php");
include ("../clases/anuncio.php");

if(isset($_GET["lista"]))
	$lista = $_GET["lista"];
else 
	$lista = "";

$taBLO = new TurnoAtencionBLO();
$opcBLO = new OpcionBLO();

$enlace_procesar = "../procesar_compra.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";

$opcion_ver_turno = "D1U76AV5";
$opcion_ver_turno_otro_usuario = "3IR964RJ";
$permiso_ver_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_ver_turno_otro_usuario, $id_usuario, $id_centro);

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
			#div_main {  width: 1150px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
			border-radius: 10px 10px 10px 10px;}
			
			.sin_info { font-size: 11px; color:#585858; font-family: Helvetica; padding-left: 4px; }
			.etiqueta { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica; }
			
			#div_tabla_lista_turnos { width: 1140px; border-collapse: collapse;  }
			#tabla_lista_turnos thead th { color: #0099CC; font-size: 12px; font-family: Helvetica; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_turnos tbody td { color: #585858; font-size: 11px; font-family: Helvetica; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_turnos { border-collapse: collapse; }
			
			#tabla_lista_turnos tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_turnos tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }
			
			#titulo { font-weight: bold; font-size: 14px; color: #0099CC; font-family: Helvetica; }
			
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
			$(".turno_operacion").live("change", function()
			{
				$opcion = $(this).val();
				$fila = $(this).parent().parent();
				$turno_key = $fila.find(".turno_key").val();
				
				if($opcion == 1)
				{
					$url = "ver_turno.php?opcion_key=<?php echo "$opcion_ver_turno&usr_key=$usr_key&id_centro=$id_centro";?>&turno_atencion_key=" + $turno_key;
					window.open($url, "Compra [" + $turno_key +"]");
				}
				
				$(this).val(0);
				
				
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
		<input type="hidden" id="operacion" name="operacion" />
		<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>" />
		<div id="div_tabla_lista_turnos">
			<div id="div_titulo"><span id="titulo">LISTA DE TURNOS DE ATENCIÓN</span></div>
			<table id="tabla_lista_turnos">
				<thead>
					<th width=20px>#</th>
					<th width=60px>Codigo</th>
					<th width=160px>Caja</th>
					<th width=80px>Almacen</th>
					<th width=80px>Usuario</th>
					<th width=115px>Fecha Apertura</th>
					<th width=115px>Fecha Cierre</th>
					<th width=80px>Saldo Inicial</th>
					<th width=80px>Tot.Ingresos</th>
					<th width=80px>Tot.Egresos</th>
					<th width=80px>Tot.Turno</th>
					<th width=60px>Estado</th>
					<th width=60px>Operacion</th>
				</thead>
				<tbody>
				<?php
				if($permiso_ver_turno->isOK)
					$lista_turnos = $taBLO->ListarTurnosXIdCentro($id_centro);
				else
					$lista_turnos = $taBLO->ListarTurnosXIdUsuario($id_usuario, $id_centro);
				if(!is_null($lista_turnos))
				{
					$i = 1;
					foreach($lista_turnos as $ta)
					{
						$fecha_hora_cierre = "--";
						$fecha_hora_apertura = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($ta->fecha_hora_inicio))));
						
						if(!is_null($ta->fecha_hora_fin))
							$fecha_hora_cierre = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($ta->fecha_hora_fin))));
						 
					?>
					<tr>
						<td align="center"><?php echo $i;?></td>
						<td align="center">
							<input class="turno_key" type="hidden" value="<?php echo $ta->auto_key;?>" />
							<input class="id_turno_atencion" type="hidden" value="<?php echo $ta->id;?>" />
							<b><?php echo $ta->codigo;?></b>
						</td>
						<td align="center"><?php echo strtoupper($ta->caja);?></td>
						<td align="center"><?php echo strtoupper($ta->almacen);?></td>
						<td align="center" title="<?php echo $ta->usuario_nombres_apellidos;?>"><b><?php echo strtoupper($ta->usuario);?></b></td>
						<td align="center"><?php echo $fecha_hora_apertura;?></td>
						<td align="center"><?php echo $fecha_hora_cierre;?></td>
						<td align="center"><?php echo "S/. ".number_format($ta->saldo_inicial_mn, 2);?></td>
						<td align="center"><?php echo is_null($ta->total_ingreso_efectivo_mn) ? "--" : "S/. ".number_format($ta->total_ingreso_efectivo_mn, 2);?></td>
						<td align="center"><?php echo is_null($ta->total_egreso_efectivo_mn) ? "--" : "S/. ".number_format($ta->total_egreso_efectivo_mn, 2);?></td>
						<td align="center"><b><?php echo is_null($ta->total_transacciones_mn) ? "--" : "S/. ".number_format($ta->total_transacciones_mn, 2);?></b></td>
						<td align="center"><b><?php echo strtoupper($ta->estado);?></b></td>
						<td>
							<select class="turno_operacion texto_2">
								<option value="0">Seleccione...</option>
								<option value="1">Ver Turno</option>
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
	</form>
	</div>
	
	</body>
</html>