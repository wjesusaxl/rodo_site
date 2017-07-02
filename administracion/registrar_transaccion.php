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
include ("../clases/transaccion.php");
include ("../clases/anuncio.php");
include ("../clases/caja.php");
include ("../clases/turno_atencion.php");

$enlace_procesar_turno_atencion = "../procesar_turno_atencion.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";
$enlace_procesar = "../procesar_transaccion.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";

$tranBLO = new TransaccionBLO();
$opcBLO = new OpcionBLO();
$caBLO = new CajaBLO();
$taBLO = new TurnoAtencionBLO();

$opcion_transaccion_sin_turno = "J3FMA841";

$permiso_transaccion_sin_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_transaccion_sin_turno, $id_usuario, $id_centro);

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
				border-radius: 10px 10px 10px 10px; font-family: Helvetica; }
			#titulo { font-weight: bold; font-size: 14px; color: #585858; }
			.texto_1 { width: 30px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 75px; text-align: center; font-size: 11px; }
			.texto_2 { width: 100px; text-align: center; font-size: 11px; }
			.texto_3 { width: 150px; text-align: center; font-size: 11px; }
			.texto_3_5 { width: 180px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4_5 { width: 230px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_10 { width: 400px; text-align: center; font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #585858; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			.etiqueta { font-size: 11px; font-weight: bold; color: #585858; float: left; }
			
			#div_opcion_general { float: right; margin-right: 20px; width: 1030px; margin-bottom: 20px;  }
			
			#titulo { margin-bottom: 20px; }
			#id_opcion_general { margin-left: 10px; }
			#tabla_ingreso { border-collapse: collapse; }
			#tabla_ingreso td { border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; padding-bottom: 3px; }
			
			#tabla_opcion_cantidad td { border: none; }
			#tabla_opcion_cantidad th { font-size: 12px; color: #0099CC; }
			
			#div_tabla_resultados { margin-top: 20px; border-collapse: collapse; display: none; }
			#tabla_resultados th { font-size: 12px; color: #0099CC; border-bottom: dotted 1px #0099CC; border-top: dotted 1px #0099CC; background-color: #FFFFFF;}
			#tabla_resultados td { font-size: 11px; color: #585858;}
			#tabla_resultados tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_resultados tr:nth-child(even) { background-color:#FFFFFF; }
			/*#tabla_resultados tbody tr:not(:first-child):hover { background-color: #F8FEA9; }*/
			#tabla_resultados tbody tr:hover { background-color: #F8FEA9; }
			#tabla_resultados { border-collapse: collapse; }
			
			#comentarios { font-size: 11px; resize: none; font-family: Helvetica;  }
			
			.btn_edit:hover { cursor: pointer; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px;}
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script>
		<script language="JavaScript" src="../js/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="../styles/jquery-ui.css" type="text/css" media="all"/>
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		<script type="text/javascript">
		
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
		
		function FechaFormato(fecha, formato)
		{
			fecha = new Date(Date.parse(fecha, 'Y-m-d H:i:s' ));
			
			mes = fecha.getMonth() + 1;
			dia = fecha.getDate();
			
			switch(formato)
			{
				case "y-m-d h:m:s":
					$fecha_str = fecha.getFullYear().toString() + "-" + padStr(mes.toString()) + "-" + padStr(dia.toString()) + " " + 
					padStr(fecha.getHours().toString()) + ":" + padStr(fecha.getMinutes().toString()) + ":" + padStr(fecha.getSeconds().toString()); break;
				case "d-m-y":
					$fecha_str = padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()); break;
					
				case "d-m-y h:m":
					$fecha_str = padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()) + " " +
					padStr(fecha.getHours().toString()) + ":" + padStr(fecha.getMinutes().toString()); break
			}
			
			return $fecha_str
		}
		
		function padStr(i) 
		{
		    return (i < 10) ? "0" + i : "" + i;
		}
		
		function roundNumber(number, digits) {
            var multiple = Math.pow(10, digits);
            var rndedNum = Math.round(number * multiple) / multiple;
            return rndedNum;
        }
        
        function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		$(function()
		{
			$("#id_transaccion_grupo_str").change(function()
			{
				$id_transaccion_grupo_str = $(this).val();
				$id_transaccion_motivo = $("#id_transaccion_motivo");
				
				$id_transaccion_motivo.empty();
				$id_transaccion_motivo.append("<option value=\"0\">Seleccione...</option>");
				
				$id_transaccion_grupo_str = $id_transaccion_grupo_str.split(":");
				
				if($id_transaccion_grupo_str.length == 2)
				{
					$id_transaccion_grupo = $id_transaccion_grupo_str[0];
					$transaccion_grupo_factor = $id_transaccion_grupo_str[1];
					
					$("#id_transaccion_grupo").val($id_transaccion_grupo);
					$("#transaccion_grupo_factor").val($transaccion_grupo_factor);
					
					if($id_transaccion_grupo > 0)
					{
						$url = "<?php echo $enlace_procesar;?>" + "&operacion=transaccionmotivoxtransacciongrupo&id_transaccion_grupo=" + $id_transaccion_grupo;
						
						$.getJSON($url, function(data)
						{
							if(data != null)
							{
								
								$.each(data, function(key, val) 
								{
								
									$id_transaccion_motivo.append("<option value=\"" + val.id +"\">" + val.descripcion.toUpperCase() +"</option>");
								})
								
							}
						});
					}
					
				}
				
				
			});
			
			$("#id_caja_str").change(function()
			{
				$id_caja_arr = $(this).val();
				
				$id_turno_atencion = $("#id_turno_atencion");
				$id_turno_atencion.empty();
				$id_turno_atencion.append("<option value=\"0\">Seleccione...</option>");
				$id_usuario = $("#id_usuario").val();
				
				$id_caja_arr = $id_caja_arr.split(":");
				
				if($id_caja_arr.length == 4)
				{
					$id_caja = $id_caja_arr[0];
					$("#id_caja").val($id_caja);
					$("#caja_flag_habilitado").val($id_caja_arr[1]);
					$("#caja_flag_ingreso").val($id_caja_arr[2]);
					$("#caja_flag_salida").val($id_caja_arr[3]); 
					
					if($id_caja > 0)
					{
						$url = "<?php echo $enlace_procesar_turno_atencion;?>" + "&operacion=query_turnos_activos_usuario&id_usuario=" + $id_usuario + "&id_caja=" + $id_caja;
						
						$.getJSON($url, function(data)
						{
							if(data != null)
							{
								$.each(data, function(key, val) 
								{
									$fecha_inicio = FechaFormato(val.fecha_hora_inicio, "d-m-y h:m");
									
									$opcion = "<option value=\"" + val.id + "\">" + val.codigo + " [" + $fecha_inicio + "]</option>";
									$id_turno_atencion.append($opcion)
								});
								
							}
						});
					}
				}
					
			});
			
			$("#btn_guardar").click(function()
			{
				$id_caja = $("#id_caja").val();
				$id_usuario = $("#id_usuario").val();
				$id_transaccion_motivo = $("#id_transaccion_motivo").val();
				$id_transaccion_grupo = $("#id_transaccion_grupo").val();
				$id_turno_atencion = $("#id_turno_atencion").val();
				$monto_mn = $("#monto_mn").val();
				
				
				$mensaje = "";
				
				if($id_caja == 0)
					$mensaje += "+ No ha seleccionado una Caja Válida\n";
					
				if($id_transaccion_motivo == 0)
					$mensaje += "+ No ha seleccionado un Motivo de Transacción.\n";
				
				<?php 
				if(!$permiso_transaccion_sin_turno->isOK)
				{?>
					if($id_turno_atencion == 0)
						$mensaje += "+ No ha seleccionado un Turno de Atención.\n";
				<?php		
				}	
				?>
				
				if($monto_mn == 0)
					$mensaje += "+ Monto NO puede ser igual a 0.\n";
					
				if($mensaje != "")
				{
					$mensaje = "Se encontraron los siguientes errores:\n\n" + $mensaje;
					alert($mensaje)
				}
				else
				{
					$("#operacion").val("crear");
					$("#transaccion").submit();
				}
					
				
			})
			
		});
		
	
		
		
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
		<form id="transaccion" name="transaccion" method="post" action="<?php echo $enlace_procesar; ?>">
			<input type="hidden" name="operacion" id ="operacion" />
			<input type="hidden" name="id_usuario" id ="id_usuario" value="<?php echo $id_usuario;?>" />
			<input type="hidden" name="id_caja" id ="id_caja" value="0"/>
			<input type="hidden" name="id_transaccion_grupo" id ="id_transaccion_grupo" value="0"/>
			<input type="hidden" name="transaccion_grupo_factor" id ="transaccion_grupo_factor"/>
			<input type="hidden" name="caja_flag_habilitado" id ="caja_flag_habilitado"/>
			<input type="hidden" name="caja_flag_ingreso" id ="caja_flag_ingreso"/>
			<input type="hidden" name="caja_flag_salida" id ="caja_flag_salida"/>
			
			<div id="div_tabla_transaccion">
				<table id="tabla_transaccion">
					<tr>
						<td><span class="etiqueta">Grupo de Transacción: </span></td>
						<td width=10px></td>
						<td>
							<select id="id_transaccion_grupo_str" name="id_transaccion_grupo_str" class="texto_3">
								<option value="0">Seleccione...</option>
								<?php
									$lista_grupos = $tranBLO->ListarGruposTransaccionHabilitadosXIdUsuarioIdCentro($id_usuario, $id_centro);
									if(!is_null($lista_grupos))
									{
										foreach($lista_grupos as $tg)
											echo "<option value=\"$tg->id_transaccion_grupo:$tg->transaccion_factor\">".strtoupper($tg->transaccion_grupo)."</option>";
									}
								?>	
							</select>
						</td>
						<td width="30px"></td>
						<td><span class="etiqueta">Motivo de Transacción: </span></td>
						<td width=10px></td>
						<td>
							<select id="id_transaccion_motivo" name="id_transaccion_motivo" class="texto_4">
								<option value="0">Seleccione...</option>
							</select>
						</td>
						<td width="30px"></td>
						<td><span class="etiqueta">Caja: </span></td>
						<td width=10px></td>
						<td>
							<select id="id_caja_str" name="id_caja_str" class="texto_4">
								<option value="0">Seleccione...</option>
								<?php
								$lista_cajas = $caBLO->ListarCajaHabilitadaXIdUsuario($id_usuario, $id_centro);
								if(!is_null($lista_cajas))
									foreach($lista_cajas as $cu)
										echo "<option value=\"$cu->id_caja:$cu->habilitado:$cu->flag_ingreso:$cu->flag_salida\">$cu->caja</option>\n";								
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><span class="etiqueta">Turno Activo: </span></td>
						<td width="20px"></td>
						<td>
							<select id="id_turno_atencion" name="id_turno_atencion" class="texto_3">
								<option value="0">Seleccione...</option>				
							</select>
						</td>
						<td></td>
						<td><span class="etiqueta">Monto Transacción S/.: </span></td>
						<td width="20px"></td>
						<td><input type="number" id="monto_mn" name="monto_mn" class="texto_1_5" value="0.00" onkeypress="validate()"/></td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td><span class="etiqueta">Comentarios: </span></td>
						<td width="20px"></td>
						<td colspan="9">
							<textarea cols="86" rows="2" id="comentarios" name="comentarios" ></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="11" align="center">
							<input type="button" id="btn_guardar" value="Guardar" class="texto_1_5"> 
						</td>
					</tr>
				</table>
				
			</div>
			
			
		</form>
	</div>
	
	</body>
</html>