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
include ("../clases/comprobante_pago.php");
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_comprobante_pago.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";
$enlace_query = "../procesar_comprobante_pago.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";

$opcBLO = new OpcionBLO();
$compBLO = new ComprobantePagoBLO();

$opcion_crear_comprobantes = "NW156E5V";  //De Local
$permiso_crear_comprobante = $opcBLO->ValidarOpcionXIdUsuario($opcion_crear_comprobantes, $id_usuario, $id_centro);


$opcion_modificar_comprobantes = "F135YC0U";  //De Local
$permiso_modificar_comprobante = $opcBLO->ValidarOpcionXIdUsuario($opcion_modificar_comprobantes, $id_usuario, $id_centro);


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
			#div_main {  width: 1100px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; 
				overflow: hidden; border-radius: 10px 10px 10px 10px }
			.etiqueta { font-family: Helvetica; font-size: 12px; font-weight: bold; color: #585858; }
			.etiqueta_1 { font-family: Helvetica; font-size: 12px;  color: #585858; }
			select { font-family: Helvetica; font-size: 12px; }
			
			.dato { font-family: Helvetica; font-size: 11px; text-align: center; font-weight: bold; }
			.dato_1 { font-family: Helvetica; font-size: 11px; text-align: center; }
			.texto_1 { width: 50px; }
			.texto_1_5 { width: 65px;}
			.texto_2 { width: 110px; }
			.texto_3 { width: 150px; }
			.texto_4 { width: 200px; }
			.texto_5 { width: 300px; }
			.texto_6 { width: 450px; }
			.texto_7 { width: 550px; }
			
			.lbl_resultado { float: left; border: dotted 1px; margin-left: 3px; }
			.lbl_nro_comprobante { float: left; }
			.lbl_OK, .lbl_FAIL{ display: none; font-family: Helvetica; font-weight: bold;}
			.lbl_OK { color: green; border-radius: 5px 5px 5px 5px; }
			.lbl_FAIL { color: red; border-radius: 3px 3px 3px 3px; }
			
			.cantidad { width: 45px;font-size: 11px; text-align: center; }
			.eliminar_fila_producto { width: 100px; text-align: center; font-size: 11px; }
			#crear_movimiento { font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #0099CC; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			#tabla_info { border-collapse: collapse;}
			#tabla_info tbody td{ border-bottom: dotted 1px #0099CC;  }
			.td_titulo { border-bottom: dotted 1px #0099CC; }
			
			#div_titulo_comprobantes { float: left; width: 1050px;  }
			#tabla_lista_comprobantes { }
			#div_lista_comprobantes { width: 1050px; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; margin-bottom: 10px; margin-top: 20px; 
				display: none;}
			
			#tabla_lista_comprobantes { border-collapse: collapse; width: 1040px; font-family: Helvetica;  }
			#tabla_lista_comprobantes thead th { font-size: 12px; font-weight: bold; color: #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_comprobantes tbody td { border-bottom: dotted 1px #0099CC; font-size: 11px; color: #585858;}
			
			
			#tabla_lista_comprobantes tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_lista_comprobantes tbody tr:nth-child(even) { background-color:#FFFFFF; }
			
			#div_operacion { margin-top: 15px; display: none; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px; }
			
			#btn_operacion { display: none; }
			
			.nro_comprobante { color: #0099CC; font-size: 13px; }
			
			#div_prueba { width: 20px; height: 20px; background-color: #0099CC }
			#div_guardar_cambios { display: none; margin-top: 15px; }
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		function padStr(i) 
		{
		    return (i < 10) ? "0" + i : "" + i;
		}
		
		function FechaFormato(fecha, formato)
		{
			fecha = new Date(Date.parse(fecha, 'Y-m-d H:i:s' ));
			
			//fecha = new Date(Date.parse(fecha));
			mes = fecha.getMonth() + 1;
			dia = fecha.getDate();
			
			switch(formato)
			{
				case "y-m-d h:m:s":
					$fecha_str = fecha.getFullYear().toString() + "-" + padStr(mes.toString()) + "-" + padStr(dia.toString()) + " " + 
					padStr(fecha.getHours().toString()) + ":" + padStr(fecha.getMinutes().toString()) + ":" + padStr(fecha.getSeconds().toString()); break
				case "d-m-y":
					$fecha_str = padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()); break
			}
			
			return $fecha_str
		}
		
		function validate(evt) 
		{
			var theEvent = evt || window.event;
			var key = theEvent.keyCode || theEvent.which;
			key = String.fromCharCode( key );
			//var regex = /[0-9]|\./;
			var regex = /[0-9]/;
			if( !regex.test(key) ) 
			{
				theEvent.returnValue = false;
				if(theEvent.preventDefault) theEvent.preventDefault();
			}
		}
		function validate2(evt) 
		{
			var theEvent = evt || window.event;
			var key = theEvent.keyCode || theEvent.which;
			key = String.fromCharCode( key );
			var regex = /[0-9]|\./;
			//var regex = /[0-9]/;
			if( !regex.test(key) ) 
			{
				theEvent.returnValue = false;
				if(theEvent.preventDefault) theEvent.preventDefault();
			}
		}
		
		function roundNumber(number, digits) 
		{
            var multiple = Math.pow(10, digits);
            var rndedNum = Math.round(number * multiple) / multiple;
            return rndedNum;
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
			
			$("#id_operacion").change(function()
			{
				$id_operacion = $(this).val();
				
				$("#btn_operacion").css("display", "none");
				
				
				$operacion = "";
				
				if($id_operacion > 0)
				{
					$("#btn_operacion").css("display", "block");
					
					switch($id_operacion)
					{
						case "1": $operacion = "Buscar"; break;
						case "2": $operacion = "Generar"; break;
						case "3": $operacion = "Mostrar"; break;				
					}
					
					$("#btn_operacion").val($operacion);
				}
				
			});
			
			
			$("#id_comprobante_pago_tipo").change(function()
			{
				$("#nro_serie").empty();
				
				
				$id_comprobante_pago_tipo = $(this).val();
				
				if($id_comprobante_pago_tipo > 0)
				{
					var url = "<?php echo $enlace_query;?>&operacion=query_serie&id_comprobante_pago_tipo=" + $id_comprobante_pago_tipo;
					
					$.getJSON(url, function(data)
					{
						if(data != null)
						{
							$("#nro_serie").append("<option value=\"0\">Seleccione...</option>");
							
						    $.each(data, function(key, val) 
							{
								$option = "<option value=\"" + val.nro_serie +"\">" + val.nro_serie + "</option>"
								$("#nro_serie").append($option);								
							});
						}
						else
							$("#nro_serie").append("<option value=\"0\">Serie No Disponible</option>");						
					});
					
				}
				else				
					$("#nro_serie").append("<option value=\"0\">Seleccione...</option>");
				
			});
			
			$("#btn_operacion").live("click", function()
			{
				$id_operacion = $("#id_operacion").val();
				$id_comprobante_pago_tipo = $("#id_comprobante_pago_tipo").val();
				$nro_serie = $("#nro_serie").val();
				$nro_inicio = $("#nro_inicio").val();
				$nro_fin = $("#nro_fin").val();
				$fecha_inicio = $("#fecha_inicio").val();
				$fecha_fin = $("#fecha_fin").val();
				$id_tipo_origen = 1;
				
				$("#div_guardar_cambios").css("display","none");
					
				$mensaje = "";
					
				$("#tabla_lista_comprobantes").find("tr:gt(0)").remove();
				$("#div_lista_comprobantes").css("display", "none");
					
				if($id_comprobante_pago_tipo == 0)
					$mensaje = $mensaje + "+ Tipo de Comprobante de Pago NO Seleccionado.\n";
				
				if($nro_serie == 0)
					$mensaje = $mensaje + "+ Nro. de Serie NO Válida.\n";
					
				if($nro_inicio == 0)
					$mensaje = $mensaje + "+ Nro. de Comprobante Inicial NO Válido.\n";
					
				if($nro_fin == 0)
					$mensaje = $mensaje + "+ Nro. de Comprobante Final NO Válido.\n";
					
				if($mensaje != "")
				{
					$mensaje = "Se ha(n) encontrado el/los siguiente(s) error(es):\n\n" + $mensaje;
					alert($mensaje);
				}
				else
				{
					$("#div_lista_comprobantes").css("display", "block");
											
					if($id_operacion == 1 || $id_operacion == 2 || $id_operacion == 3)
					{
						if($id_operacion == 1 || $id_operacion == 3)
						{
							if($("#flag_fecha").is(':checked'))
								$busqueda_fecha = "Y";
							else
								$busqueda_fecha = "N"; 
								
							var url = "<?php echo $enlace_query;?>&operacion=query_comprobante&id_comprobante_pago_tipo=" + $id_comprobante_pago_tipo + "&id_tipo_origen=" + $id_tipo_origen + "&nro_serie=" + $nro_serie;
							url = url + "&nro_inicio=" + $nro_inicio + "&nro_fin=" + $nro_fin + "&fecha_inicio=" + $fecha_inicio + "&fecha_fin=" + $fecha_fin + "&busqueda_fecha=" + $busqueda_fecha;
								
						}
						if($id_operacion == 2)
						{
							var url = "<?php echo $enlace_query;?>&operacion=generar&id_comprobante_pago_tipo=" + $id_comprobante_pago_tipo + "&id_tipo_origen=" + $id_tipo_origen + "&nro_serie=" + $nro_serie;
							url = url + "&nro_inicio=" + $nro_inicio + "&nro_fin=" + $nro_fin + "&fecha_inicio=" + $fecha_inicio + "&fecha_fin=" + $fecha_fin;
						}
						
						$.getJSON(url, function(data)
						{					
							if(data != null)
							{
								$i = 1;
								
								if($id_operacion == 1 || $id_operacion == 3)
									$("#div_guardar_cambios").css("display","block");
								
								$.each(data, function(key, val) 
								{
									$readonly = "";
									$disabled = "";
											
									if(val.fecha_hora_registro != null)
										$fecha = FechaFormato(val.fecha_hora_registro, "d-m-y");
									else
										//$fecha = new Date();
										$fecha = "";
																		
									$html_fecha_registro = "<input type=\"text\" class=\"fecha_registro_str texto_1_5 dato_1\" id=\"fecha_registro_str_" + $i + "\" readonly='readonly'" +  $disabled + "/>";
									$html_fecha_registro = $html_fecha_registro + "<input type=\"hidden\" class=\"fila_fecha\" value=\"" + $i + "\" />"
									$html_fecha_registro = $html_fecha_registro + "<input type=\"hidden\" class=\"fecha_registro texto_1 dato_1\" id=\"fecha_registro_" + $i + "\"/>";
									//$html_fecha_registro = $html_fecha_registro + "<input type=\"hidden\" class=\"fecha_registro_aux\" id=\"fecha_registro_aux_" + $i + "\" value=\"" + $fecha + "\"  />";
										
									if(val.flag_anulado == 0)
										$flag_anulado = "No";
									if(val.flag_anulado == 1)
										$flag_anulado = "<b>Si</b>";
									if(val.flag_post == 0)
									{
										$flag_post = "Sin Declarar";
										$class_lock = "";
									}
										
									if(val.flag_post == 1)
									{
										$flag_post = "<b>Declarado</b>";
										$readonly = "readonly='readonly'";
										$disabled = "disabled='disabled'";
										$class_lock = "locked";
									}
										
									$html_flag_anulado = "<select class=\"flag_anulado texto_1\" " + $disabled + ">";
									if(val.flag_anulado == 0)
									{
										$html_flag_anulado = $html_flag_anulado + "<option value=\"0\" selected='selected'>No</option>";
										$html_flag_anulado = $html_flag_anulado + "<option value=\"1\">Si</option>";	
									}
									if(val.flag_anulado == 1)
									{
										$html_flag_anulado = $html_flag_anulado + "<option value=\"0\">No</option>";
										$html_flag_anulado = $html_flag_anulado + "<option value=\"1\" selected='selected'>Si</option>";
									}
										
									$html_flag_anulado = $html_flag_anulado + "</select>";
											
									if(val.flag_post == "G")							
										$flag_post = "<span style=\"color:green\"><b>Generado</b></span>";
									if(val.flag_post == "NG")							
										$flag_post = "<span style=\"color:red\"><b>Rango No Vacío</b></span>";
									
									$fila = "<tr>";
									$fila = $fila + "<td align=center>" + $i + "<input type=\"hidden\" class=\"id_comprobante_pago\" value=\"" + val.id + "\" /></td>";
									$fila = $fila + "<td align=center><b>" + val.comprobante_pago_tipo; + "</b></td>";
									$fila = $fila + "<td align=center><div class=\"lbl_nro_comprobante\"><b><span class=\"nro_comprobante\">" + val.nro_comprobante + "</span></b></div>";
									$fila = $fila + "<input type=\"hidden\" class=\"estado_proceso\" value=\"0\">";
									$fila = $fila + "<div class=\"lbl_OK lbl_resultado\">OK</div>";
									$fila = $fila + "<div class=\"lbl_FAIL lbl_resultado\">F</div>";
									$fila = $fila + "</td>";
									$fila = $fila + "<td align=center><b>" + $html_fecha_registro + "</b></td>";
									
									if($id_operacion == 1)
									{
										$fila = $fila + "<td align=center><input type=\"number\" id=\"monto_total_mn_" + $i + "\" class=\"texto_1 monto_total_mn dato\" value=\"" + parseFloat(val.monto_total_mn).toFixed(2) + "\"" + $readonly + " " + $disabled + " onkeypress='validate2(event)' tabindex=\"" + $i + "\"/></td>";																					
									}
									if($id_operacion == 3)
									{
										$fila = $fila + "<td align=center><input type=\"text\" id=\"monto_total_mn_" + $i + "\" class=\"texto_1 monto_total_mn dato " + $class_lock + "\" value=\"" + parseFloat(val.monto_total_mn).toFixed(2) + "\"" + $readonly + " " + $disabled + " onkeypress='validate2(event)' tabindex=\"" + $i + "\"/></td>";																					
									}	
									$fila = $fila + "<td align=center><input class=\"texto_1 monto_impuesto_mn dato_1\" value=\"" + parseFloat(val.monto_impuesto_mn).toFixed(2) + "\" readonly='readonly'/></td>";
									$fila = $fila + "<td align=center><input class=\"texto_1 monto_neto_mn dato_1\" value=\"" + parseFloat(val.monto_neto_mn).toFixed(2) + "\" readonly='readonly'/></td>";																	
									$fila = $fila + "<td align=center>";
									$fila = $fila + "<input type=\"hidden\" class=\"id_comp_pago_agente\" value=\"" +  val.id_comp_pago_agente + "\"/>";
									$fila = $fila + "<input type=\"text\" class=\"texto_4 comp_pago_agente dato\" value=\"" +  val.comp_pago_agente + "\" " + $readonly + " " + $disabled + " onkeypress='validate2(event)'/>";
									$fila = $fila + "</td>";
									$fila = $fila + "<td align=center>" + $html_flag_anulado + "</td>";
									$fila = $fila + "<td align=center>" + $flag_post + "</td>";
									$fila = $fila + "</tr>";
										
									$("#tabla_lista_comprobantes").append($fila);
									
									$("#fecha_registro_str_" + $i).datepicker({
										dateFormat: "dd-mm-yy",
										altField: "#fecha_registro_" + $i,
										altFormat: 'yy-mm-dd'
									});
									
									
									if($fecha != "")
										$("#fecha_registro_str_" + $i).datepicker("setDate", $fecha);
									
									$i++;								
								});
									
							}
							else
							{
								$tr = "<tr><td colspan=10><b>Sin Resultados por Mostrar</b></td></tr>";
								$("#tabla_lista_comprobantes").append($tr);
							}											
						});	
					}
					
										
				}
				
			});
			
			$(".fecha_registro_str").live("change",function()
			{
				$fecha = $(this).parent().find(".fecha_registro").val();				
				$fecha = $fecha + " 00:00:00";
				
				
				$nro_filas = $("#tabla_lista_comprobantes tr").length - 1;
				$fila_fecha = $(this).parent().find(".fila_fecha").val();
				
				$fecha = FechaFormato($fecha, "d-m-y");
				
				for($j = $fila_fecha; $j <= $nro_filas; $j++)
				{
					$("#fecha_registro_str_" + $j).datepicker("setDate", $fecha);
				}				
						
			});
			
			$("#flag_fecha").change(function()
			{
				
				$("#fecha_inicio_str").attr("disabled", "disabled");
				$("#fecha_fin_str").attr("disabled", "disabled");
				
				if($("#flag_fecha").is(':checked'))
				{
					$("#fecha_inicio_str").removeAttr("disabled");
					$("#fecha_fin_str").removeAttr("disabled");
				}
				
			});
			
			$(".monto_total_mn").live("change", function()
			{
				$tr = $(this).parent().parent();
				
				$monto_total_mn = roundNumber(parseFloat($(this).val()), 2);
				
				$monto_neto_mn = roundNumber($monto_total_mn / 1.18, 2);
				$monto_impuesto_mn = roundNumber($monto_total_mn - $monto_neto_mn, 2);
				
				$tr.find(".monto_neto_mn").val($monto_neto_mn.toFixed(2));
				$tr.find(".monto_impuesto_mn").val($monto_impuesto_mn.toFixed(2));
			});
			
			$(".monto_total_mn").live("keydown",function(event)
			{
				$nro_filas = $("#tabla_lista_comprobantes tr").length - 1;
				$tr = $(this).parent().parent();
				$nro_fila = $tr.find(".fila_fecha").val();
				
				if(event.which == 13 || event.which == 40)
				{
					$nro_fila ++;
					
					if($nro_fila <= $nro_filas)
					{
						$("#monto_total_mn_" + $nro_fila).focus();
						$("#monto_total_mn_" + $nro_fila).select();
					}
						
				}
				if(event.which == 38)
				{
					$nro_fila --;
					if($nro_fila >= 1)
						$("#monto_total_mn_" + $nro_fila).focus();
				}
			});
			
			$('.comp_pago_agente').live("click", function()
			{
				/*mywindow = showModalDialog("../clientes/buscar.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
				
				$td = $(this).parent();
				
				id_cliente = mywindow.id_cliente;
				nombre_cliente = mywindow.nombre_cliente;
				
				$id_comp_pago_agente = $td.find(".id_comp_pago_agente");
				$comp_pago_agente = $(this);
				
				$comp_pago_agente.val(nombre_cliente);
				$id_comp_pago_agente.val(id_cliente);*/
				
			});
			
			$("#btn_guardar_cambios").click(function()
			{			
				$("#operacion").val("modificar");
				
				if(confirm("Seguro que deseas Guardar los Cambios?"))
				{
					$i = 1;
					
					$('#tabla_lista_comprobantes tbody tr').each(function () 
					{
						$fila = $(this);
						
						if($("#id_operacion").val() == 2)
						{
							$fila.find(".id_comprobante_pago").attr("id", "id_comprobante_pago_" + $i);
							$fila.find(".id_comprobante_pago").attr("name", "id_comprobante_pago_" + $i);
							
							$fila.find(".flag_anulado").attr("id", "flag_anulado_" + $i);
							$fila.find(".flag_anulado").attr("name", "flag_anulado_" + $i);
							
							/*$fila.find(".monto_total_mn").attr("id", "monto_total_mn_" + $i);
							$fila.find(".monto_total_mn").attr("name", "monto_total_mn_" + $i);*/
							
							$fila.find(".fecha_registro").attr("id", "fecha_registro_" + $i);
							$fila.find(".fecha_registro").attr("name", "fecha_registro_" + $i);
							
							$fila.find(".id_comp_pago_agente").attr("id", "id_comp_pago_agente_" + $i);
							$fila.find(".id_comp_pago_agente").attr("name", "id_comp_pago_agente_" + $i);	
						}
						
						if($("#id_operacion").val() == 3)
						{
							$id_comprobante = $fila.find(".id_comprobante_pago").val();
							$fecha_registro = $fila.find(".fecha_registro").val();
							$monto_total_mn = $fila.find(".monto_total_mn").val();
							$id_comp_pago_agente = $fila.find(".id_comp_pago_agente").val();
							$flag_anulado = $fila.find(".flag_anulado").val();
							
							$estado_proceso = $fila.find(".estado_proceso").val();
							
							if($estado_proceso == 0)
							{
								$.ajax({
							        type: "POST",
							        async: false,
							        timeout: 10000,
							        url: "../procesar_comprobante_pago.php",
							        data: { id_comprobante_pago: $id_comprobante, 
							        		fecha_registro: $fecha_registro, 						        		
							        		monto_total_mn: $monto_total_mn, 
							        		id_comp_pago_agente: $id_comp_pago_agente,
							        		flag_anulado: $flag_anulado,
							        		operacion: "modificar_comprobante"
							        	},
							        success: function() 
							        {
							            $fila.find(".lbl_OK").css("display","block");
							            $fila.find(".lbl_FAIL").css("display","none");
							            $fila.find(".estado_proceso").val(2);
									},
									error: function(jqXHR, textStatus)
									{
										$fila.find(".lbl_FAIL").css("display","block");
										$fila.find(".lbl_FAIL").attr("alt", textStatus);
										$fila.find(".estado_proceso").val(0);
									},
									complete: function()
									{
										setTimeout(
											function()
											{
												$j = 0;
											}, 
											1500);										
									}		        
							    });
							}
							
						}
						
						$i++;
					  	 
					});
					
					if($("#id_operacion").val() == 3)
					{
						alert("Cambios guardados!");
						$i = 0;
						$('#tabla_lista_comprobantes tbody tr').each(function () 
						{
							$fila = $(this);
							if($fila.find(".estado_proceso").val() == 0)
							{
								$i++;
							}
						});
						
						if($i > 0)						
							$("#btn_guardar_cambios").val("Procesar " + $i + " pendiente(s)");
						else
						{
							$("#btn_guardar_cambios").val("Guardar Cambios");
							$("#btn_guardar_cambios").attr("disabled","disabled");
						}
							
						
					}
					
					if($("#id_operacion").val() == 2)
					{
						$("#nro_items").val($i - 1);
					
						$("#lista_comprobantes").submit();	
					}
					
					
				}

			});
			
		});
		
		function wait()
		{
			
		}
		
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
	<form id="lista_comprobantes" name="lista_comprobantes" method="post" action="<?php echo $enlace_procesar; ?>">
		<input id="operacion" name="operacion" type="hidden"/>
		<input id="nro_items" name="nro_items" type="hidden"/>
		<div id="div_info">
			<table id="tabla_info">
				<tr>
					<td colspan="15" align="center"><span class="titulo_1">REGISTRO DE COMPROBANTES DE PAGO MANUALES PARA TERCEROS</span></td>
				</tr>
				<tr height="20px"></tr>
				<tr>
					<td><span class="etiqueta">Tipo de Comprobante Pago: </span></td>
					<td width="10px"></td>
					<td>
						<select id="id_comprobante_pago_tipo" name="id_comprobante_pago" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							$comps = $compBLO->ListarTiposTodos();
							foreach($comps as $c)
								echo "<option value=\"$c->id\">".strtoupper($c->descripcion_corta)."</option>";							
							?>
						</select>
					</td>
					<td width="40px"></td>
					<td><span class="etiqueta">Nro. Serie: </span></td>
					<td width="20px"></td>
					<td>
						<select id="nro_serie" name="nro_serie" class="texto_2">
							<option value="0">Seleccione...</option>;
						</select>
					</td>
					<td width="40px"></td>
					<td><span class="etiqueta">Nro. Inicial: </span></td>
					<td width="20px"></td>
					<td><input type="number" class="texto_2 dato" id="nro_inicio" name="nro_inicio" maxlength="8" onkeypress='validate(event)' value="0"/></td>
					<td width="50px"></td>
					<td><span class="etiqueta">Nro. Final: </span></td>
					<td width="20px"></td>
					<td><input type="number" class="texto_2 dato" id="nro_fin" name="nro_fin" maxlength="8" onkeypress='validate(event)' value="0"/></td>
				</tr>
				<tr>
					<td><span class="etiqueta">Fecha Inicio: </span></td>
					<td width="10px"></td>
					<td>
						<input type="text" class="texto_2 dato" id="fecha_inicio_str" name="fecha_inicio_str" maxlength="8" readonly="readonly"/>
						<input type="hidden" id="fecha_inicio" name="fecha_inicio"/>
					</td>
					<td width="40px"></td>
					<td><span class="etiqueta">Fecha Fin: </span></td>
					<td width="20px"></td>
					<td>
						<input type="text" class="texto_2 dato" id="fecha_fin_str" name="fecha_fin_str" maxlength="8" readonly="readonly"/>
						<input type="hidden" id="fecha_fin" name="fecha_fin"/>
					</td>
					<td width="40px"></td>
					<td><span class="etiqueta">Operación: </span></td>
					<td width="20px"></td>
					<td>
						<select id="id_operacion" class="texto_2">
							<option value="0">Seleccione...</option>
							<option value="1">Buscar/Editar</option>														
							<?php
							if($permiso_crear_comprobante->isOK)
								echo "<option value=\"2\">Generar</option>";
							/*else
								echo "<option value=\"2\">$permiso_crear_comprobante->mensaje</option>";*/
							
							?>
							<option value="3">Registrar Nuevo</option>
						</select>					
					</td>
					<td></td>
					<td  colspan="2"><input class="texto_1_5 dato" type="button" id="btn_operacion"></td>
					<td><input type="checkbox" id="flag_fecha" checked="checked"><span class="etiqueta">Buscar Fecha</span></td>					
				</tr>
				
			</table>
		</div>
		
		<div id="div_lista_comprobantes">
			<table id="tabla_lista_comprobantes">
				<thead>
					<th width=30px>#</th>
					<th width=120px>Tipo Comprobante</th>
					<th width=120px>Nro. Comprobante</th>
					<th width=80px>Fecha</th>					
					<th width=80px>M.Total S/.</th>
					<th width=80px>I.G.V</th>
					<th width=80px>M.Neto S/.</th>
					<th width=200px>Cliente</th>
					<th width=50px>Anulado</th>
					<th width=100px>Estado</th>
				</thead>							
			</table>
			<?php
			if($permiso_modificar_comprobante->isOK)
			{
			?>
			<div id="div_guardar_cambios"><input id="btn_guardar_cambios" class="texto_3 dato_1" type="button" value="Guardar Cambios" /> </div>
			<?php
			}
			?>
		</div>
	</form>
	</div>
	
	</body>
</html>


