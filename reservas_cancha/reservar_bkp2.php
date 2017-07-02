<?php

session_start();

date_default_timezone_set("America/Lima");

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

$hora_inicial = 0;
$opcion_query_cliente = "24QZZ19Q";
$opcion_ver_detalle_reserva_cancha = "D01BO62A";
$opcion_crear_reserva_cancha = "EXT53D14";
$opcion_modificar_reserva_cancha = "EO5JZ294";
$opcion_cancelar_reserva_cancha = "YY9M6M15";
$opcion_transaccion_sin_turno = "J3FMA841";
$opcion_ingresar_ventas_otro_turno = "84I3F3HS";

$cliente_buscar_enlace_post = "../procesar_cliente.php";
$cliente_buscar_query_cliente = "../procesar_cliente.php";
$cliente_ruta_imagenes = "../images";
$cliente_tipo_fuente_externa = "reservas_cancha_cliente_buscar";


include ('../clases/enc_dec.php');
include ('../clases/usuario.php');
include ('../clases/opcion.php');
include ('../clases/general.php');
include ('../clases/security.php');
include ('../clases/reserva_cancha.php');
include ('../clases/centro.php');
include ("../clases/anuncio.php");
include ("../clases/caja.php");
include ("../clases/turno_atencion.php");
include ("../clases/cliente.php");

$id_usuario = $usuario -> id;

$cenBLO = new CentroBLO();
$opcBLO = new OpcionBLO();
$caBLO = new CajaBLO();
$resBLO = new ReservaCanchaBLO();
$taBLO = new TurnoAtencionBLO();

$permiso_ver_detalle_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_ver_detalle_reserva_cancha, $usuario -> id, $id_centro);

$permiso_crear_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_crear_reserva_cancha, $usuario -> id, $id_centro);
$permiso_modificar_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_modificar_reserva_cancha, $usuario -> id, $id_centro);
$permiso_cancelar_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_cancelar_reserva_cancha, $usuario -> id, $id_centro);

$permiso_registrar_transaccion_sin_turno = $opcBLO -> ValidarOpcionXIdUsuario($opcion_transaccion_sin_turno, $usuario -> id, $id_centro);
$permiso_ingresar_ventas_otro_turno = $opcBLO -> ValidarOpcionXIdUsuario($permiso_ingresar_ventas_otro_turno, $usuario -> id, $id_centro);

function GetDays($sStartDate, $sEndDate) {

	$sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
	$sEndDate = gmdate("Y-m-d", strtotime($sEndDate));
	$aDays[] = $sStartDate;
	$sCurrentDate = $sStartDate;

	while ($sCurrentDate < $sEndDate) {
		$sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
		$aDays[] = $sCurrentDate;
	}

	return $aDays;
}

if (isset($_GET["fecha"]))
	$fecha = $_GET["fecha"];
else
	$fecha = date("Y-m-d");

$fecha_mostrar = date("d/m/Y", strtotime(date('Y-m-d', strtotime($fecha))));

//************ ASIGNACION DE FECHA DE INICIO Y FECHA FIN******************

$w = date("w", strtotime(date('Y-m-d', strtotime($fecha))));
echo "W: $w";
$dia_1 = date('Y-m-d', strtotime(($w == 0 ? -6 : $w - ((2*$w)-1))." days", strtotime($fecha)));
$dia_7 = date('Y-m-d', strtotime(($w == 0 ? 0 : 6 - $w + 1)." days", strtotime($fecha)));



/*$dia_semana = date("w", strtotime(date('Y-m-d', strtotime($fecha))));
$custom_date = strtotime(date('Y-m-d', strtotime($fecha)));


if ($dia_semana == 0) {
	$dia_1 = date('Y-m-d', strtotime('last week monday', $custom_date));
	$dia_7 = date("Y-m-d", strtotime(date('Y-m-d', strtotime($fecha))));

} else {
	if ($dia_semana == 1)
		$dia_1 = date('Y-m-d', strtotime('this week monday', $custom_date));
	else
		$dia_1 = date('Y-m-d', strtotime(' last monday', $custom_date));
	$dia_7 = date('Y-m-d', strtotime('next sunday', $custom_date));
}*/


$dia_1_str = date("d/m/Y", strtotime(date('Y-m-d', strtotime($dia_1))));
$dia_7_str = date("d/m/Y", strtotime(date('Y-m-d', strtotime($dia_7))));

//************************************************************************

$dias = GetDays($dia_1, $dia_7);

$enlace_procesar = "../procesar_reserva_cancha.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_query_turno = "../procesar_turno_atencion.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";


if($permiso_registrar_transaccion_sin_turno->isOK)
	$enlace_query_turno = "$enlace_query_turno&operacion=query_turnos_activos_usuario";
else 
	$enlace_query_turno = "$enlace_query_turno&operacion=query_turnos_activos";
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>RODO </title>
		<meta name="author" content="Jesus Rodriguez" />
		
        <script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
        <script src="../js/jquery.fixedheadertable.js"></script>        
		<script src="../js/jquery.autocomplete-min.js"></script>
		<script src="../js/jquery.validate.min.js"></script>
		<script src="../js/additional-methods.js"></script>
		<script src="../js/additional-methods.min.js"></script>
		<script src="../js/messages_es.js"></script>
		
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
        
        <link rel="stylesheet" href="../styles/jquery-ui.css"/>
		<script src="../js/jquery-ui.min.js" /> </script>
		<script src="../js/jquery.livequery.js" /> </script>
		
		
		<style media="screen" type="text/css">
			body {
				background-color: #F1F1F1;
			}

			/* Table with Fixed Header */
			.fht-table, .fht-table thead, .fht-table tfoot, .fht-table tbody, .fht-table tr, .fht-table th { margin: 0; padding: 0; font-size: 100%; font: inherit; vertical-align: top; }
			.fht-table td { margin: 5px; padding: 0; font-size: 100%; font: inherit; vertical-align: top; }
 			.fht-table thead th { height: 38px; padding-top: 1px; }
			.fht-table { border-collapse: collapse; border-spacing: 0; }
			.fht-table-wrapper, .fht-table-wrapper .fht-thead, .fht-table-wrapper .fht-tfoot, .fht-table-wrapper .fht-fixed-column .fht-tbody, .fht-table-wrapper .fht-fixed-body .fht-tbody, .fht-table-wrapper .fht-tbody {
				overflow: hidden; position: relative; }
			.fht-table-wrapper .fht-fixed-body .fht-tbody, .fht-table-wrapper .fht-tbody { overflow: auto; }
			.fht-table-wrapper .fht-table .fht-cell { overflow: hidden; height: 1px; }
			.fht-table-wrapper .fht-fixed-column, .fht-table-wrapper .fht-fixed-body { top: 0; left: 0; position: absolute; }
			.fht-table-wrapper .fht-fixed-column { z-index: 1; }
			/* Table with Fixed Header */

			#div_main {
				width: 1250px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; margin: 10px auto; overflow: hidden; background-color: #FFFFFF; font-family: Helvetica; }

			#div_reservas_titulo {}
			#reservas_titulo { font-family: Helvetica; font-size: 18px; font-weight: bold; color: #0099CC; }
			#ir_a_fecha { font-family: Helvetica; font-size: 12px; float: right; font-weight: bold; margin-right: 10px; }
			#ir_a_fecha:hover { cursor: pointer; }

			#fecha_mostrar { font-family: Helvetica; font-size: 11px; width: 60px; }

			#div_reservas_semana { color: #585858; margin-bottom: 20px; width: 1230px; border-radius: 10px 10px 10px 10px; }
			#div_tabla_reservas {
				width: 1225px; height: 600px; overflow-x: auto; overflow-y: auto; display: inline; float: left; border: solid 1px #3399FF; margin-left: 7px; border-radius: 8px 8px 8px 8px; margin-bottom: 10px;
				margin-top: 3px; }
			#tabla_reservas {
				border-collapse: collapse; width: 1210px; font-size: 12px; font-family: Helvetica; }
			#tabla_reservas body td { height: 18px; }
			#tabla_reservas tbody tr:hover { background-color: #F5FCA6; }

			#tabla_reservas thead th { width: 160px; border: none; }

			.div_fecha_titulo { float: left; width: 159px; background-color: #333333; color: #FFFFFF; border-radius: 8px 8px 8px 8px; padding-top: 3px; padding-bottom: 3px; }
			.div_fecha_titulo .div_label { height: 16px; }
			.span_fecha_titulo_nombre_dia { font-family: Helvetica; font-size: 14px; font-weight: bold; }
			.span_fecha_titulo_fecha { font-family: Helvetica; font-size: 11px; }

			.div_horario_0 { border-radius: 5px 5px 5px 5px; height: 15px; width: 80px; background-color: #0099CC; margin-top: 1px; margin-bottom: 1px; }
			.div_horario_30 { border-radius: 5px 5px 5px 5px; height: 15px; width: 60px; border: solid 1px #0099CC; margin-top: 1px; margin-bottom: 1px; }
			.hora_0 { font-size: 12px; font-weight: bold; }
			.hora_30 { font-size: 11px; margin-top: 4px; }

			.td_horario_0 { border-right: solid 1px #3399FF; border-top: solid 1px #3399FF; border-left: none; }
			.td_horario_30 { border-right: solid 1px #3399FF; border-top: dotted 1px #3399FF; }

			.td_fecha_hora_reserva { border: dotted 1px #3399FF; width: 160px; position: relative; }
			.td_fecha_hora_reserva:hover { cursor: pointer; background-color: #F1F1F1; }

			.td_fecha_hora_reserva_libre { border: dotted 1px #3399FF; }
			.td_fecha_hora_reserva_libre:hover {
				cursor: pointer;
				background-color: rgba(0, 153, 204, 0.3);
			}

			.div_cliente { width: 158px; border-radius: 5px 5px 5px 5px; height: 95%; display: inline-block; font-family: Helvetica; font-size: 10px; width: 158px; margin-left: 1px; }
			.div_cliente:hover { background-color: #585858; color: #FFFFFF; font-weight: bold; }
			.div_cliente p { display: table-cell; vertical-align: middle; text-align: center; }

			#aux_comentarios { resize: none; }
			.ui-dialog { border: dotted 1px #0099CC; }
			.ui-dialog-titlebar { border: dotted 1px #0099CC; }
			.ui-dialog-title { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; }

			#nombre_cliente { text-transform: uppercase; font-size: 11px; width: 240px; text-align: center;  }
			.boton_operacion {  font-size: 11px; }
			#btn_agregar_cliente { width: 22px; height; 20px; font-weight: bold; font-size: 20px; font-family: Helvetica;}			
			.etiqueta { font-size: 11px; font-family: Helvetica; font-weight: bold;  }

			.texto_1 { font-family: Helvetica;width: 60px; text-align: center; font-size: 11px; color: #585858; }
			.texto_1_5 { font-family: Helvetica; width: 75px; text-align: center; font-size: 11px; color: #585858; }
			.texto_2 { font-family: Helvetica; width: 100px; text-align: center; font-size: 11px; color: #585858; }
			.texto_3 { font-family: Helvetica; width: 150px; text-align: center; font-size: 11px; color: #585858; }
			.texto_3_5 { font-family: Helvetica; width: 180px; text-align: center; font-size: 11px; color: #585858; }
			.texto_4 { font-family: Helvetica; width: 200px; text-align: center; font-size: 11px; color: #585858; }
			.texto_4 { font-family: Helvetica; width: 200px; text-align: center; font-size: 11px; color: #585858; }
			.texto_4_5 { font-family: Helvetica; width: 230px; text-align: center; font-size: 11px; color: #585858; }
			.texto_5 { font-family: Helvetica; width: 270px; text-align: center; font-size: 11px; color: #585858; }
			.texto_10 { font-family: Helvetica; width: 400px; text-align: center; font-size: 11px; color: #585858; }

			.ui-widget input { font-family: Helvetica; }
			.ui-widget select { font-family: Helvetica; }

			#div_leyenda_estados { border: dotted 1px #0099CC; margin-top: 10px; padding: 10px 10px 10px 10px; width: 980px; height: 18px; border-radius: 10px 10px 10px 10px; background-color: #FFFFFF; margin: 0 auto 10px; overflow: hidden; }
			.div_reserva_estado { float: left; margin-left: 10px; border: dotted 1px #0099CC; border-radius: 5px 5px 5px 5px; background-color: #FFFFFF; padding-left: 5px; }
			.leyenda_estado { width: 40px; height: 15px; float: left; margin-left: 10px; border-radius: 5px 5px 5px 5px; }
			.etiqueta_leyenda { float: left; }
			
			
			
		</style>
		
		<script type="text/javascript">
		
		function Left(str, n)
		{
			if (n <= 0)
				return "";
			else if (n > String(str).length)
				return str;
			else
				return String(str).substring(0,n);
		}
			
		function Right(str, n)
		{
			if (n <= 0)
				return "";
			else 
				if (n > String(str).length)
					return str;
				else 
				{
					var iLen = String(str).length;
					return String(str).substring(iLen, iLen - n);
				}
		}
		
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key . "&id_centro=$id_centro"; ?>";
		}

		function LimpiarValoresPOST()
		{
			$("#fecha_hora_inicio").val("");
			$("#fecha_hora_fin").val("");
			$("#id_cliente").val(0);
			$("#id_reserva").val(0);
			$("#pago_adelantado_mn").val("0.00");
			$("#aux_pago_adelantado").val("0.00")
			$("#nombre_cliente").val("");
			$("#comentarios").val("");
			$("#id_turno_atencion").val(0);
			$("#id_caja").val(0);
			$("#aux_comentarios").val("");
			$("#aux_opcion_reserva").val(0);
			$("#hora_inicio_numero").val("");
			$("#fecha").val("");
			$("#operacion").val("");
		}

		function TransformarHora(nro)
		{
			var min = Right(nro, 2);
			var hora = Left(nro, 2 - (4 - nro.length));

			var hora_valor = "" + hora;
			var pad = "00";
			hora_valor = pad.substring(0, pad.length - hora_valor.length) + hora_valor;
			hora_valor = hora_valor + ":" + min + ":" + "00";
			return hora_valor;
		}

		function TransformarHoraANro(hora_texto)
		{
			var hora = hora_texto.substring(0, 2);
			var min = hora_texto.substring(3, 5);

			if(hora.substring(0,1) == "0")
				hora = hora.substring(1,2);

			return hora + "" + min;
		}

		function TransformarHoraAMPM(nro)
		{
			var min = Right(nro, 2);
			var hora = Left(nro, 2 - (4 - nro.length));
			var ampm;

			if(hora >= 12 && hora != 24)
			{
				hora = hora - 12;
				ampm = "PM.";
			}
			else
				ampm = "AM.";

			if(hora == 0)
				hora = 12;

			if(hora == 24)
			{
				hora = 12;
				ampm = "AM.";
			}

			hora_valor = "" + hora;
			var pad = "00";
			hora_valor = pad.substring(0, pad.length - hora_valor.length) + hora_valor;
			hora_valor = hora_valor + ":" + min + " " + ampm;
			return hora_valor;
		}

		function TransformarAAMPM(texto)
		{
			var hora = texto.substring(0, 2);
			var min = texto.substring(3, 5);
			var ampm ;

			if(hora.substring(0,1) == "0")
				hora = hora.substring(1,2);

			hora = parseInt(hora);

			if(hora > 12 && hora != 24)
			{
				ampm = "PM.";
				hora = hora - 12;
			}
			else
				ampm = "AM.";

			if(hora == 24)
			{
				hora = 0;
				ampm = "AM.";
			}

			if(hora == 0)
				hora = 12;

			hora_valor = "" + hora;
			var pad = "00";
			hora_valor = pad.substring(0, pad.length - hora_valor.length) + hora_valor;
			hora_valor = hora_valor + ":" + min + " " + ampm;
			return hora_valor;
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

		$(function()
		{
			$('#fecha_mostrar').datepicker({dateFormat: 'dd/mm/yy', altField: '#fecha_mostrar_db', altFormat: 'yy-mm-dd'});
		    $("#fecha_mostrar").datepicker("setDate","<?php echo $fecha_mostrar;?>");
		    
		    $("#fecha_mostrar").change(function()
	    	{
	    		$fecha = $("#fecha_mostrar_db").val();
				location.href = "reservar.php?<?php echo "usr_key=$usr_key&id_centro=$id_centro&opcion_key=$opcion_key";?>&fecha="+$fecha;
	    	})
			
			$('#tabla_reservas').fixedHeaderTable({
				footer: true, 
				cloneHeadToFoot: true, 
				altClass: 'odd', 
				autoShow: false
			});
			$('#tabla_reservas').fixedHeaderTable('show', 1000);

			$(".div_cliente").each(function()
			{
				$dch = $(this).height();
				$div_cliente_nombre = $(this).find(".div_cliente_nombre");
				$dcnh = $div_cliente_nombre.height();

				$margin_top = ($dch - $dcnh) / 2;

				$div_cliente_nombre.css("margin-top", $margin_top);

				$td = $(this).parent();
				$dc = $(this);

				$margin_top = ($td.height() - $dc.height()) / 2;
			//$margin_top = $margin_top.toFixed(0);

				$dc.css("margin-top", $margin_top);

			});
			
			$url_query_cliente = "<?php echo $cliente_buscar_query_cliente;?>" + "?operacion=query2&nombres=";
	
			$("#nombre_cliente").autocomplete({
				source: function (request, response) {
					$.ajax({
						url: $url_query_cliente + request.term,
						dataType: "json",
						type: "POST",
						success: function (data) {
							response($.map( data, function(item) {
								return{
									value: item.id,
									label: item.nombres+ ' '+item.apellidos+'['+item.tipo_documento.substring(0,3)+':'+item.nro_documento+']',
									nombres: item.nombres,
									apellidos: item.apellidos							
								}
							}))
						}
					});
				},
				select: function(event, ui) {
					event.preventDefault();					
					$("#id_cliente").val(ui.item.value);
					$("#nombre_cliente").val(ui.item.nombres+" "+ui.item.apellidos);
				},/*,
					    focus: function(event, ui) {
					        event.preventDefault();
					        $("#descripcion").val(ui.item.label);
					    },
					    change: function(event, ui)
					    {
					    	if($("#descripcion").val() != $("#descripcion_corta_aux").val())
					    		$("#id_producto").val(0);
					    }*/
				minLength: 3 
			});

			$("#div_reserva").dialog(
			{
				autoOpen: false,
				height: 410,
				width: 450,
				modal: true,
				resizable: false,
				title: "Detalle de la Reserva"
			});

			$(".celda_horario").live("click",function()
			{
				$("#id_reserva").val(0);
				$("#reserva_key").val("");
				$("#aux_pago_adelantado").val("0.00");
				$("#aux_comentarios").val("");
				$("#aux_fecha_hora_registro").val("");
				$("#aux_usuario_creador").val("");
				$("#operacion").val("");
				$("#id_cliente").val(0);
				$("#aux_opcion_reserva").val(0);
				$("#aux_id_caja").val(0);
				$("#aux_id_turno_atencion").val(0);

				$reserva_fecha_str = $(this).find(".fecha_str").val();
				$reserva_fecha = $(this).find(".fecha").val();
				$reserva_hora_inicio_numero = $(this).find(".hora_inicio_numero").val();
				$reserva_hora_inicio_str = $(this).find(".hora_inicio_str").val();
				$reserva_hora_inicio = $(this).find(".hora_inicio").val();
				$reserva_nombre_cliente = $(this).find(".nombre_cliente").val();
				$reserva_fecha_hora_registro = $(this).find(".fecha_hora_registro").val();
				$reserva_usuario_creador = $(this).find(".usuario_creador").val();
				$reserva_key = $(this).find(".reserva_key").val();
				
				$reserva_hora_fin_str = $(this).find(".hora_fin_str").val();
				$reserva_hora_fin = $(this).find(".hora_fin").val();
				
				$reserva_pago_adelantado = $(this).find(".pago_adelantado").val();
				$comentarios = $(this).find(".comentarios").val();
				$estado = $(this).find(".estado").val();
				$reserva_hora_inicio = $reserva_fecha + " " + $reserva_hora_inicio;
				$reserva_hora_fin = $reserva_fecha + " " + $reserva_hora_fin;
				
				$id_reserva = $(this).find(".id_reserva").val();
				$id_cliente = $(this).find(".id_cliente").val();
				
				$("#id_reserva").val($id_reserva);
				$("#id_cliente").val($id_cliente);
				$("#reserva_key").val($reserva_key);

				$opcion_hora_inicio = "<option value=\"" + $reserva_hora_inicio + "\">" + $reserva_hora_inicio_str + "</option>";

				$("#reserva_fecha").val($reserva_fecha_str);

				$("#aux_hora_inicio").empty();
				$("#aux_hora_inicio").append($opcion_hora_inicio);
				
				$("#aux_pago_adelantado").removeAttr("readonly");

				$("#aux_hora_fin").empty();
				$("#aux_comentarios").val("");
				
				$(".opciones_reserva_nueva").css("display", "none");
				$(".opciones_reserva").css("display", "none");
				$(".opcion_pago").css("display", "none");
				
				$("#aux_pago_adelantado").attr("readonly", "readonly");
				$("#aux_comentarios").attr("readonly", "readonly");
				$("#btn_seleccionar_cliente").attr("disabled","disabled");
				$("#btn_guardar").attr("disabled", "disabled");
				
				$("#hora_inicio_numero").val($reserva_hora_inicio_numero);
				$("#fecha").val($reserva_fecha);
				
				if($id_reserva == 0)
				{
					
					$(".opciones_reserva_nueva").css("display", "block");
					$("#aux_pago_adelantado").removeAttr("readonly");
					$("#aux_comentarios").removeAttr("readonly");
					$("#btn_seleccionar_cliente").removeAttr("disabled");
					$("#btn_guardar").removeAttr("disabled");
					$("#operacion").val("crear");
					$("#id_cliente").val(0);
					$("#nombre_cliente").val("");
					$("#nombre_cliente").removeAttr("readonly");
					$("#btn_agregar_cliente").css("display","block");
					
					$("#aux_hora_fin").append("<option value=''>Seleccione...</option>");
					for(var i = 0; i < 48; i ++)
					{
						if(i % 2 == 0)
							min = "00";
						else
							min = "30";
	
						if(i >= 24)
							ampm = "PM";
						else
							ampm = "AM";
	
						hora_valor = Math.floor(i / 2);
						hora_valor = hora_valor + "" + min;
	
						if(parseInt(hora_valor) > parseInt($reserva_hora_inicio_numero))
							$("#aux_hora_fin").append('<option value="' + $reserva_fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
					}
					
					//Agregando último horario
					hora_valor = "2359";
					if(parseInt(hora_valor) > parseInt($reserva_hora_inicio_numero))
						$("#aux_hora_fin").append('<option value="' + $reserva_fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
				}
				else
				{
					$("#btn_agregar_cliente").css("display","none");
					$("#aux_opcion_reserva").empty();
					$("#aux_opcion_reserva").append("<option value=\"0\">Seleccione...</option");
					<?php if($permiso_ver_detalle_reserva_cancha->isOK)
						echo "$(\"#aux_opcion_reserva\").append(\"<option value=\\\"1\\\">Ver Detalle</option>\");\n";?>
					if($estado == 1 || $estado == 6) //Reservada y Reservada Frecuente
					{
						$("#aux_opcion_reserva").append("<option value=\"2\">Modificar Reserva</option");
						$("#aux_opcion_reserva").append("<option value=\"3\">Ingresar Pago y Cerrar</option");
						$("#aux_opcion_reserva").append("<option value=\"4\">Cerrar Reserva</option");
						$("#aux_opcion_reserva").append("<option value=\"5\">Cancelar Reserva</option");
						$("#aux_opcion_reserva").append("<option value=\"6\">Cerrar (Cliente NO Llegó)</option");
					}
						
					$(".opciones_reserva").css("display", "block");
					
					$("#aux_pago_adelantado").attr("readonly", "readonly");
					$("#aux_comentarios").attr("readonly", "readonly");
					
					
					$("#aux_hora_fin").append("<option value=\"" + $reserva_hora_fin + "\">" + $reserva_hora_fin_str + "</option>");
					$("#nombre_cliente").val($reserva_nombre_cliente);
					$("#nombre_cliente").attr("readonly","readonly");
					$("#aux_pago_adelantado").val($reserva_pago_adelantado);
					$("#aux_comentarios").val($comentarios);
					
					$("#aux_fecha_hora_registro").val($reserva_fecha_hora_registro);
					$("#aux_usuario_creador").val($reserva_usuario_creador);
					
				}
				
				$( "#div_reserva" ).dialog("open" );
				
			});

			$("#btn_cerrar").click(function()
			{
				LimpiarValoresPOST();
				$( "#div_reserva" ).dialog("close" );
			});
			
			$(".ui-dialog-titlebar-close").click(function()
			{
				LimpiarValoresPOST();
			});

			$("#btn_seleccionar_cliente").click(function()
			{
				//mywindow = showModalDialog("../clientes/buscar.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");

				//var cliente = $.cookie("id_clientex");
				
				$('#cliente_div_main').dialog('open');
				
				/*id_cliente = mywindow.id_cliente;
				nombre_cliente = mywindow.nombre_cliente;*/
				
			});

			$("#btn_guardar").click(function()
			{
				$id_cliente = $("#id_cliente").val();
				$hora_inicio = $("#aux_hora_inicio").val();
				$hora_fin = $("#aux_hora_fin").val();
				$pago_adelantado = $("#aux_pago_adelantado").val();
				$pago = $("#aux_pago").val();
				$id_reserva = $("#id_reserva").val();
				$id_caja = $("#aux_id_caja").val();
				$id_turno_atencion = $("#aux_id_turno_atencion").val();
				$comentarios = $("#aux_comentarios").val();
				$operacion = $("#operacion").val();
				$id_reserva = $("#id_reserva").val();

				$msg_error = "";

				if($id_cliente == 0 && ($operacion == "crear" || $operacion == "modificar"))
					$msg_error = $msg_error + "+ No ha seleccionado Cliente.\n";

				if($hora_inicio == "" && $operacion =="modificar")
					$msg_error = $msg_error + "+ No ha seleccionado Hora Inicio.\n";
				
				if($hora_fin == "" && ($operacion == "crear" || $operacion == "modificar"))
					$msg_error = $msg_error + "+ No ha seleccionado Hora Fin.\n";
	
				if($pago_adelantado == "" && $operacion =="crear")
					$msg_error = $msg_error + "+ Valor del Monto Adelantado NO puede ser vacío.\n";
				
				if($pago_adelantado > 0)
					if($id_caja == 0 && $operacion =="crear")
						$msg_error = $msg_error + "+ No ha seleccionado Caja\n";
						
				
				if($id_caja == 0 && $operacion =="ingresar_pago_cerrar")
					$msg_error = $msg_error + "+ No ha seleccionado Caja\n";
				
				if($pago == 0 && $operacion == "ingresar_pago_cerrar")
					$msg_error = $msg_error + "+ Valor del Monto Ingresado NO puede ser vacío\n";
					
				<?php
				if(!$permiso_registrar_transaccion_sin_turno->isOK)
				{?>
					if($pago_adelantado > 0)
						if($id_turno_atencion == 0 && $operacion =="crear")
							$msg_error = $msg_error + "+ No ha seleccionado ningún Turno.\n";
					
					if($id_turno_atencion == 0 && $operacion =="ingresar_pago")
						$msg_error = $msg_error + "+ No ha seleccionado ningún Turno.\n";
										
				<?php
				}
				?>	
				
				if($msg_error != "")
				{
					$msg_error = "Se han encontrado los siguientes errores:\n\n" + $msg_error;
					alert($msg_error);
				}
				else
				{
					$("#fecha_hora_inicio").val($hora_inicio);
					$("#fecha_hora_fin").val($hora_fin);
					$("#pago_adelantado_mn").val($pago_adelantado);
					$("#id_caja").val($id_caja);
					$("#id_turno_atencion").val($id_turno_atencion);
					$("#comentarios").val($comentarios);
					$("#pago_mn").val($pago);
					/*if($id_reserva > 0)
						$("#operacion").val("modificar");
					else
						$("#operacion").val("crear");*/
					$("#reserva").submit();
				}

			});
			
			$("#aux_id_caja").change(function()
			{
				$id_caja = $("#aux_id_caja").val();
				$id_usuario = $("#id_usuario").val();
				
				$id_turno_atencion = $("#aux_id_turno_atencion");
				
				$id_turno_atencion.empty();
				
				if($id_caja > 0)
				{
					$url = "<?php echo $enlace_query_turno;?>" + "&id_usuario=" + $id_usuario + "&id_caja=" + 	$id_caja;
					
					$.getJSON($url, function(data)
					{
						if(data != null)
						{
							$opcion = "<option value=\"0\">Seleccione...</option>";
							$id_turno_atencion.append($opcion);
							
							$.each(data, function(key, val) 
							{
								$fecha_inicio = FechaFormato(val.fecha_hora_inicio, "d-m-y h:m");
								
								$opcion = "<option value=\"" + val.id + "\">" + val.usuario + ": " + val.codigo + " [" + $fecha_inicio + "]</option>";
								$id_turno_atencion.append($opcion);
							});
								
						}
						else
						{
							$opcion = "<option value=\"0\">Sin turnos activos...</option>";
							$id_turno_atencion.append($opcion);
						}
					});
					
				}
				
			});
			
			$("#aux_opcion_reserva").change(function()
			{
				$opcion_reserva = $(this).val();
				$("#operacion").val("");
				
				if($opcion_reserva == 1)
				{
					link = "<?php echo "../redirect.php?opcion_key=$opcion_ver_detalle_reserva_cancha&usr_key=$usr_key&id_centro=$id_centro&reserva_key=";?>" + $reserva_key;
					window.open(link, "Reserva[" + $reserva_key + "]");
					$("#aux_opcion_reserva").val(0);
				}
				
				if($opcion_reserva == 2)  // Modificar Reserva
				{
					$id_reserva = $("#id_reserva").val();
					$hora_inicio_numero = $("#hora_inicio_numero").val();
					$reserva_fecha = $("#fecha").val();
					$reserva_key = $("#reserva_key").val();
					
					$("#aux_hora_inicio").empty();
					
					$("#btn_seleccionar_cliente").attr("disabled", "disabled");
					$("#aux_comentarios").attr("readonly", "readonly");
					$("#aux_hora_fin").empty();
					$("#aux_hora_fin").append("<option value=''>Seleccione...</option>");
					$("#btn_guardar").attr("disabled", "disabled");		
					
					$("#btn_seleccionar_cliente").removeAttr("disabled");
					$("#aux_comentarios").removeAttr("readonly");
					$("#btn_guardar").removeAttr("disabled");
					$("#operacion").val("modificar");
					
					$("#aux_hora_inicio").append("<option value=''>Seleccione...</option>");
					for(var i = 0; i < 48; i ++)
					{
						if(i % 2 == 0)
							min = "00";
						else
							min = "30";
	
						if(i >= 24)
							ampm = "PM";
						else
							ampm = "AM";
	
						hora_valor = Math.floor(i / 2);
						hora_valor = hora_valor + "" + min;
	
						$("#aux_hora_inicio").append("<option value=\"" + $reserva_fecha + " " + TransformarHora(hora_valor) + "\" >" + TransformarHoraAMPM(hora_valor) + "</option>");
					}
					
					//Agregando último horario
					
					hora_valor = "2359";
					$("#aux_hora_inicio").append("<option value=\"" + $reserva_fecha + " " + TransformarHora(hora_valor) + "\" >" + TransformarHoraAMPM(hora_valor) + "</option>");
				}
				
				if($opcion_reserva == 3)
				{
					$(".opcion_pago").css("display", "block");
					$(".aux_pago").val("0.00");
					$("#operacion").val("ingresar_pago_cerrar");
					$("#btn_guardar").removeAttr("disabled");
				}
				if($opcion_reserva == 4)
				{
					if(confirm("¿Seguro que Desea Cerrar la Reserva sin Ingresar Pago?"))
					{
						$("#operacion").val("cerrar");
						$("#reserva").submit();	
					}
					else					
						$("#aux_opcion_reserva").val(0);
				}
				if($opcion_reserva == 5)  // cancelar Reserva
				{
					
					if(confirm("¿Seguro que Desea Cancelar la Reserva?"))
					{
						$("#operacion").val("cancelar");
						$("#reserva").submit();	
					}
					else					
						$("#aux_opcion_reserva").val(0);

				}
				if($opcion_reserva == 6)
				{
					if(confirm("¿Seguro que Desea Registrar que el Cliente NO se presentó?"))
					{
						$("#operacion").val("no_completada");
						$("#reserva").submit();	
					}
					else					
						$("#aux_opcion_reserva").val(0);
				}
			});
			
			$("#aux_hora_inicio").change(function()
			{
				$hora_inicio = $('#aux_hora_inicio');
				var hora_inicio = $('#aux_hora_inicio').val();
				hora_inicio = hora_inicio.substring(11, 19);	
				
				hora_inicio = TransformarHoraANro(hora_inicio);
				$hora_fin = $('#aux_hora_fin');
				$reserva_fecha = $("#fecha").val();
				
				$hora_fin.empty();			    	
				$hora_fin.append("<option value=''>Seleccione...</option>");
				var hora_valor;
					
				for(var i = 0; i < 48; i ++)
				{
					if(i % 2 == 0)
						min = "00";
					else
				       	min = "30";
					if(i >= 24)
						ampm = "PM";
					else
						ampm = "AM";
					hora_valor = Math.floor(i / 2);
					hora_valor = hora_valor + "" + min; 
					        					        		
					if(parseInt(hora_valor) > parseInt(hora_inicio))
						$hora_fin.append('<option value="' + $reserva_fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');							
				}
					
				hora_valor = "2359";
				if(parseInt(hora_valor) > parseInt(hora_inicio))
					$hora_fin.append('<option value="' + $reserva_fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
			});
			
			$(document).live("ajaxStop", function (e) 
			{
	      		$("#div_reserva").dialog("option", "position", "center");
	      		//Dialogo Buscar Cliente
	      		$("#cliente_div_main").dialog("option", "position", "center");												
			});
			
			$("#btn_agregar_cliente").click(function()
			{
				
				$('#div_cliente_nuevo').dialog('open');
			});

		});

		</script>
	</head>
	<body>
	<?php
		include ("../header.php");
	?>
	<div id="div_main">
		<div id = "main" align="center" >
			<form id="reserva" name="reserva" action="<?php echo $enlace_procesar; ?>" method="POST">
				<input type="hidden" id="fecha_hora_inicio" name="fecha_hora_inicio"/>
				<input type="hidden" id="fecha_hora_fin" name="fecha_hora_fin"/>
				<input type="hidden" id="fecha" name="fecha"/>
				<input type="hidden" id="pago_adelantado_mn" name="pago_adelantado_mn"/>
				<input type="hidden" id="pago_mn" name="pago_mn"/>
				<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>"/>
				<input type="hidden" id="id_centro" name="id_centro" value="<?php echo $id_centro; ?>"/>
				<input type="hidden" id="id_cliente" name="id_cliente" />
				<input type="hidden" id="id_reserva" name="id_reserva" />
				<input type="hidden" id="reserva_key" name="reserva_key" />
				<input type="hidden" id="id_caja" name="id_caja" />
				<input type="hidden" id="id_turno_atencion" name="id_turno_atencion" />
				<input type="hidden" id="operacion" name="operacion" />
				<input type="hidden" id="comentarios" name="comentarios" />
				<input type="hidden" id="hora_inicio_numero" name="hora_inicio_numero" />
				<input id="fecha_mostrar_db" name="fecha_mostrar_db" type="hidden">
				
				<div id="div_reservas_titulo" align="center"">
					<span id="reservas_titulo"><?php echo "RESERVAS DE CANCHA ENTRE: $dia_1_str y $dia_7_str"; ?></span>
					<div id="ir_a_fecha">
						<span ><u>Ir a fecha:</u></span>
						<input id="fecha_mostrar" type="text" readonly="readonly"/> 
					</div>
				</div>
				<div id="div_reservas_semana" >
					<div id="div_tabla_reservas">
						
						<table id="tabla_reservas">
							<thead>
								<tr>
									<th width=90px></th>
									<?php
									foreach($dias as $d)
									{
										$nro_dia= date("w", strtotime( date('Y-m-d', strtotime($d)) ));
										$fecha_str = date("d/m/Y", strtotime( date('Y-m-d', strtotime($d)) ));
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
										
										?>
										<th align="center">
											<div class="div_fecha_titulo">
											<div align="center" style="height:16px"><span class="span_fecha_titulo_nombre_dia"><?php echo $nombre_dia; ?></span></div>
												<div align="center" class="div_label"><span class="span_fecha_titulo_fecha"><?php echo $fecha_str; ?></span></div>
											</div>
										</th>
									<?php
									}
								?>
								</tr>
								
							</thead>
							<tfoot></tfoot>
							<?php
							$minuto = 0;
							$hora = $hora_inicial;
							$class_hora = "hora_0";
							$class_div_horario = "div_horario_0";
							$class_td_horario = "td_horario_0";
							for($i = $hora * 2; $i < 48; $i++)
							{
								$hora_str = date("h:i A.", mktime($hora, $minuto, 0, 1,1, 2000));
								?>
								<tr>
									<td align="center" class="<?php echo $class_td_horario; ?>">
										<div class="<?php echo $class_div_horario; ?>" align="center">
											<span class="<?php echo $class_hora; ?>">
												<?php echo $hora_str; ?>
											</span>
										</div>
									</td>
									<?php
									foreach($dias as $d)
									{
										$nro_dia = date("w", strtotime( date('Y-m-d', strtotime($d)) ));
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
											
										$fecha_hora = date("Y-m-d", strtotime( date('Y-m-d', strtotime($d)) ))." ".date("H:i:s", mktime($hora, $minuto, 0, 1,1, 2000));
										$fecha = date("Y-m-d", strtotime( date('Y-m-d', strtotime($d)) ));
										$fecha_str = date("d-m-Y", strtotime( date('Y-m-d', strtotime($d)) ));
										$fecha_hora_m30 = date("Y-m-d H:i:s", strtotime('+30 minutes',strtotime( date('Y-m-d H:i:s', strtotime($fecha_hora)) )));
										$fecha_hora_str = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($fecha_hora))));
										
										$hora_inicio_numero = date("Hi", mktime($hora, $minuto, 0, 1,1, 2000));
										$hora_inicio = date("H:i:s", mktime($hora, $minuto, 0, 1,1, 2000));
										$hora_inicio_str = date("h:i A.", mktime($hora, $minuto, 0, 1,1, 2000)); 
										
										$lista_reservas_activas = $resBLO->ListarReservaActivaXFechaIniyFechaFin($id_centro, $fecha_hora, $fecha_hora_m30);
										
										/*if(!is_null($lista_reservas_activas))
										{*/
											if(!is_null($lista_reservas_activas) && count($lista_reservas_activas) > 0)
											{
												$res = $lista_reservas_activas[0];
											
												$title = "Fecha: $nombre_dia $fecha_str - Hora: $hora_str";
												
												$mostrar_cliente = FALSE;
												
												if($res->fecha_hora_inicio == $fecha_hora)
												{
													$mostrar_cliente = TRUE;
													
													$hora_inicio = strtotime($res->fecha_hora_inicio);
													$hora_fin = strtotime($res->fecha_hora_fin);
													$reserva_minutos = round(abs($hora_fin - $hora_inicio) / 60,2);
													$row_span = round($reserva_minutos / 30);
														
													$reserva_hora_inicio = date("H:i:s", strtotime( date('Y-m-d H:i:s', strtotime($res->fecha_hora_inicio)) ));
													$reserva_hora_inicio_str = date("h:i A.", strtotime( date('Y-m-d H:i:s', strtotime($res->fecha_hora_inicio)) ));
													$reserva_hora_fin = date("H:i:s", strtotime( date('Y-m-d H:i:s', strtotime($res->fecha_hora_fin)) ));
													$reserva_hora_fin_str = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($res->fecha_hora_fin)) ));
													
													$fecha_hora_registro = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($res->fecha_hora_registro)) ));
													
													$pago_adelantado = number_format($res->pago_adelantado, 2);
													$title = "Fecha: $nombre_dia $fecha_str [Hora Inicio: $reserva_hora_inicio_str - Hora Fin: $reserva_hora_fin_str] - A Cta.: S/.".$pago_adelantado;
													
													
													$hora_inicio = $reserva_hora_inicio;
													$id_reserva = $res->id;
													$id_cliente = $res->id_cliente;
													$cliente_nombres_apellidos = $res->cliente_nombres_apellidos;
													$pago_adelantado = number_format($res->pago_adelantado, 2);
													$lbl_reserva_cliente = "$res->cliente_nombres_apellidos</br>[A Cta. S/.$pago_adelantado]";
													
													$colorweb = $res->colorweb;
													$td_class = "td_fecha_hora_reserva";
													$comentarios = strtoupper($res->comentarios);
													
													$row_span = "rowspan = $row_span";
													
													?>
													
													<td class="td_fecha_hora_reserva celda_horario" title="<?php echo $title; ?>" <?php echo $row_span; ?> >
														<input class="hora_inicio_numero" type="hidden" value="<?php echo $hora_inicio_numero; ?>"/>
														<input class="hora_inicio_str" type="hidden" value="<?php echo $hora_inicio_str; ?>"/>
														<input class="hora_inicio" type="hidden" value="<?php echo $hora_inicio; ?>"/>													
														<input class="hora_fin_str" type="hidden" value="<?php echo $reserva_hora_fin_str; ?>"/>
														<input class="hora_fin" type="hidden" value="<?php echo $reserva_hora_fin; ?>"/>
														<input class="fecha_str" type="hidden" value="<?php echo $fecha_str; ?>"/>
														<input class="fecha" type="hidden" value="<?php echo $fecha; ?>"/>
														<input class="id_reserva" type="hidden" value="<?php echo $res->id;?>" />
														<input class="reserva_key" type="hidden" value="<?php echo $res->auto_key;?>" />
														<input class="id_cliente" type="hidden" value="<?php echo $res->id_cliente;?>" />
														<input class="nombre_cliente" type="hidden" value="<?php echo $res->cliente_nombres_apellidos;?>" />
														<input class="pago_adelantado" type="hidden" value="<?php echo number_format($res->pago_adelantado, 2);?>" />
														<input class="comentarios" type="hidden" value="<?php echo $res->comentarios;?>" />
														<input class="fecha_hora_registro" type="hidden" value="<?php echo $fecha_hora_registro;?>" />
														<input class="usuario_creador" type="hidden" value="<?php echo $res->usuario_creacion;?>" />
														<input class="estado" type="hidden" value="<?php echo $res->estado;?>" />
														
														<div class="div_cliente" align="center" style="background-color: <?php echo $res->colorweb; ?>">
															<div class="div_cliente_nombre">
																<?php echo "$res->cliente_nombres_apellidos</br>[A Cta. S/.$pago_adelantado]"; ?>	 
															</div>
														</div>
													</td>
														
												<?php
												}

											}
											else
											{
												$td_class = "td_fecha_hora_reserva_libre";
												
												$mostrar_cliente = FALSE;
												$title = "Fecha: $nombre_dia $fecha_str - Hora: $hora_str";
												$reserva_hora_fin_str = "";
												$reserva_hora_fin = "";
												
												
											?>
												<td class="td_fecha_hora_reserva_libre celda_horario" title="Fecha: <?php echo $title; ?>">
													<input class="hora_inicio_numero" type="hidden" value="<?php echo $hora_inicio_numero; ?>"/>
													<input class="hora_inicio_str" type="hidden" value="<?php echo $hora_inicio_str; ?>"/>
													<input class="hora_inicio" type="hidden" value="<?php echo $hora_inicio; ?>"/>
													<input class="hora_fin_str" type="hidden" value=""/>
													<input class="hora_fin" type="hidden" value=""/>													
													<input class="fecha_str" type="hidden" value="<?php echo $fecha_str; ?>"/>
													<input class="fecha" type="hidden" value="<?php echo $fecha; ?>"/>
													<input class="id_reserva" type="hidden" value="0" />
													<input class="reserva_key" type="hidden" value="" />
													<input class="id_cliente" type="hidden" value="0" />
													<input class="nombre_cliente" type="hidden" value="" />
													<input class="pago_adelantado" type="hidden" value="0.00" />
													<input class="comentarios" type="hidden" value="" />
													<input class="fecha_hora_registro" type="hidden" value="" />
													<input class="usuario_creador" type="hidden" value="" />
													<input class="estado" type="hidden" value="0" />
												</td>
											
											<?php
											}	
																				
										

									}
									if($i % 2 == 1)
									{
										$hora ++;
										$minuto = 0;
										$class_hora = "hora_0";
										$class_div_horario = "div_horario_0";
										$class_td_horario = "td_horario_0";
									}
									else
									{
										$minuto = 30;
										$class_hora = "hora_30";
										$class_div_horario = "div_horario_30";
										$class_td_horario = "td_horario_30";
									}
									?>
								</tr>
							<?php
								}
							?>	
							</tbody>
						</table>
					</div>
					
				</div>	
																	
			</form>
		</div>	
		<div id="div_leyenda_estados">
		<?php
		$lista_estados = $resBLO->ListarEstadoTodos();
		if(!is_null($lista_estados))
		{
			foreach($lista_estados as $e)
			{?>
				<div class="div_reserva_estado">
					<div class="etiqueta_leyenda"><span><?php echo $e -> descripcion; ?></span></div>
					<div class="leyenda_estado" style="background-color: <?php echo $e -> colorweb; ?>"></div>
				</div>
			<?php
			}
		}
		?>
		</div>
				
	</div>
	<div id="div_reserva">
		
		<table style="color:#585858; margin-left: 10px;">
			<tr>
				<td colspan="3">
					<div style="float: left; width: 300px;">
						<img src="<?php echo $global_images_folder; ?>logo-delocal.png" style="width:63px; height:72px;"/></br>
							<!--span style="font-size:11px; font-weight: bold">Datos de la Reserva:</span-->	
					</div>
				</td>					
			</tr>
			<tr height="20px"></tr>
			<tr>
				<td width="100px" colspan="2">
					<span class="etiqueta">Fecha:</span>
				</td>
				<td>
					<input id="reserva_fecha" class="texto_2" readonly=readonly/>
				</td>
			</tr>
			<tr>
				<td width="130px" colspan="2">
						<span class="etiqueta">Hora Inicio:</span>
				</td>
				<td>
					<select id="aux_hora_inicio" value="" class="texto_2"/>
				</td>
			</tr>
			<tr>
				<td width="130px" colspan="2">
					<span class="etiqueta">Hora Fin:</span>
				</td>
				<td>
					<select id="aux_hora_fin" class="texto_2">	</select>									
				</td>
			</tr>
			<tr>
				<td width="100px" colspan="2">
					<span class="etiqueta">Cliente:</span>
				</td>
				<td>
					<table>
						<tr>
							<td><input name="nombre_cliente" id = "nombre_cliente" value=""/></td>
							<td><input type="button" id="btn_agregar_cliente" value="+" title="Crear Nuevo Cliente?"/></td>
					</tr>
					</table>
				</td>
			</tr>
			
			<!--tr>
				<td width="100px" colspan="2">
					<div class="opciones_reserva_nueva opcion_pago">
						<span class="etiqueta">Caja:</span>
					</div>
					
				</td>
				<td>
					<div class="opciones_reserva_nueva opcion_pago">
						<select id="aux_id_caja" class="texto_4">
							<option value="0">Seleccione...</option>
							<?php
	
							$lista_cajas = $caBLO -> ListarCajaHabilitadaIngresoXIdUsuario($id_usuario, $id_centro);
							foreach ($lista_cajas as $c)
								echo "<option value=\"$c->id_caja\">" . strtoupper($c -> caja) . "</option>";
							?>
						</select>
					</div>
				</td>
			</tr-->
			<!--tr>
				<td width="100px" colspan="2">
					<div class="opciones_reserva_nueva opcion_pago">
						<span class="etiqueta">Turno:</span>
					</div
				</td>
				<td>
					<div class="opciones_reserva_nueva opcion_pago">
						<select id="aux_id_turno_atencion" class="texto_4_5">
							<option value="0">Seleccione...</option>		
						
						</select>
					</div>
				</td>
			</tr-->
			
			<tr>
				<td width="100px">
					<span class="etiqueta">A cuenta:</span>									
				</td>
				<td align="right">
					<span class="etiqueta">S/.</span>	
				</td>								
				<td>
					<table>
						<tr>
						<td><input id="aux_pago_adelantado" type="number" onkeypress="validate()" class="texto_1" value="0.00"/></td>
						<td width="41px">
							</td>
							<td>
								<div class="opcion_pago">
									<span class="etiqueta">Pago :</span>
								</div>
							</td>
							<td align="right" width="10px">
								<div class="opcion_pago">
									<span class="etiqueta">S/.</span></td>
								</div>
							<td>
								<div class="opcion_pago">
									<input id="aux_pago" type="number" onkeypress="validate()" class="texto_1" value="0.00"/>
								</div>
							</td>	
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="100px" colspan="2">
					<span class="etiqueta">Comentarios:</span>									
				</td>
				<td>
					<textarea id="aux_comentarios" cols="35" rows="3" style="" class="clase12"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="opciones_reserva" style="display: none;">
						<span class="etiqueta">Creado por:</span>
					</div>
				</td>
				<td>
					<div class="opciones_reserva" style="display: none;">
						<input id="aux_usuario_creador" readonly="readonly" value="" class="texto_2"/>
					</div>
				</td>	
				</td>								
			</tr>
			<tr>
				<td colspan="2">
					<div class="opciones_reserva" style="display: none;">
						<span class="etiqueta">Fecha Creación:</span>
					</div>
				</td>
				<td>
					<div class="opciones_reserva" id="div_fecha_creacion" style="display: none;">
						<input id="aux_fecha_hora_registro" readonly="readonly" value="" class="texto_3_5"/>
					</div> 
				</td>								
			</tr>
			<tr>
				<td colspan="2">
					<div class="opciones_reserva" style="display: none;">
						<span class="etiqueta">Opciones:</span>
					</div>
				</td>
				<td>
					<div class="opciones_reserva" style="display: none;">
						<select id="aux_opcion_reserva" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							if($permiso_ver_detalle_reserva_cancha->isOK)
								echo "<option value=\"1\">Ver Detalle</option>";
							if($permiso_modificar_reserva_cancha->isOK)
								echo "<option value=\"2\">Modificar Reserva</option>";
							echo "<option value=\"3\">Ingresar Pago y Cerrar</option>";
							echo "<option value=\"4\">Cerrar Reserva</option>";
							if($permiso_cancelar_reserva_cancha->isOK)	
								echo "<option value=\"5\">Cancelar Reserva</option>";
							echo "<option value=\"6\">Cerrar (Cliente NO Llegó)</option>";
							?>
							
						</select>
					</div>	
				</td>								
			</tr>
			
			<tr>
				<td width="100px" align="left" colspan="2">
					<input type="button" class="boton_operacion" id="btn_guardar" value="Guardar" />									
				</td>
				<td align="right">
					<input type="button" class="boton_operacion" id="btn_cerrar" value="Cerrar" />									
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<span class="boton_operacion" style="font-size: 11px; color: red;" id="msg_error"></span>
				</td>
			</tr>
		</table>	
	</div>
	<?php		
		include ('../clientes/crear_simple.php');
	?>
	</body>
</html>
	
