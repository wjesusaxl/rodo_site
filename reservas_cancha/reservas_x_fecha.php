<?php

session_start();

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

$precio_turno_dia = 30;
$precio_turno_noche = 40;

include ('../clases/enc_dec.php');
include ('../clases/general.php');
include ('../clases/usuario.php');
include ('../clases/centro.php');
include ('../clases/opcion.php');
include ('../clases/security.php');
include ("../clases/reserva_cancha.php");
include ("../clases/anuncio.php");

$opcBLO = new OpcionBLO();

$opcion_ver_detalle_reserva_cancha = "D01BO62A";
$permiso_ver_detalle_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_ver_detalle_reserva_cancha, $usuario -> id, $id_centro);

$fecha_inicio = "";
$fecha_fin = "";

if(isset($_POST["fecha_inicio"]))
	$fecha_inicio = $_POST["fecha_inicio"];

if(isset($_POST["fecha_fin"]))
	$fecha_fin = $_POST["fecha_fin"];

$mostrar = "display:none;";

$enlace_post = "reservas_x_fecha.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";

if($fecha_inicio != "" && $fecha_fin != "")
{
	
	$fecha_inicio_str = date("d-m-Y", strtotime( date('Y-m-d', strtotime($fecha_inicio)) ));
	$fecha_fin_str = date("d-m-Y", strtotime( date('Y-m-d', strtotime($fecha_fin)) ));
	
	$resBLO = new ReservaCanchaBLO();
	
	$fecha_inicio = $fecha_inicio." 00:00:00";
	$fecha_fin = $fecha_fin." 23:59:59";
	
	$lista_reservas = $resBLO->ListarReservaXFechaIniyFechaFin($id_centro, $fecha_inicio, $fecha_fin);
	
	if(!is_null($lista_reservas))
	{
		$mostrar = "display:block;";
		$lista_fechas = array();
		$fecha_str = date("Y-m-d", strtotime(date('Y-m-d H:i:s', strtotime($lista_reservas[0]->fecha_hora_inicio))));
		$lista_fechas[] = $fecha_str; 
			
		foreach($lista_reservas as $r)
		{
			$fecha_str = date("Y-m-d", strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_inicio))));
			$nro_fechas = count($lista_fechas);
			if($fecha_str > $lista_fechas[$nro_fechas - 1])
				$lista_fechas[] = $fecha_str;
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
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		<style type="text/css">
		
			
			body { background-color: #F1F1F1; }
			#div_main {  width: 1200px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
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
			
			#titulo { font-weight: bold; font-size: 14px; color: #0099CC; font-family: Helvetica;  }
			
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
			
			#div_tabla_reservas { width: 1100px; margin-top: 20px;}
			
			#tabla_reservas { border: dotted 1px #0099C; border-radius: 10px 10px 10px 10px; border-collapse: collapse; }
			#tabla_reservas thead th { color: #0099CC; font-size: 14px; border-bottom: dotted 1px #0099CC; }
			#tabla_reservas tbody tr { color: #585858; font-size: 11px; font-family: Helvetica;}
			
			#tabla_reservas tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_reservas tbody tr:nth-child(even) { background-color:#FFFFFF; }
			#tabla_reservas tbody tr:hover { background-color: #F5FCA6; }
			
			.fila_total_dia { background-color: #0099CC; border-top: solid 1px #0099CC; border-bottom: solid 1px #0099CC;}
			
			.estado_1 { color: green; }
			.estado_0 { color: red; font-weight: bold;}
			
			.lbl_reserva_key { color: #0099CC; text-decoration: underline; }
			.lbl_reserva_key:hover { cursor: pointer;}
			
			.div_mod { width: 10px; height: 10px; background-color: #FE1E1E; border-radius: 5px;}
			
		</style>
		
		
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key . "&id_centro=$id_centro"; ?>";
		}
		
		$(function()
		{
			$fecha = new Date();
			
			$('#fecha_inicio_str').datepicker({
				//minDate: 0,
				dateFormat: 'dd-mm-yy', 
				altField: '#fecha_inicio', 
				altFormat: 'yy-mm-dd',
				onSelect: function(selected)
					{
          				$("#fecha_fin_str").datepicker("option","minDate", selected)
        			}
				});
			$("#fecha_inicio_str").datepicker("setDate", $fecha);
			
			$('#fecha_fin_str').datepicker({
				//minDate: 0,
				dateFormat: 'dd-mm-yy', 
				altField: '#fecha_fin', 
				altFormat: 'yy-mm-dd',
        		onSelect: function(selected) 
        			{
						$("#fecha_inicio_str").datepicker("option","maxDate", selected)
        			}
				});
			$("#fecha_fin_str").datepicker("setDate", $fecha);
			
			$(".lbl_reserva_key").live("click",function()
			{
				<?php
				
				if($permiso_ver_detalle_reserva_cancha->isOK)
				{?>
					$td = $(this).parent();
					$reserva_key = $td.find(".reserva_key").val();
					link = "<?php echo "../redirect.php?opcion_key=$opcion_ver_detalle_reserva_cancha&usr_key=$usr_key&id_centro=$id_centro&reserva_key=";?>" + $reserva_key;
					window.open(link, "Reserva[" + $reserva_key + "]");
				<?php	
				}?>
				
			});
			
			
			$("#btn_mostrar").click(function()
			{
				$("#reporte").submit();
			});
			
			<?php
			if($fecha_inicio != "")
			{?>
				$("#fecha_inicio_str").datepicker("setDate","<?php echo $fecha_inicio_str;?>");
			<?php		
			}
			if($fecha_fin != "")
			{?>
				$("#fecha_fin_str").datepicker("setDate","<?php echo $fecha_fin_str;?>");
			<?php	
			}
			?>
			
			
			
			
		});
		
			
		</script>
	</head>
	<body>		
	<?php
		include ("../header.php");
	?>
	<div id="div_main" align="center">
		<form id="reporte" action="<?php echo $enlace_post;?>" method="POST">
			
			<div id="div_titulo">
				<span id="titulo">RESERVAS DE CANCHA POR FECHAS</span>
			</div>
			<div id="div_info" align="center">
				<table id="tabla_info">
					<tr>
						<td><span class="etiqueta">Fecha Inicio:</span></td>
						<td width="10px"></td>
						<td class="td_valor" align="center">
							<input type="text" class="texto_2" id="fecha_inicio_str" readonly="readonly"/>
							<input type="hidden" class="texto_2" id="fecha_inicio" name="fecha_inicio"/>
						</td>
						<td width="50px"></td>
						<td><span class="etiqueta">Fecha Fin:</span></td>
						<td width="10px" align="center"></td>
						<td class="td_valor" align="center">
							<input type="text" class="texto_2" id="fecha_fin_str" readonly="readonly"/>
							<input type="hidden" class="texto_2" id="fecha_fin" name="fecha_fin"/>
						</td>
						<td width="50px"></td>
						<td><input type="button" class="texto_2" value="Mostrar" id="btn_mostrar"></td>
					</tr>
				</table>
			</div>
			<div id="div_tabla_reservas" style="<?php echo $mostrar;?>">
				<table id="tabla_reservas">
					<thead>
						<tr>
							<th width=20px></th>
							<th width=30px>Id</th>							
							<th width=120px>Fecha Creación</th>
							<th width=80px>Usuario</th>
							<th width=250px>Cliente</th>
							<th width=60px>Fecha</th>
							<th width=60px>Inicio</th>
							<th width=60px>FIn</th>
							<th width=80px>Adelanto S/.</th>
							<th width=80px>Total S/.</th>
							<th width=80px>Recibido S/.</th>
							<th width=60px>Duración</th>
							<th width=120px>Estado</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$total_fechas_mn = 0;
					$total_transacciones_fechas_mn = 0;
					$duracion_fechas = 0;
					
					if(count($lista_reservas) > 0)
						foreach($lista_fechas as $f)
						{
							$total_dia_mn = 0;
							$duracion_dia = 0;
							$mostrar_fila = FALSE;
							$total_transacciones_dia_mn = 0;
							
							foreach($lista_reservas as $r)
							{
								$fecha_str = date("Y-m-d", strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_inicio))));
								$nro_dia = date("w", strtotime( date('Y-m-d', strtotime($f)) ));
								
								switch($nro_dia)
								{
									case 0: $nombre_dia = "Domingo"; break;
									case 1: $nombre_dia = "Lunes"; break;
									case 2: $nombre_dia = "Martes"; break;
									case 3: $nombre_dia = "Miércoles"; break;
									case 4: $nombre_dia = "Jueves"; break;
									case 5: $nombre_dia = "Viernes"; break;
									case 6: $nombre_dia = "Sábado"; break;
								}
								
								
								if($f == $fecha_str && $r->estado != 4)
								{
									$mostrar_fila = TRUE;
									$duracion_reserva = 0;
									$duracion = 0;
									
									$flag_modificacion = FALSE;
									$lista_modificaciones = $resBLO->ListarHistoriaReservaModificada($r->id);
									if(!is_null($lista_modificaciones))
										if(count($lista_modificaciones)>1)
											$flag_modificacion = TRUE;
									
									$total_transacciones_mn = 0;
									$lista_transacciones = $resBLO->ListarTransaccionesXIdReserva($r->id);
									if(!is_null($lista_transacciones))
										foreach($lista_transacciones as $tx)
											if(!$tx->flag_anulado)
												$total_transacciones_mn += $tx->monto_total_mn;
											
									if($total_transacciones_mn == 0)
										$total_transacciones_mn = $r->pago_adelantado;
									
									$total_transacciones_dia_mn += $total_transacciones_mn;
									$total_transacciones_fechas_mn += $total_transacciones_mn;
										
									
									$fecha_creacion = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_registro))));
									$fecha = date("d-m-Y", strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_inicio))));
									
									$hora_00 = strtotime($fecha_str." 00:00:00");
									$hora_08 = strtotime($fecha_str." 08:00:00");
									$hora_18 = strtotime($fecha_str." 18:00:00");
									$hora_inicio = strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_inicio)));
									$hora_fin = strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_fin)));
									
									$hora_inicio1 = strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_inicio)));
									$hora_fin1 = strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_fin)));
									
									if($hora_fin >= $hora_00 && $hora_fin <= $hora_08)
									{
										$duracion = round(($hora_fin - $hora_inicio) / 3600, 1);
										$total_mn = $duracion * $precio_turno_noche;
										
										$duracion_reserva = $duracion;
										
									}
																			
									if($hora_fin >= $hora_08 && $hora_fin <= $hora_18)
									{
										if($hora_inicio < $hora_08)
										{
											$duracion = round(($hora_08 - $hora_inicio) / 3600, 1);
											$total_mn = $duracion * $precio_turno_noche;
											$duracion_reserva += $duracion;
											
											$duracion = round(($hora_fin - $hora_08) / 3600, 1);
											$total_mn = $total_mn + ($duracion * $precio_turno_dia);
											$duracion_reserva += $duracion;
										}
										else 
										{
											$duracion = round(($hora_fin - $hora_inicio) / 3600, 1);
											$total_mn = $duracion * $precio_turno_dia;
											$duracion_reserva += $duracion;
										}
										
										$duracion2 = "Grupo 2";
									}
									
									if($hora_fin >= $hora_18)
									{
										if($hora_inicio < $hora_08)
										{
											$duracion = round(($hora_08 - $hora_inicio) / 3600, 1);
											$total_mn = $duracion * $precio_turno_noche;
											$duracion_reserva += $duracion;
											
											$duracion = round(($hora_18 - $hora_08) / 3600, 1);
											$total_mn = $total_mn + ($duracion * $precio_turno_dia);
											$duracion_reserva += $duracion;
											
											$duracion = round(($hora_inicio - $hora_fin) / 3600, 1);
											$total_mn = $total_mn + ($duracion * $precio_turno_noche);
											$duracion_reserva += $duracion;
												
										}
										else 
										{
											if($hora_inicio < $hora_18)
											{
												$duracion = round(($hora_18 - $hora_inicio) / 3600, 1);
												$total_mn = $duracion * $precio_turno_dia;
												$duracion_reserva += $duracion;	
												
												$duracion = round(($hora_fin - $hora_18) / 3600, 1);
												$total_mn = $total_mn + ($duracion * $precio_turno_noche);
												$duracion_reserva += $duracion;
											}
											else
											{
												$duracion = round(($hora_fin - $hora_inicio) / 3600, 1);
												$total_mn = $duracion * $precio_turno_noche;
												$duracion_reserva += $duracion;
											}	
										}
										
									}

									if($r->estado == 5)
									{
										$total_mn = 0;
										$duracion = 0;
									}

									
									
									$hora_inicio = date("h:i A", strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_inicio))));
									$hora_fin = date("h:i A", strtotime(date('Y-m-d H:i:s', strtotime($r->fecha_hora_fin))));
									$total_dia_mn += $total_mn;
									$duracion_dia += $duracion;
									$total_fechas_mn += $total_mn;
									$duracion_fechas += $duracion;
									
									if($r->estado == 5)
										$class_estado = "estado_0";
									else
										$class_estado = "estado_1";
									
									?>
									<tr>
										<td align="center">
											<?php
											if($flag_modificacion)
											{?>
												<div class="div_mod"></div>
											<?php
											}?>
										</td>
										<td align="center">											
											<span class="lbl_reserva_key" title="Ver Reserva"><?php echo $r->id;?></span>
											<input class="reserva_key" type="hidden" value="<?php echo $r->auto_key;?>"/>
										</td>
										<td align="center"><?php echo $fecha_creacion;?></td>
										<td align="center"><?php echo $r->usuario_creacion;?></td>
										<td><?php echo $r->cliente_nombres_apellidos;?></td>
										<td align="center"><?php echo $fecha;?></td>
										<td align="center"><?php echo $hora_inicio;?></td>
										<td align="center"><?php echo $hora_fin;?></td>
										<td align="center"><?php echo "S/. ".number_format($r->pago_adelantado, 2);?></td>
										<td align="center"><?php echo "S/. ".number_format($total_mn, 2);?></td>
										<td align="center"><?php echo "S/. ".number_format($total_transacciones_mn, 2);?></td>
										<td align="center"><?php echo $duracion_reserva."h";?></td>
										<td align="center"><?php echo "<span class=\"$class_estado\">".strtoupper($r->estado_descripcion)."</span>";?></td>
									</tr>
									<?php
									
								}	
							}
							if($mostrar_fila)
							{?>
							<tr class="fila_total_dia">
								<td></td>
								<td  colspan=8><b><?php echo "Total del día $nombre_dia $fecha";?></b></td>
								<td align="center"><b><?php echo "S/. ".number_format($total_dia_mn, 2);?></b></td>
								<td align="center"><b><?php echo "S/. ".number_format($total_transacciones_dia_mn, 2);?></b></td>
								<td align="center"><b><?php echo $duracion_dia."h";?></b></td>
								<td></td>
								
							</tr>	
								
							<?php
							}	
						}?>
						<tr>
							
							<td colspan=8></td>
							<td align=right><b>TOTAL</b></td>
							<td align="center"><b><?php echo "S/. ".number_format($total_fechas_mn, 2);?></b></td>
							<td align="center"><b><?php echo "S/. ".number_format($total_transacciones_fechas_mn, 2);?></b></td>
							<td align="center"><b><?php echo $duracion_fechas."h";?></b></td>
							<td></td>
							
						</tr>
					</tbody>
				</table>
			</div>
		</form>
	</div>
	
	</body>
</html>


