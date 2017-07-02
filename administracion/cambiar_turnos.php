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
include ('../clases/caja.php');
include ('../clases/turno_atencion.php');
include ("../clases/almacen.php");
include ("../clases/anuncio.php");

if(isset($_POST["id_caja"]))
	$id_caja = $_POST["id_caja"];
else
	$id_caja = 0;

$opcion_original = "K1UC7U91";

$caBLO = new CajaBLO();
$taBLO = new TurnoAtencionBLO();
$opcBLO = new OpcionBLO();
$almBLO = new AlmacenBLO();

$enlace_procesar = "../procesar_turno_atencion.php?id_centro=$id_centro&op_original_key=$opcion_original&usr_key=$usr_key";
$enlace = "cambiar_turnos.php?usr_key=$usr_key&opcion_key=$opcion_key&id_centro=$id_centro";

$opcion_crear_turno = "4WD962MF";
$opcion_cerrar_turno = "6R930WGD";

$permiso_crear_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_crear_turno, $usuario->id, $id_centro);
$permiso_cerrar_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_cerrar_turno, $usuario->id, $id_centro);

if($id_usuario > 0)
	$lista_almacenes = $almBLO->ListarAlmacenXIdUsuarioIdCentro_Venta($id_usuario, $id_centro);

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
			border-radius: 10px 10px 10px 10px; }
			
			.texto_1 { width: 60px; text-align: center; font-size: 11px; }
			.texto_2 { width: 80px; text-align: center; font-size: 11px; }
			.texto_2_5 { width: 130px; text-align: center; font-size: 11px; }
			.texto_3 { width: 120px; text-align: center; font-size: 11px; }
			.texto_3_5 { width: 180px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4_5 { width: 230px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_6 { width: 330px; text-align: center; font-size: 11px; }
			.texto_10 { width: 350px; text-align: center; font-size: 11px; }
			
			#div_caja { padding-left: 30px; }
			#lbl_caja { font-family: Helvetica; }
			#tabla_caja { font-family: Helvetica; }
			.sin_info { font-size: 11px; color:#585858; font-family: Helvetica; padding-left: 4px; }
			.etiqueta { font-weight: bold; font-size: 12px; color:#585858; font-family: Helvetica; }
			#div_tabla_turnos_activos { margin-top: 20px;}
			#tabla_turnos_activos { border-collapse: collapse; font-family: Helvetica; font-size: 11px; font-weight: normal; 
				background-color: #FFFFFF; border: solid 1px #0099CC; }
			#tabla_turnos_activos thead { color: #0099CC; border-bottom: solid 1px #0099CC; }
			#tabla_turnos_activos tbody td { color: #585858; border-bottom: dotted 1px #0099CC; }
			
			#tabla_turnos_activos tbody tr:nth-child(even) { background-color:#FFFFFF; }
			#tabla_turnos_activos tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			
			#tabla_turnos_activos tbody tr:hover { background-color: #F8FEA9; }
			
			.lbl_rojo { color: red; }
			
			 
				font-size: 11px; font-weight: bold; }
			#btn_crear_turno { font-family: Helvetica; font-size: 12px; }
			#id_turno_operacion { font-family: Helvetica; font-size: 11px; }
			
			#div_titulo_turnos_activos { margin-top: 10px; margin-bottom: 10px; text-shadow: 1px 1px 1px #333; }
			#titulo_turnos_activos { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC;  }
				
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		function CambiarCaja()
		{
			var id_caja = document.getElementById("id_caja").value;			
			if(id_caja > 0)
				document.turno.submit();
		}
		
		function validate(evt) 
		{
			var theEvent = evt || window.event;
			var key = theEvent.keyCode || theEvent.which;
			key = String.fromCharCode( key );
			var regex = /[0-9]|\./;
			if( !regex.test(key) ) 
			{
				theEvent.returnValue = false;
				if(theEvent.preventDefault) theEvent.preventDefault();
			}
		}
		
		function padStr(i) 
		{
		    return (i < 10) ? "0" + i : "" + i;
		}
		
		$(function()
		{
			$("#btn_crear_turno").click(function()
			{
				var id_caja = $("#id_caja").val();
				$id_almacen = $("#id_almacen").val();
				
				if(id_caja > 0 && $id_almacen > 0)
				{
					$("#turno").attr("action","<?php echo $enlace_procesar;?>");
					$("#operacion").val("crear");
					$("#turno").submit();					
				}
				else
					alert("No ha seleccionado Caja o Almacén");
				
			});
			
			$("#id_turno_operacion").live("change",function()
			{
				$("#turno").attr("action","<?php echo $enlace_procesar;?>");				
				$id_turno_operacion = $(this).val();
				
				$fila = $(this).parent().parent();
				
				$id_turno = $fila.find(".id_turno").val();
				
				if($id_turno_operacion == 1 && confirm("¿Seguro que Desea Cerrar este Turno?"))
				{
					$("#id_turno").val($id_turno);
					$("#operacion").val("cerrar");
					$("#turno").submit();
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
		<form id="turno" name="turno" method="post" action="<?php echo $enlace; ?>">
			<input id="operacion" name="operacion" type="hidden" />
			<input id="id_usuario" name="id_usuario" type="hidden" value="<?php echo $id_usuario;?>" />
			<input id="id_turno" name="id_turno" type="hidden" />
		<div id="div_caja" align="left">
			<table id= "tabla_caja">
				<tr>
					<td width="120px"><span class="etiqueta">Cajas Habilitadas:</span></td>
					<td>
						<select class="texto_4" id="id_caja" name="id_caja" onchange="CambiarCaja()">
						<?php
							$lista_cajas = $caBLO->ListarCajaHabilitadaIngresoXIdUsuario($id_usuario, $id_centro);
							if(!is_null($lista_cajas))
							{
								if(count($lista_cajas) > 0)
								{
									echo "<option value=\"0\">Seleccione...</option>";
									foreach($lista_cajas as $c)
									{
										if($id_caja == $c->id_caja)
											$selected = "selected='selected'";
										else
											$selected = "";	
										echo "<option value=\"$c->id_caja\" $selected>$c->caja</option>";
									}
										
								}
								else
								{ 
									echo "<option value=\"0\">Cajas No disponibles!</option>";
									$lista_cajas = NULL;
								}
							}
						?>
						</select>
					</td>
				</tr>
				
			</table>
		</div>			
		<div id="div_tabla_turnos_activos">
		<?php 
		if($id_caja > 0)
		{
		?>
			<div id="div_titulo_turnos_activos">
				<span id="titulo_turnos_activos">TURNOS DE ATENCIÓN</span>
			</div>
			
			<table id="tabla_turnos_activos">
				<thead>
					<th width=20px>Id</th>
					<th width=60px>Código</th>
					<th width=70px>Estado</th>
					<th width=100px>Usuario</th>
					<th width=100px>Almacen Def</th>
					<th width=130px>Hora Inicio</th>
					<th width=130px>Hora Fin</th>
					<th width=100px>Monto Inicial</th>
					<th width=80px>Total Ingresos</th>
					<th width=80px>Total Egresos</th>
					<th width=70px>Monto Total</th>
					<th width=100px>Opciones</th>
				</thead>
				<tbody>
				<?php
				//$lista_turnos_activos = $taBLO->ListarTurnosActivosXIdUsuario($id_caja, $id_usuario);
				$lista_turnos_activos = $taBLO->ListarTurnosActivos($id_caja);
				$lista_turnos_activos_usuario = $taBLO->ListarTurnosActivosXIdUsuario($id_caja, $id_usuario);
				
				
				if(!is_null($lista_turnos_activos_usuario))
				{
					if(count($lista_turnos_activos_usuario) == 0)
					{?>
					
					<tr>
						<td align="center">--</td>
						<td align="center">--</td>
						<td align="center">POR CREAR</td>
						<td align="center"><b><?php echo $usuario->login;?></b></td>
						<td align="center">
							<select id="id_almacen" name="id_almacen" class="texto_3">								
							<?php
							if(!is_null($lista_almacenes))
							{									
								if(count($lista_almacenes) > 0)
								{
									echo "<option value=\"0\">Seleccione...</option>";
									
									foreach($lista_almacenes as $a)
										echo "<option value=\"$a->id_almacen\" >$a->almacen</option>";
								}
								else
									echo "<option value=\"0\">No tiene Almacenes Disponibles</option>";
							}
							?>
							</select>
						</td>
						<td align="center">--</td>
						<td align="center">--</td>
								
						<td class="fila_crear_turno" align="center">S/.
							<input id="monto_inicial_mn" name="monto_inicial_mn" type="number" class="texto_1" onkeypress="validate()" value="0.00" />
						</td>
						<td align="center">--</td>
						<td align="center">--</td>
						<td align="center">--</td>
						<td align="center">
							<input id="btn_crear_turno" value="Crear Turno" type="button" class="texto_2" />
						</td>
					</tr>
					
					
					<?php		
					}
				}
				
				if(!is_null($lista_turnos_activos))
				{
					
					if(count($lista_turnos_activos) > 0)
					{
						
						$i = 1;
						foreach($lista_turnos_activos as $ta)
						{
							$fecha_hora_inicio_str = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($ta->fecha_hora_inicio))));
					
							if(!is_null($ta->fecha_hora_fin))						
								$fecha_hora_fin_str = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($ta->fecha_hora_fin))));
							else
								$fecha_hora_fin_str = "--";
							if(!is_null($ta->saldo_inicial_mn))
								$saldo_inicial_mn = $ta->saldo_inicial_mn;
							else 
								$saldo_inicial_mn = 0;
								
							$total_ingresos_mn = $taBLO->RetornarMontoTotalCuentaVentaXIdTurnoAtencion($ta->id);
							$otros_ingresos_mn = $taBLO->RetornarMontoTransaccionesPositivas($ta->id);
							$otros_egresos_mn = $taBLO->RetornarMontoTransaccionesNegativas($ta->id);
							
							if(is_null($total_ingresos_mn))
								$total_ingresos_mn = 0;
							
							if(is_null($otros_ingresos_mn))
								$otros_ingresos_mn = 0;
							
							if(is_null($otros_egresos_mn))
								$otros_egresos_mn = 0;
							
							$total_ingresos_mn += $otros_ingresos_mn;
							
							$total_mn = $saldo_inicial_mn + $total_ingresos_mn - $total_egresos_mn;
							 
							?>
							<tr>
								<td align="center">
									<?php echo $i;?>
									<input class="id_turno" type="hidden" value="<?php echo $ta->id;?>" />
								</td>
								<td align="center"><b><span><?php echo $ta->auto_key;?></span></b></td>
								<td align="center"><b><span class="lbl_rojo"><?php echo $ta->estado;?></span></b></td>
								<td align="center"><b><?php echo $ta->usuario;?></b></td>
								<td align="center"><?php echo $ta->almacen;?></b></td>
								<td align="center"><?php echo $fecha_hora_inicio_str;?></td>
								<td align="center"><?php echo $fecha_hora_fin_str;?></td>
								<td align="center"><?php echo "S/. ".number_format($saldo_inicial_mn, 2);?></td>
								<td align="center"><b><?php echo "S/. ".number_format($total_ingresos_mn, 2);?><b></td>
								<td align="center"><?php echo "S/. ".number_format($total_egresos_mn, 2);?></td>
								<td align="center"><b><?php echo "S/. ".number_format($total_mn, 2);?></b></td>
								<td align="center">
									<select id="id_turno_operacion" name="id_turno_operacion">
										<option value="0">Seleccione...</option>
										<?php
											if($id_usuario == $ta->id_usuario || $permiso_cerrar_turno->isOK)
												echo "<option value=\"1\">Cerrar Turno</option>\n";
										?>
									</select>
								</td>
							</tr>
						<?php	
						
							$i++;
						}
						?>
					
					
					<?php
					}
				}	
				?>	
					
					
				</tbody>
			</table>
			
		<?php
		}
		else 
		{?>
		<span class="sin_info">No hay Información disponible</span>
		<?php	
		}
		?>
		</div>		
		</form>
	</div>
	
	</body>
</html>