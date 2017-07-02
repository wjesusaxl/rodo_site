<?php

session_start();
date_default_timezone_set("America/Lima");

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

include ('../clases/enc_dec.php');
include ('../clases/general.php');
include ('../clases/usuario.php');
include ('../clases/centro.php');
include ('../clases/opcion.php');
include ('../clases/security.php');
include ('../clases/reserva_cancha.php');

if(isset($_GET["reserva_key"]))
	$reserva_key = $_GET["reserva_key"];
else 
	$reserva_key = "";
	
$opcBLO = new OpcionBLO();
$rcBLO = new ReservaCanchaBLO();

if($reserva_key != "")
{
	
	$reserva = $rcBLO->RetornarXKey($reserva_key);
	if(!is_null($reserva))
	{
		$fecha = date("d-m-Y", strtotime(date('Y-m-d H:i:s', strtotime($reserva->fecha_hora_inicio ))));
		$fecha_hora_inicio = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($reserva->fecha_hora_inicio ))));
		$fecha_hora_fin = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($reserva->fecha_hora_fin ))));
		$fecha_hora_registro = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($reserva->fecha_hora_registro ))));
		
		$id_reserva = $reserva->id;
		$pago_adelantado = $reserva->pago_adelantado;		
	}
	else
		$id_reserva = 0;
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
			#div_main {  width: 1150px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
			border-radius: 10px 10px 10px 10px;}
			
			.sin_info { font-size: 11px; color:#585858; font-family: Helvetica; padding-left: 4px; }
			.etiqueta { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica;}
			.valor { font-size: 12px; color:#585858; font-family: Helvetica; }
			.valor_2 { font-size: 11px; color:#585858; font-family: Helvetica;color: #0099CC; }
			.valor_3 { font-size: 13px; color:#585858; font-family: Helvetica;color: #0099CC; font-weight: bold; }
			.td_valor { border-bottom: dotted 1px #0099CC;}
			
			#titulo { font-weight: bold; font-size: 14px; color: #585858; font-family: Helvetica;  }
			.lbl_titulo { font-weight: bold; font-size: 13px; color: #0099CC; font-family: Helvetica; }
			
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
			
			#div_info { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 1050px; padding-top: 10px; padding-bottom: 10px; }
			
			.div_opcion { border: dotted 1px #0099CC; font-family: Helvetica; border-radius: 10px 10px 10px 10px; display: none; }
			#div_almacen_destino { width: 380px}
			
			#lbl_titulo { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; text-shadow: 1px 1px 1px #333; }
			#tabla_info { border-collapse: collapse; }
			
			#div_lista_transacciones { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 690px; padding-top: 5px; padding-bottom: 5px; margin-top: 10px; margin-left: 12px; }
			#tabla_lista_transacciones { font-family: Helvetica; border-collapse: collapse; margin-top: 10px; }
			#tabla_lista_transacciones thead { font-size: 12px; color: #0099CC; font-weight: bold; }
			#tabla_lista_transacciones tbody tr { font-size: 11px; }
			#tabla_lista_transacciones tbody td { border-top: dotted 1px #0099CC; color: #585858; }
			#tabla_lista_transacciones tbody tr:nth-child(even) { background-color:#DAF1F7; }
			#tabla_lista_transacciones tbody tr:nth-child(odd) { background-color:#FFFFFF; }
			
			#tabla_lista_transacciones tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_lista_transacciones tbody tr:nth-child(even) { background-color:#FFFFFF; }
			
			#div_reserva_historia { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; padding-top: 5px; padding-bottom: 5px; margin-top: 10px; margin-left: 12px; width: 1110px; }
			#tabla_reserva_historia { font-family: Helvetica; border-collapse: collapse; margin-top: 10px; margin: 5px 5px 5px 5px;  }
			#tabla_reserva_historia thead { font-size: 12px; color: #0099CC; font-weight: bold; }
			#tabla_reserva_historia tbody tr { font-size: 11px; }
			#tabla_reserva_historia tbody td { border-top: dotted 1px #0099CC; color: #585858; }
			#tabla_reserva_historia tbody tr:nth-child(even) { background-color:#DAF1F7; }
			#tabla_reserva_historia tbody tr:nth-child(odd) { background-color:#FFFFFF; }
			
			#tabla_reserva_historia tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_reserva_historia tbody tr:nth-child(even) { background-color:#FFFFFF; }
			
			#titulo_lista_producto { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; margin-bottom: 10px;}
			#titulo_lista_transacciones{ font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; margin-bottom: 10px;}
			.lbl_2 { color: #0099CC; font-weight: bold; }
			.total_mn { color: #0099CC; font-size: 12px;}
			
		</style>
		
		
		<script type="text/javascript">
		
		$(function()
		{
			
			
		})
		
			
		</script>
	</head>
	<body>		
	
	<div id="div_main" align="center">
		<div id="div_info" align="center">
			<table id="tabla_info">
				<tr><td colspan="15" align="center"><span id="lbl_titulo">DETALLE DE RESERVA DE CANCHA</span></td></tr>
				<tr height="10px"></tr>
				<tr>
					<td><span class="etiqueta">CLIENTE:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center" colspan="5"><span class="valor"><b><?php echo $reserva->cliente_nombres_apellidos;?></b></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">ESTADO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center" width="180px"><span class="valor"><?php echo strtoupper($reserva->estado_descripcion);?></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">USUARIO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center" width="120px"><span class="valor lbl_2" title="<?php echo $reserva->usuario_nombres_apellidos;?>"> <?php echo strtoupper($reserva->usuario_creacion); ?></span></td>					
				</tr>
				<tr>
					<td><span class="etiqueta">FECHA:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo $fecha;?></span></td>
					<td width="50px"></td>
					<td><span class="etiqueta">HORA INICIO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo $fecha_hora_inicio;?></span></td>
					<td></td>
					<td><span class="etiqueta">HORA FIN:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo $fecha_hora_fin;?></span></td>
					<td></td>
					<td><span class="etiqueta">PAGO ADELANTADO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor lbl_2"><?php echo "S/. ".number_format($pago_adelantado, 2);?></span></td>					
				</tr>
				<tr>
					<td><span class="etiqueta">F.CREACION:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center" colspan="3"><span class="valor"><?php echo $fecha_hora_registro;?></span></td>
					<td colspan="10"></td>
				</tr>
				<tr>
					<td><span class="etiqueta">COMENTARIOS:</span></td>
					<td width="5px"></td>					
					<td colspan="13" class="td_valor">
						<span class="valor"><?php echo strtoupper($reserva->comentarios);?></span>
					</td>
					
				</tr>
			</table>
		</div>
		<div id="div_lista_transacciones">
			<span class="lbl_titulo">Lista de Transacciones</span>
			<table id="tabla_lista_transacciones">
				<thead>
					<tr>
						<th width=20px>Id</th>
						<th width=90px>Usuario</th>
						<th width=180px>Motivo</th>
						<th width=80px>Fecha</th>
						<th width=80px>Hora</th>
						<th width=70px>M.Neto</th>
						<th width=70px>Impuesto</th>
						<th width=70px>M.Total</th>
					</tr>					
				</thead>
				<tbody>
				<?php				
				$lista_transacciones = $rcBLO->ListarTransaccionesXIdReserva($reserva->id);				
				if(!is_null($lista_transacciones))
				{
					$total_mn = 0;
					foreach($lista_transacciones as $t)
					{
						$fecha = date("d-m-Y", strtotime(date('Y-m-d H:i:s', strtotime($t->fecha_hora_registro))));
						$hora = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($t->fecha_hora_registro))));
						$total_mn += $t->monto_total_mn;					
					?>
					<tr>
						<td align="center"><?php echo $t->id_transaccion;?></td>
						<td align="center"><?php echo $t->usuario;?></td>
						<td><?php echo strtoupper($t->transaccion_motivo);?></td>
						<td align="center"><?php echo $fecha;?></td>
						<td align="center"><?php echo $hora;?></td>
						<td align="center"><?php echo "S/.".number_format($t->monto_neto_mn, 2);?></td>
						<td align="center"><?php echo "S/.".number_format($t->monto_impuesto_mn, 2);?></td>
						<td align="center"><b><?php echo "S/.".number_format($t->monto_total_mn, 2);?></b></td>
					</tr>
					<?php	
					}?>
					<tr>
						<td colspan="7"></td>						
						<td align="center"><b><span class="total_mn"><?php echo "S/.".number_format($total_mn, 2);?></span></b></td>
					</tr>
				<?php
				}
				
				?>	
				</tbody>
			</table>
		</div>
		<div id="div_reserva_historia">
			<span class="lbl_titulo">Historial de Reserva</span>
			<table id="tabla_reserva_historia">
				<thead>
					<tr>
						<th width=120px>Hora</th>
						<th width=200px>Cliente</th>
						<th width=70px>Fecha</th>
						<th width=60px>Hora Inicio</th>
						<th width=60px>Hora Fin</th>
						<th width=120px>Usuario</th>
						<th width=170px>Estado</th>						
						<th width=300px>Comentarios</th>						
					</tr>					
				</thead>
				<tbody>
				<?php
				$lista_historia = $rcBLO->ListarHistoriaXIdReserva($reserva->id);				
				if(!is_null($lista_historia))
				{
					foreach($lista_historia as $h)
					{
						$fecha_hora_registro = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($h->fecha_hora_registro))));
						$fecha = date("d-m-Y", strtotime(date('Y-m-d H:i:s', strtotime($h->fecha_hora_inicio))));
						$hora_inicio = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($h->fecha_hora_inicio))));
						$hora_fin = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($h->fecha_hora_fin))));										
					?>
					<tr>
						<td align="center"><?php echo $fecha_hora_registro;?></td>
						<td><?php echo strtoupper($h->cliente_nombres_apellidos);?></td>
						<td align="center"><?php echo $fecha;?></td>
						<td align="center"><?php echo $hora_inicio;?></td>
						<td align="center"><?php echo $hora_fin;?></td>
						<td align="center"><?php echo $h->usuario;?></td>
						<td align="center"><?php echo strtoupper($h->estado);?></td>
						<td><?php echo strtoupper($h->comentarios);?></td>
					</tr>
					<?php	
					}
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	
	</body>
</html>