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
include ("../clases/anuncio.php");
include ("../clases/cliente.php");

$cliente_buscar_enlace_post = "../procesar_cliente.php";
$cliente_ruta_imagenes = "../images";
$cliente_buscar_query_cliente = "../procesar_cliente.php";
$cliente_tipo_fuente_externa = "reservas_cancha_cliente_frecuente_buscar";

if(isset($_POST["id_caja"]))
	$id_caja = $_POST["id_caja"];
else
	$id_caja = 0;

$enlace_query = "../procesar_reserva_cancha.php?id_centro=$id_centro&opcion_original_key=$opcion_key&usr_key=$usr_key";
$enlace_procesar = "../procesar_reserva_cancha.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";


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
				overflow: hidden; border-radius: 10px 10px 10px 10px; }
				
			#div_operacion {}
			#div_reserva_frecuente { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 1010px; padding: 10px 10px 10px 10px;}
			
			#tabla_reserva_frecuente { border-collapse: collapse; }
			#tabla_reserva_frecuente tbody td { border-bottom: dotted 1px #0099CC; }
			
			.etiqueta { font-weight: bold; font-size: 12px; color: #585858; font-family: Helvetica; }
			
			#opcion { font-family: Helvetica; font-size: 11px; }
			
			#opcion_general { font-family: Helvetica; font-size: 11px; }
			.operacion_reserva { font-family: Helvetica; font-size: 11px; width: 115px; }
			#hora_inicio, #hora_fin { font-family: Helvetica; font-size: 11px; width: 100px;}
			
			.dato { text-align: center; font-family: Helvetica; font-size: 11px; }
			
			.texto_0_5 { width: 30px; }
			.texto_1 { width: 35px; }
			.texto_2 { width: 100px; }
			.texto_3 { width: 135px; }
			.texto_4 { width: 200px; }
			.texto_5 { width: 235px; }
			.texto_6 { width: 300px; } 
			.texto_8 { width: 520px; }
			
			#titulo {font-size: 14px; font-weight: bold; font-family: Helvetica; color: #0099CC; text-shadow: 0.1em 0.1em 0.05em #333; }
			
			#div_horario_busqueda { border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; width: 770px; margin-top: 15px; padding: 5px 0px 5px 0px;
				display:none;  }
			#tabla_horario_busqueda { border-collapse: collapse; }
			
			#div_buscar { margin-top: 10px; width: 1000px; display: none; border-bottom: dotted 1px #0099CC; }
			
			#div_lista_horarios { margin-top: 20px; display:none; }
			#tabla_lista_horarios { border-collapse: collapse; width: 1000px; font-family: Helvetica;  }
			#tabla_lista_horarios thead th { font-size: 12px; font-weight: bold; color: #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_horarios tbody td { border-bottom: dotted 1px #0099CC; font-size: 11px; color: #585858;}
			
			
			#tabla_lista_horarios tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_lista_horarios tbody tr:nth-child(even) { background-color:#FFFFFF; }
			
			#div_operaciones_reserva_activas { margin-top: 10px; margin-bottom: 10px; width: 1000px;}
			
			#div_guardar_cambios { width: 1000px; margin-top: 10px; margin-bottom: 10px; display: none;}
			#guardar_cambios { width: 110px;}
			
			#img_cargando { display: none; }
			
			.conflicto { background-color: #FA6645; }
				
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<link rel="stylesheet" href="../styles/jquery-ui.css"/>
		<script src="../js/jquery-ui.min.js" /> </script>
		<script src="../js/jquery.livequery.js" /> </script>
		
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
		
		function ValidarReservas()
		{
			$i = 1;
			
			$('#tabla_lista_horarios tbody tr').each(function()
			{
				alert($(this).html());
				$fecha_hora_inicio = $(this).find(".fecha_hora_inicio").val();
				$fecha_hora_fin = $(this).find(".fecha_hora_inicio").val();
				$comentarios = $(this).find(".comentarios");
				$reservas = "";
				
				$i++;
				
				$url = "<?php echo $enlace_query;?>&operacion=query_horario&fecha_hora_inicio=" + $fecha_hora_inicio + "&fecha_hora_fin=" + $fecha_hora_fin;
					
				$.getJSON($url, function(data)
				{
					$comentarios.val("PRUEBA");
					if(data != null)
					{
						//alert("Encontrado");
						
						$.each(data, function(key, val) 
						{
							$reservas = $reservas + " " + "4";
							alert("Encontrado");
						});
						///$comentarios.val("PRUEBA");
						//$comentarios.addClass("conflicto");
					}
					
				});
						
			});
		}
		
		
		
		function DateAdd(timeU,byMany,dateObj) 
		{
			var millisecond=1;
			var second=millisecond*1000;
			var minute=second*60;
			var hour=minute*60;
			var day=hour*24;
			var year=day*365;
		
			var newDate;
			var dVal=dateObj.valueOf();
			switch(timeU) {
				case "ms": newDate=new Date(dVal+millisecond*byMany); break;
				case "s": newDate=new Date(dVal+second*byMany); break;
				case "mi": newDate=new Date(dVal+minute*byMany); break;
				case "h": newDate=new Date(dVal+hour*byMany); break;
				case "d": newDate=new Date(dVal+day*byMany); break;
				case "y": newDate=new Date(dVal+year*byMany); break;
			}
			return newDate;
		}
		
		function FechaFormato(fecha, formato)
		{
			//alert(fecha);
			//fecha = new Date(Date.parse(fecha,"yyyy-mm-dd HH MM ss"));
			
			fecha = new Date(Date.parse(fecha, 'Y-m-d H:i:s' ));
			
			//fecha = new Date(Date.parse(fecha));
			mes = fecha.getMonth() + 1;
			dia = fecha.getDate();
			
			nro_dia = fecha.getDay();
			
			nombre_dia = "";
			
			switch(nro_dia)
			{
				case 0: nombre_dia = "Domingo"; break;
				case 1: nombre_dia = "Lunes"; break;
				case 2: nombre_dia = "Martes"; break;
				case 3: nombre_dia = "Miércoles"; break;
				case 4: nombre_dia = "Jueves"; break;
				case 5: nombre_dia = "Viernes"; break;
				case 6: nombre_dia = "Sábado"; break;
			}
			
			hora = fecha.getHours();
			
			if(hora > 12)
			{
				hora = hora - 12;
				ampm = "PM";
			}
			else
				ampm = "AM";
				
			if(hora == 0)
				hora = 12; 
			
			switch(formato)
			{
				case "y-m-d h:m:s":
					$fecha_str = fecha.getFullYear().toString() + "-" + padStr(mes.toString()) + "-" + padStr(dia.toString()) + " " + 
					padStr(fecha.getHours().toString()) + ":" + padStr(fecha.getMinutes().toString()) + ":" + padStr(fecha.getSeconds().toString()); break;
				case "d-m-y":
					$fecha_str = padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()); break;
				case "n d-m-y":
					$fecha_str = nombre_dia + " " + padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()); break;	
				
				case "h:m a":
					$fecha_str = padStr(hora) + ":" + padStr(fecha.getMinutes().toString()) + " " + ampm; break;
			}
			
			return $fecha_str;
		}
		
		function FechaFormato2(fecha, formato)
		{
			
			mes = fecha.getMonth() + 1;
			dia = fecha.getDate();
			
			nro_dia = fecha.getDay();
			
			nombre_dia = "";
			
			switch(nro_dia)
			{
				case 0: nombre_dia = "Domingo"; break;
				case 1: nombre_dia = "Lunes"; break;
				case 2: nombre_dia = "Martes"; break;
				case 3: nombre_dia = "Miércoles"; break;
				case 4: nombre_dia = "Jueves"; break;
				case 5: nombre_dia = "Viernes"; break;
				case 6: nombre_dia = "Sábado"; break;
			}
			
			hora = fecha.getHours();
			
			if(hora > 12)
			{
				hora = hora - 12;
				ampm = "PM";
			}
			else
				ampm = "AM";
				
			if(hora == 0)
				hora = 12; 
			
			switch(formato)
			{
				case "y-m-d h:m:s":
					$fecha_str = fecha.getFullYear().toString() + "-" + padStr(mes.toString()) + "-" + padStr(dia.toString()) + " " + 
					padStr(fecha.getHours().toString()) + ":" + padStr(fecha.getMinutes().toString()) + ":" + padStr(fecha.getSeconds().toString()); break;
				case "d-m-y":
					$fecha_str = padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()); break;
				case "n d-m-y":
					$fecha_str = nombre_dia + " " + padStr(dia.toString()) + "-" + padStr(mes.toString()) + "-" + padStr(fecha.getFullYear().toString()); break;	
				
				case "h:m a":
					$fecha_str = padStr(hora) + ":" + padStr(fecha.getMinutes().toString()) + " " + ampm; break;
			}
			
			return $fecha_str;
		}
		
		function AgregarTiempo(fecha, tiempo_str)
		{
			tiempo_str = tiempo_str.replace(":", "");
			tiempo_str = tiempo_str.substring(0,4);
			
			hora = parseInt(tiempo_str.substring(0,2));
			minuto = parseInt(tiempo_str.substring(2,4));
			
			$fecha_x = fecha;
			$fecha_x = DateAdd("h", hora, $fecha_x);
			$fecha_x = DateAdd("mi", minuto, $fecha_x);
			
			return $fecha_x; 
		}
		
		function CargarHoraInicio()
		{
			$hora_inicio = $('#hora_inicio');
			$hora_inicio.append("<option value=''>Seleccione...</option>");
			var hora_valor;
			for(var i = 0; i < 48; i ++)
			{	
				if(i % 2 == 0)
					min = 0;
				else
					min = 30;
				if(i >= 24)
					ampm = "PM";
				else
					ampm = "AM";
				
				hora_valor = Math.floor(i / 2);
				
				$hora_valor_db = padStr(hora_valor) + ":" + padStr(min) + ":00";
				
				if(hora_valor > 12)
					hora_valor = hora_valor - 12;
				
				if(hora_valor == 0)
					hora_valor = 12;
					
				$hora_valor_str =  padStr(hora_valor) + ":" + padStr(min) + " " + ampm;
					        				        		
				$hora_inicio.append("<option value=\"" + $hora_valor_db + "\">" + $hora_valor_str + "</option>\n");
	
			}
		}
		
		$(function()
		{
			$fecha = new Date();
			
			$('#fecha_inicio_str').datepicker({
				minDate: 0,
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
				minDate: 0,
				dateFormat: 'dd-mm-yy', 
				altField: '#fecha_fin', 
				altFormat: 'yy-mm-dd',
        		onSelect: function(selected) 
        			{
						$("#fecha_inicio_str").datepicker("option","maxDate", selected)
        			}
				});
			$("#fecha_fin_str").datepicker("setDate", $fecha);
			
			$('#seleccionar_cliente').click(function()
			{
				//mywindow = showModalDialog("../clientes/buscar.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
				
				$('#cliente_div_main').dialog('open');
								
				//id_cliente = mywindow.id_cliente;
				//nombre_cliente = mywindow.nombre_cliente;
				
				//$('#nombre_cliente').val(nombre_cliente);								
				//$('#id_cliente').val(id_cliente);				
				
			});
			
			
			
			$("#opcion").change(function()
			{
				$opcion = $(this).val();
				
				$("#div_horario_busqueda").css("display", "none");
				$("#div_buscar").css("display", "none");
				$("#opcion_general").empty();
				
				
				if($opcion == 1)
				{
					$("#div_buscar").css("display", "block");
					$("#operacion").val("modificar_frecuente");
					$("#opcion_general").append("<option value=\"0\">Ningún Cambio</option>");
					$("#opcion_general").append("<option value=\"1\">Cancelar Todas</option>")
						
				}
					
				if($opcion == 2)
				{				
					$("#div_horario_busqueda").css("display", "block");
					CargarHoraInicio();
					$("#operacion").val("crear_frecuente");
					$("#opcion_general").append("<option value=\"2\">Crear Todas</option>")
					$("#opcion_general").append("<option value=\"0\">Ningún Cambio</option>");					
				}
				
			});
			
			$("#buscar_horario").click(function()
			{
				$fecha_inicio = $("#fecha_inicio").val();
				$fecha_fin = $("#fecha_fin").val();
				
				$id_cliente = $("#id_cliente").val();
				
				$("#div_lista_horarios").css("display", "none");
				$("#div_guardar_cambios").css("display", "none");
				
				$("#tabla_lista_horarios").find("tr:gt(0)").remove();
				
				$("#img_cargando").show();
				
				if($id_cliente > 0)
				{
					$url = "<?php echo $enlace_query;?>&operacion=query_cliente&fecha_inicio=" + $fecha_inicio + "&fecha_fin=" + $fecha_fin + "&id_cliente=" + $id_cliente;
					
					$.getJSON($url, function(data)
					{
						if(data != null)
						{
							$("#div_lista_horarios").css("display", "block");
							
							$i = 0;
							
							$operacion_reserva_html = "<select class=\"operacion_reserva\">";
							$operacion_reserva_html = $operacion_reserva_html +  "<option value=\"\">Ningún Cambio</option>";
							$operacion_reserva_html = $operacion_reserva_html +  "<option value=\"cancelar_reserva\">Cancelar</option>";
							$operacion_reserva_html = $operacion_reserva_html +  "</select>";
							
							$.each(data, function(key, val) 
							{
								$i++;
								
								$tr = "<tr>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_reserva\" value=\"" + val.id +"\"/>" + $i + "</td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha\" value=\"" + FechaFormato(val.fecha_hora_inicio, "n d-m-y") +"\"/><b>" + FechaFormato(val.fecha_hora_inicio, "n d-m-y") + "</b></td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha_hora_inicio\" value=\"" + FechaFormato(val.fecha_hora_inicio, "y-m-d h:m:s") +"\"/>" + FechaFormato(val.fecha_hora_inicio, "h:m a") + "</td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha_hora_fin\" value=\"" + FechaFormato(val.fecha_hora_fin, "y-m-d h:m:s") +"\"/>" + FechaFormato(val.fecha_hora_fin, "h:m a") + "</td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"pago_adelantado\" value=\"" + parseFloat(val.pago_adelantado).toFixed(2) +"\"/>" + parseFloat(val.pago_adelantado).toFixed(2) + "</td>";
								$tr = $tr + "<td align='center'>" + val.estado_descripcion + "</td>";
								$tr = $tr + "<td width=110px align='center'>" + $operacion_reserva_html +"</td>";
								$tr = $tr + "<td style=\"padding-left: 15px; padding-right: 15px;\"></td>";
								$tr = $tr + "</tr>";
								
								$("#tabla_lista_horarios").append($tr);

							});
							
							if($i > 0)
								$("#div_guardar_cambios").css("display", "block");
						}
					}); 						
				}
				
				$("#img_cargando").hide();
					
			});
			
			$("#generar_horario").click(function()
			{
				$fecha_inicio = $("#fecha_inicio").val();
				$fecha_fin = $("#fecha_fin").val();
				$dia_semana = $("#dia_semana").val();
				$id_cliente = $("#id_cliente").val();
				$id_centro = $("#id_centro").val();
				
				$hora_inicio = $("#hora_inicio").val();
				$hora_fin = $("#hora_fin").val();
				$comentarios = $("#comentarios").val().toUpperCase();
				
				
				$("#div_lista_horarios").css("display", "none");
				$("#div_guardar_cambios").css("display", "none");
				
				$("#tabla_lista_horarios").find("tr:gt(0)").remove();
				
				if($dia_semana != "" && $hora_inicio != "" && $hora_fin != "" && $id_cliente > 0)
				{
					
					$("#div_lista_horarios").css("display", "block");
					
					$url = "<?php echo $enlace_query;?>&operacion=generar_horarios&fecha_inicio=" + $fecha_inicio +"&fecha_fin=" + $fecha_fin + "&id_cliente=" + $id_cliente;
					$url = $url + "&nro_dia_reserva=" + $dia_semana +"&hora_inicio=" + $hora_inicio + "&hora_fin=" + $hora_fin + "&id_centro=" + $id_centro;
					
					$.getJSON($url, function(data)
					{
						if(data != null)
						{
							$i = 0;
							
							$.each(data, function(key, val) 
							{
								$i++;
								
								if(val.estado == 1)
								{
									$comentarios_res = $comentarios;
									$clase_conflicto = "";
									$readonly = "";
								}
								if(val.estado == 0)
								{
									$comentarios_res = val.comentarios;
									$clase_conflicto = " conflicto";
									$readonly = "readonly='readonly'";
								}
								
								$operacion_reserva_html = "<select class=\"operacion_reserva\">";
								$operacion_reserva_html = $operacion_reserva_html +  "<option value=\"\">Ningún Cambio</option>";
								if(val.estado == 0)
									$opcion_habilitada = "disabled='disabled'";
								else
									$opcion_habilitada = "selected='selected'";
								$operacion_reserva_html = $operacion_reserva_html +  "<option value=\"crear_reserva\" " + $opcion_habilitada + ">Crear Reserva</option>";
								$operacion_reserva_html = $operacion_reserva_html +  "</select>";
								
								$tr = "<tr>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_reserva\" value=\"" + $i +"\"/>" + $i + "</td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha\" value=\"" + FechaFormato(val.fecha_hora_inicio, "n d-m-y") +"\"/><b>" + FechaFormato(val.fecha_hora_inicio, "n d-m-y") + "</b></td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha_hora_inicio\" value=\"" + val.fecha_hora_inicio +"\"/>" + val.fecha_hora_inicio_str + "</td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha_hora_fin\" value=\"" + val.fecha_hora_fin +"\"/>" + val.fecha_hora_fin_str + "</td>";
								$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"pago_adelantado\" value=\"0.0\"/>0.00</td>";
								$tr = $tr + "<td align='center'>" + $operacion_reserva_html + "</td>";
								$tr = $tr + "<td width=110px align='center'></td>";								
								$tr = $tr + "<td align='center'><input type=\"text\" class=\"dato texto_4 comentarios" + $clase_conflicto +"\" value=\"" + $comentarios_res +"\" " + $readonly + "/></td>";
								$tr = $tr + "</tr>";
								
								$("#tabla_lista_horarios").append($tr);
							});
							
							if($i > 0)
								$("#div_guardar_cambios").css("display", "block");
						}
						
					});
					
					
				}
				else
					alert("Alguno de los siguientes Valores se encuentra errado:\n\n+ Día de Reserva\n+ Hora de Inicio de Reserva\n+ Hora de Fin de Reserva\n+ Cliente");
				
				
			})
			
			$("#opcion_general").live("change",function()
			{
				$opcion = $("#opcion_general").val();
				
				if($opcion == 1)
				{
					$('#tabla_lista_horarios tr').each(function()
			  		{
			  			$(this).find(".operacion_reserva").val("cancelar_reserva");		
			  		});
				}
				
				if($opcion == 2)
				{
					$('#tabla_lista_horarios tr').each(function()
			  		{
			  			$(this).find(".operacion_reserva").val("crear_reserva");		
			  		});
				}
				
				if($opcion == 0)
				{
					$('#tabla_lista_horarios tr').each(function()
			  		{
			  			$(this).find(".operacion_reserva").val("");		
			  		});
				}
			});
			
			$("#guardar_cambios").live("click", function()
			{
				$i = 1;
				$('#tabla_lista_horarios tbody tr').each(function()
			  	{
			  		$(this).find(".id_reserva").attr("id","id_reserva_" + $i);
			  		$(this).find(".id_reserva").attr("name","id_reserva_" + $i);
			  		
			  		$(this).find(".operacion_reserva").attr("id","operacion_reserva_" + $i);
			  		$(this).find(".operacion_reserva").attr("name","operacion_reserva_" + $i);
			  		
			  		$(this).find(".fecha_hora_inicio").attr("id","fecha_hora_inicio_" + $i);
			  		$(this).find(".fecha_hora_inicio").attr("name","fecha_hora_inicio_" + $i);
			  		
			  		$(this).find(".fecha_hora_fin").attr("id","fecha_hora_fin_" + $i);
			  		$(this).find(".fecha_hora_fin").attr("name","fecha_hora_fin_" + $i);
			  		
			  		$i++;
			  	});
			  	
			  	$nro_reservas = $i - 1 ;
			  	
			  	$("#nro_reservas").val($nro_reservas);
			  	
			  	$("#reserva").submit();
			});
			
			$("#hora_inicio").change(function()
			{
				$hora_inicio = $("#hora_inicio").val();
				$hora_fin = $("#hora_fin");
				
				$hora_inicio = $hora_inicio.replace(":", "");
				$hora_inicio = $hora_inicio.substring(0, 4);
				$hora_inicio = parseInt($hora_inicio);
				
				$hora_fin.empty();
				$hora_fin.append("<option value=\"\">Seleccione...</option>");
				
				for(var i = 0; i < 48; i ++)
				{	
					if(i % 2 == 0)
						min = 0;
					else
						min = 30;
					if(i >= 24)
						ampm = "PM";
					else
						ampm = "AM";
					
					hora_valor = Math.floor(i / 2);
					
					$hora_valor_db = padStr(hora_valor) + ":" + padStr(min) + ":00";
					$hora_int = hora_valor + "" + padStr(min);
					$hora_int = parseInt($hora_int);
					
					if(hora_valor > 12)
						hora_valor = hora_valor - 12;
					
					if(hora_valor == 0)
						hora_valor = 12;
					
					$hora_valor_str =  padStr(hora_valor) + ":" + padStr(min) + " " + ampm;
					
					if($hora_int > $hora_inicio)
						$hora_fin.append("<option value=\"" + $hora_valor_db + "\">" + $hora_valor_str + "</option>\n");
				}
				$hora_fin.append("<option value=\"23:59:59\">11:59 PM</option>");
			});
			
			$(document).live("ajaxStop", function (e) 
			{
	      		//Dialogo Buscar Cliente
	      		$("#cliente_div_main").dialog("option", "position", "center");												
			});
			
		});
			
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
		<form id="reserva" name="reserva" method="post" action="<?php echo $enlace_procesar; ?>">
			<input id="id_usuario" name="id_usuario" type="hidden" value="<?php echo $id_usuario;?>" />
			<input id="id_cliente" name="id_cliente" type="hidden"/>
			<input id="id_centro" name="id_centro" type="hidden"  value="<?php echo $id_centro;?>" />
			<input id="nro_reservas" name="nro_reservas" type="hidden"/>
			<input id="operacion" name="operacion" type="hidden"/>
			<div id="div_reserva_frecuente">
				<div id="div_operacion">
					
				</div>
				<table id="tabla_reserva_frecuente">
					<tr><td colspan="15" align="center" style="border-bottom: none;"><span id="titulo">RESERVAS FRECUENTES DE CANCHA</span></td></tr>
					<tr height="15pxs"><td colspan="15"></td></tr>
					<tr>
						<td><span class="etiqueta">Cliente: </span></td>
						<td width="20px"></td>
						<td>
							<input class="texto_4 dato" type="text" id="nombre_cliente" name="nombre_cliente" readonly="readonly"/>
							<input class="texto_0_5 dato" type="button" id="seleccionar_cliente" value="..." /> 
						</td>
						<td width="35px"></td>
						<td><span class="etiqueta">Fecha Inicio: </span></td>
						<td width="20px"></td>
						<td>
							<input class="texto_2 dato" type="text" id="fecha_inicio_str" name="fecha_inicio_str" readonly="readonly"/>
							<input type="hidden" id="fecha_inicio" name="fecha_inicio"/> 
						</td>
						<td width="35px"></td>
						<td><span class="etiqueta">Fecha Fin: </span></td>
						<td width="20px"></td>
						<td>
							<input class="texto_2 dato" type="text" id="fecha_fin_str" name="fecha_fin_str" readonly="readonly"/>
							<input type="hidden" id="fecha_fin" name="fecha_fin"/> 
						</td>
						<td width="35px"></td>
						<td><span class="etiqueta">Operación: </span></td>
						<td width="20px"></td>
						<td>
							<select id="opcion" class="texto_2">
								<option value="0">Seleccione...</option>
								<option value="1">Buscar</option>
								<option value="2">Generar Horarios</option>
							</select> 
							

						</td>
					</tr>
				</table>
			</div>
			<div id="div_opciones_busqueda">	
				<div id="div_horario_busqueda">
					<table id="tabla_horario_busqueda">
						<tr>
							<td><span class="etiqueta">Día Semana:</span></td>
							<td width="10px"></td>
							<td>
								<select id="dia_semana" class="dato texto_3">
									<option value="">Seleccione...</option>
									<option value="0">Domingo</option>
									<option value="1">Lunes</option>
									<option value="2">Martes</option>
									<option value="3">Miércoles</option>
									<option value="4">Jueves</option>
									<option value="5">Viernes</option>
									<option value="6">Sábado</option>
									
								</select>
							</td>
							<td width="20px"></td>
							<td><span class="etiqueta">Hora Inicio:</span></td>
							<td width="10px"></td>
							<td>
								<select id="hora_inicio">
								</select>
							</td>
							<td width="20px"></td>
							<td><span class="etiqueta">Hora Fin:</span></td>
							<td width="10px"></td>
							<td>
								<select id="hora_fin">
								</select>
							</td>
							<td width="20px"></td>
							<td rowspan="2" valign="middle"><input type="button" class="dato texto_2" id="generar_horario" value="Generar" /></td>
						</tr>
						<tr>
							<td><span class="etiqueta">Comentarios:</span></td>
							<td></td>
							<td colspan="10"><input id="comentarios" class="texto_8 datos"/></td>
						</tr>
					</table>
				</div>
				<div id="div_buscar">
					<input type="button" class="dato texto_2" value="Buscar" id="buscar_horario" />
					<img id="img_cargando" src="../images/loading.gif" />
				</div>
			</div>
			<div id="div_lista_horarios">
				<div id="div_operaciones_reserva_activas" align="left">
					<span class="etiqueta">Operación General: </span>
					<select id="opcion_general">
						
					</select>
				</div>
				<table id="tabla_lista_horarios">
					<thead>
						<th width=20px>#</th>
						<th width=120px>Fecha</th>
						<th width=80px>Hora Inicio</th>
						<th width=80px>Hora Fin</th>
						<th width=100px>M. Adelanto S/.</th>
						<th width=60px>Estado</th>
						<th width=105px>Operación</th>
						<th width=300px>Comentarios</th>
					</thead>
				</table>
				<div id="div_guardar_cambios">
					<input type="button" class="dato" value="Guardar Cambios" id="guardar_cambios">
				</div>
			</div>	
		</form>
	</div>
	<?php		
		include ('../clientes/buscar.php');
	?>
	</body>
</html>