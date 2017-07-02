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
include ("../clases/cuenta_venta.php");
include ("../clases/transaccion.php");

if(isset($_GET["turno_atencion_key"]))
	$turno_atencion_key = $_GET["turno_atencion_key"];
else 
	$turno_atencion_key = "";
	
$opcBLO = new OpcionBLO();
$cvBLO = new CuentaVentaBLO();
$taBLO = new TurnoAtencionBLO();
$traBLO = new TransaccionBLO();

$opcion_ver_turno_otro_usuario = "3IR964RJ";
$permiso_ver_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_ver_turno_otro_usuario, $id_usuario, $id_centro);

if($turno_atencion_key != "")
{
	
	$turno = $taBLO->RetornarXKey($turno_atencion_key);
	if(!is_null($turno))
	{
		
		$fecha_hora_inicio = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($turno->fecha_hora_inicio ))));
		$fecha_hora_fin = is_null($turno->fecha_hora_fin) ? "--" : date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($turno->fecha_hora_fin))));
		
		$id_turno_atencion = $turno->id;
		
		//if($turno->id_estado == 1)
		$saldo_inicial_mn = $turno->saldo_inicial_mn;
		$monto_ventas_mn = $taBLO->RetornarMontoTotalCuentaVentaXIdTurnoAtencion($turno->id);
		$monto_transacciones_positivas = $taBLO->RetornarMontoTransaccionesPositivas($turno->id);
		$monto_transacciones_negativas = $taBLO->RetornarMontoTransaccionesNegativas($turno->id);
		$monto_total_mn = $saldo_inicial_mn + $monto_ventas_mn + $monto_transacciones_positivas - $monto_transacciones_negativas;
		
		
		if($id_usuario != $turno->id_usuario && !$permiso_ver_turno->isOK)
		{
			$turno = null;
			$id_turno_atencion = 0;
		}
	}
	else
		$id_turno_atencion = 0;
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
			
			.div_cuenta { float: left; margin-left: 12px; font-family: Helvetica; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; 
				padding-left: 10px; padding-right: 10px; padding-top: 10px; padding-bottom: 10px; margin-bottom: 10px; }
			.lbl_cuenta { border-radius: 8px 8px 8px 8px; background-color: #0099CC; padding-left: 5px; padding-right: 5px; color: #FFFFFF; padding-top: 3px;
				padding-bottom: 3px; font-size: 14px; float: left;}
			.lbl_cuenta_estado { vertical-align: middle; height: 22px; color: #0099CC; font-size: 12px; float: right; padding-top:6px; margin-left: 10px; }
			.lbl_cliente { font-size: 12px; color: #0099CC; padding-top: 6px; float: left; margin-left: 5px;  }
			#div_lista_cuentas {margin-top: 10px; float: left; }
			.cuenta_cabecera { color: #0099CC; font-size: 12px; font-weight: bold; }
			.cuenta_item { color: #585858; font-size: 11px; }
			.cuenta_total { color: #0099CC; font-size: 12px; }
			
			.tabla_cuenta { border-collapse: collapse; }
			.tabla_cuenta tbody tr:nth-child(even) { background-color:#DAF1F7; }
			.tabla_cuenta tbody tr:nth-child(odd) { background-color:#FFFFFF; }
			.tabla_cuenta tbody tr { border-bottom: dotted 1px #0099CC; }
			
			#div_resumen_productos { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 485px; padding-top: 5px; padding-bottom: 5px; margin-top: 10px; float: left; margin-left: 12px; }
			#tabla_resumen_productos { font-family: Helvetica; border-collapse: collapse;  }
			#tabla_resumen_productos thead { font-size: 12px; color: #0099CC; font-weight: bold; }
			#tabla_resumen_productos tbody tr { font-size: 11px; }
			#tabla_resumen_productos tbody td { border-top: dotted 1px #0099CC; color: #585858; }
			#tabla_resumen_productos tbody tr:nth-child(even) { background-color:#DAF1F7; }
			#tabla_resumen_productos tbody tr:nth-child(odd) { background-color:#FFFFFF; }
			
			#div_transacciones_extras { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; padding-top: 5px; padding-bottom: 5px; margin-top: 10px; float: left; margin-left: 12px; }
			#tabla_transacciones { font-family: Helvetica; border-collapse: collapse;  }
			#tabla_transacciones thead { font-size: 12px; color: #0099CC; font-weight: bold; }
			#tabla_transacciones tbody tr { font-size: 11px; }
			#tabla_transacciones tbody td { border-top: dotted 1px #0099CC; color: #585858; }
			#tabla_transacciones tbody tr:nth-child(even) { background-color:#DAF1F7; }
			#tabla_transacciones tbody tr:nth-child(odd) { background-color:#FFFFFF; }
			
			#titulo_lista_producto { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; margin-bottom: 10px;}
			#titulo_lista_transacciones{ font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; margin-bottom: 10px;}
			.lbl_2 { color: #0099CC; font-weight: bold; }
			
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
				<tr><td colspan="15" align="center"><span id="lbl_titulo">DETALLE DEL TURNO DE ATENCIÓN</span></td></tr>
				<tr height="10px"></tr>
				<tr>
					<td><span class="etiqueta">TURNO ATENCIÓN:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo $turno->codigo;?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">ESTADO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo $turno->estado;?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">HORA APERTURA:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo $fecha_hora_inicio;?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">HORA CIERRE:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center" width="130px"><span class="valor"><?php echo $fecha_hora_fin;?></span></td>
				</tr>
				<tr>
					<td><span class="etiqueta">CENTRO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($turno->centro);?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">CAJA:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($turno->caja);?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">SALDO INICIAL:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo "S/. ".number_format($turno->saldo_inicial_mn, 2);?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">TOTAL VENTAS:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo "S/. ".number_format($monto_ventas_mn, 2);?></span></td>
				</tr>
				<tr>
					<td><span class="etiqueta">OTROS INGRESOS:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo "S/. ".number_format($monto_transacciones_positivas, 2);?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">OTROS EGRESOS:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo "S/. ".number_format($monto_transacciones_negativas, 2);?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">TOTAL MN:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor lbl_2"><?php echo "S/. ".number_format($monto_total_mn, 2);?></span></td>
					<td width="20px"></td>
					<td><span class="etiqueta">ALMACEN:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor"><?php echo strtoupper($turno->almacen) ;?></span></td>
				</tr>
				<tr>
					<td><span class="etiqueta">USUARIO:</span></td>
					<td width="5px"></td>					
					<td class="td_valor" align="center"><span class="valor lbl_2" title="<?php echo $turno->usuario_nombres_apellidos;?>"> <?php echo strtoupper($turno->usuario); ?></span></td>
					<td colspan="9"></td>
				</tr>
			</table>
		</div>
		<div id="div_resumen_productos">
		<?php
		$lista_productos = $taBLO->ListarResumenProductosXIdTurno($turno->id);		
		if(!is_null($lista_productos))
		{?>
			<div id="titulo_lista_producto">RESUMEN DE VENTAS POR PRODUCTO</div>
			<table id="tabla_resumen_productos">
				<thead>
					
					<th width=100px>MARCA</th>
					<th width=100px>NRO. SERIE</th>
					<th width=170px>PRODUCTO</th>
					<th width=50px>CANT</th>
					<th width=50px>TOTAL</th>
				</thead>
				<tbody>
				<?php
				$total_mn = 0;
				foreach($lista_productos as $p)
				{
					$total_mn += $p->precio_total_mn;
					?>
					<tr>
						<td align="center"><?php echo $p->marca;?></td>
						<td align="center"><?php echo $p->nro_serie;?></td>
						<td style="padding-left: 10px;"><b><?php echo $p->descripcion_corta;?></b></td>
						<td align="center"><?php echo number_format($p->cantidad, 2);?></td>
						<td align="center"><?php echo "S/. ".number_format($p->precio_total_mn, 2);?></td>
					</tr>
					
				<?php
				}?>
				<tr>
						<td colspan="4"></td>
						<td align="center"><b><?php echo "S/. ".number_format($total_mn, 2);?></b></td>
					</tr>
				
				</tbody>
			</table>
		<?php
		}
		?>
		</div>
		
		<div id="div_transacciones_extras">
		
		
			<div id="titulo_lista_transacciones">TRANSACCIONES EXTRAS REGISTRADAS</div>
			<table id="tabla_transacciones">
				<thead>
					<th width=60px>Código</th>
					<th width=120px>Hora</th>
					<th width=180px>Grupo</th>
					<th width=180px>Motivo</th>
					<th width=70px>Monto</th>
				</thead>
				<tbody>
				<?php
				$lista_transacciones = $traBLO->ListarXIdTurnoAtencion($turno->id);
				if(!is_null($lista_transacciones))
				{
					$total_mn = 0;
					foreach($lista_transacciones as $tx)
					{
						$total_mn += $tx->monto_total_mn * $tx->transaccion_factor;
						$fecha_tx = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($tx->fecha_hora_registro))))
						?>
						<tr>
							<td align="center"><?php echo $tx->auto_key;?></td>
							<td align="center"><?php echo $fecha_tx;?></td>
							<td align="center"><?php echo strtoupper($tx->transaccion_grupo);?></td>
							<td align="center"><b><?php echo strtoupper($tx->transaccion_motivo);?></b></td>
							<td align="center"><?php echo "S/. ".number_format($tx->monto_total_mn * $tx->transaccion_factor, 2);?></td>
						</tr>
					<?php
					}
				}
				else
					echo "<tr><td colspan=5>Vacío!</td></tr>";
				?>
				<tr>
					<td colspan="4"></td>
					<td align="center"><b><?php echo "S/. ".number_format($total_mn, 2);?></b></td>
				</tr>
				
				</tbody>
			</table>
		</div>
		
		<div id="div_lista_cuentas">
		<?php
		
		$lista_cuentas = $cvBLO->ListarCuentasXIdTurnoAtencion($turno->id);
		if(!is_null($lista_cuentas))
		{
			foreach($lista_cuentas as $cv)
			{				
			?>
			<div class="div_cuenta">
				<table class="tabla_cuenta">
					<tr>
						<td colspan="6" valign="bottom">
							<div class="lbl_cuenta">
								<?php echo "Cuenta: <b>".str_pad($cv->id, 5, "0", STR_PAD_LEFT);?></b>
							</div>
							<div class="lbl_cliente">
								<?php echo "Cliente: <b> $cv->cliente";?>
							</div>
							<div class="lbl_cuenta_estado">
								<span>Estado: <b><?php echo $cv->desc_estado;?></b></span>
							</div>							
						</td>
					</tr>
					<?php
					$lista_items = $cvBLO->ListarTodosItemsXIdCuentaVenta($cv->id);
					if(!is_null($lista_items))
					{?>
						<tr class="cuenta_cabecera">
							<td width="20px" align="center">#</td>
							<td width="200px" align="center">Producto</td>
							<td width="80px" align="center">Cantidad</td>
							<td width="80px" align="center">Prec.Unitario</td>
							<td width="80px" align="center">Prec.Total</td>
							<td width="60px" align="center">Anulado</td>
						</tr>
						
						<?php
						$i = 1;
						$total_cuenta_mn = 0;
						foreach($lista_items as $cvi)
						{
							if($cvi->flag_anulado == 1)
								$total_mn = 0;
							else
								$total_mn = $cvi->cantidad * $cvi->precio_total_mn;
							?>
						<tr class="cuenta_item">
							<td align="center"><?php echo $i;?></td>
							<td style="padding-left: 5px;"><b><?php echo strtoupper($cvi->descripcion_corta);?></b></td>
							<td align="center"><?php echo number_format($cvi->cantidad, 2);?></td>
							<td align="center"><?php echo "S/. ".number_format($cvi->precio_total_mn, 2);?></td>
							<td align="center"><?php echo "S/. ".number_format($total_mn, 2);?></td>
							<td align="center"><?php echo $cvi->flag_anulado == 0 ? "NO" : "SI";?></td>
						</tr>							
						<?php
							$i++;
							$total_cuenta_mn = $total_cuenta_mn + $total_mn;
						}?>
						<tr class="cuenta_total">
							<td colspan="4" align="right"><b>TOTAL</b></td>
							<td align="center"><b><?php echo "S/. ".number_format($total_cuenta_mn, 2);?></b></td>
							<td></td>
						</tr>	
					<?php
					}					
					?>
				</table>
			</div>
			 
			
			<?php	
			}
		}
		
		
		?>
		</div>
	</div>
	
	</body>
</html>