<?php

session_start();

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

$hora_inicial = 0;
$opcion_query_cliente = "24QZZ19Q";
$opcion_ver_detalle_reserva_cancha = "D01BO62A";
$opcion_crear_reserva_cancha = "EXT53D14";
$opcion_modificar_reserva_cancha = "EO5JZ294";
$opcion_cancelar_reserva_cancha = "YY9M6M15";

include ('../clases/enc_dec.php');
include ('../clases/usuario.php');
include ('../clases/opcion.php');
include ('../clases/general.php');
include ('../clases/security.php');
include ('../clases/reserva_cancha.php');
include ('../clases/centro.php');
include ("../clases/anuncio.php");

$id_usuario = $usuario->id;
	
$cenBLO = new CentroBLO();
$opcBLO = new OpcionBLO();
$resBLO = new ReservaCanchaBLO();


$permiso_ver_detalle_reserva_cancha = $opcBLO->ValidarOpcionXIdUsuario($opcion_ver_detalle_reserva_cancha, $usuario->id, $id_centro);

$permiso_crear_reserva_cancha = $opcBLO->ValidarOpcionXIdUsuario($opcion_crear_reserva_cancha, $usuario->id, $id_centro);
$permiso_modificar_reserva_cancha = $opcBLO->ValidarOpcionXIdUsuario($opcion_modificar_reserva_cancha, $usuario->id, $id_centro);

$permiso_cancelar_reserva_cancha = $opcBLO->ValidarOpcionXIdUsuario($opcion_cancelar_reserva_cancha, $usuario->id, $id_centro);



function GetDays($sStartDate, $sEndDate)
{
 
  $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
  $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));
  $aDays[] = $sStartDate;
  $sCurrentDate = $sStartDate;
  
  while($sCurrentDate < $sEndDate)
  {
    $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
	$aDays[] = $sCurrentDate;
  }

  return $aDays;
}

if(isset($_GET["fecha"]))
	$fecha = $_GET["fecha"];
else 
	$fecha = date("Y-m-d");

$fecha_mostrar = date("d/m/Y", strtotime( date('Y-m-d', strtotime($fecha)) ));
	
//************ ASIGNACION DE FECHA DE INICIO Y FECHA FIN******************
$dia_semana = date("w", strtotime( date('Y-m-d', strtotime($fecha)) ));
$custom_date = strtotime( date('Y-m-d', strtotime($fecha)) );

if($dia_semana == 0)
{
	 $dia_1 = date('Y-m-d', strtotime('last week monday', $custom_date));
	 $dia_7 = date("Y-m-d", strtotime( date('Y-m-d', strtotime($fecha)) ));
	
}
else
{
	if($dia_semana == 1)
		$dia_1 = date('Y-m-d', strtotime('this week monday', $custom_date));
	else
		$dia_1 = date('Y-m-d', strtotime(' last monday', $custom_date));
	$dia_7 = date('Y-m-d', strtotime('next sunday', $custom_date));
}

$dia_1_str = date("d/m/Y", strtotime( date('Y-m-d', strtotime($dia_1)) )) ;
$dia_7_str = date("d/m/Y", strtotime( date('Y-m-d', strtotime($dia_7)) ));

//************************************************************************

$dias = GetDays($dia_1, $dia_7);



$enlace_procesar = "../procesar_reserva.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>RODO </title>
		<meta name="author" content="Jesus Rodriguez" />
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<!-- Date: 2011-11-28 -->
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		
		<style media="screen" type="text/css">
			.box { width: 140px; color: #FFFFFF; font-family:Helvetica; font-size:11px; font-weight:bold; }
			body { background-color: #F1F1F1;}
			#main { padding-top: 20px; font-family: Helvetica;  }
			
			#div_reservas_semana { border: dotted 1px #3399FF;  background-color: #FFFFFF; color: #585858; margin-bottom: 20px; width: 1255px; border-radius: 10px 10px 10px 10px; }
			
			#reservas_titulo { font-family: Helvetica; font-size: 18px; font-weight: bold; color: #0099CC; }
			
			#div_tabla_reservas { border: solid 1px #3399FF; margin-left: 7px; border-radius: 8px 8px 8px 8px; margin-right: 10px; margin-bottom: 10px;
				margin-top: 3px; }
			#tabla_reservas { border-collapse: collapse; width: 1230px; }
			.td_horario_0 { border-right: solid 1px #3399FF; border-top: solid 1px #3399FF; border-left: none; }
			.td_horario_30 { border-right: solid 1px #3399FF; border-top: dotted 1px #3399FF; }
			
			.div_fecha_titulo { float: left; width: 159px; background-color: #333333; color: #FFFFFF; border-radius: 8px 8px 8px 8px; padding-top: 5px; padding-bottom: 5px; }
			.span_fecha_titulo_nombre_dia { font-family: Helvetica; font-size: 14px; font-weight:bold;  }
			.span_fecha_titulo_fecha { font-family: Helvetica; font-size: 11px;  }
			
			.div_horario_0 { border-radius: 5px 5px 5px 5px; height: 15px; width: 80px; background-color:#0099CC; }
			.div_horario_30 { border-radius: 5px 5px 5px 5px; height: 15px; width: 60px; border: solid 1px #0099CC; }
			.hora_0 { font-size: 12px; font-weight: bold;}
			.hora_30 { font-size: 11px; margin-top: 4px; }
			
			.td_fecha_hora_reserva { border: dotted 1px #3399FF; width: 160px;}
			.td_fecha_hora_reserva:hover { cursor: pointer; background-color: #F1F1F1; }
			
			.td_fecha_hora_reserva_libre { border: dotted 1px #3399FF; }
			.td_fecha_hora_reserva_libre:hover { cursor: pointer; background-color: rgba(0, 153, 204, 0.3); }
			
			.div_cliente { width:158px; height: auto;   border-radius: 5px 5px 5px 5px; float: left; /*background-color: #99CC00;*/ }
			.div_cliente:hover { background-color: #585858 !importart; color: #FFFFFF; font-weight: bold; }
			.div_cliente_nombre { }
			
			#div_extra{ margin-top:650px; display: none;  }
			
			#div_main { }
			
			#fecha_creacion { font-family: Helvetica; font-size:11px; width: 120px; text-align: center; }
			
			#comentarios { font-size:11px; resize: none; font-family: Helvetica; }
			
			#ir_a_fecha { font-family: Helvetica; font-size: 12px; float: right; font-weight: bold; margin-right: 10px; }
			#ir_a_fecha:hover { cursor: pointer; }
			
			#fecha_mostrar { font-family: Helvetica; font-size: 11px;  width: 60px; }
			
			#div_leyenda_estados { border: dotted 1px #0099CC; margin-top: 10px; padding: 10px 10px 10px 10px; margin-bottom: 10px; width: 830px; height: 18px;
				border-radius: 10px 10px 10px 10px; background-color: #FFFFFF;  }
			.div_reserva_estado { float: left; margin-left: 10px; border: dotted 1px #0099CC; border-radius: 5px 5px 5px 5px; background-color: #FFFFFF; padding-left: 5px; }
			.leyenda_estado { width: 40px; height: 15px; float: left; margin-left: 10px; border-radius: 5px 5px 5px 5px;}
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
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
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
			
		$(function() {
			/*$("#fecha_mostrar").datepicker();
		    $("#fecha_mostrar").datepicker("option", "dateFormat","dd-mm-yy");*/
		   
		    
		    $('#fecha_mostrar').datepicker({dateFormat: 'dd/mm/yy', altField: '#fecha_mostrar_db', altFormat: 'yy-mm-dd'});
		    $("#fecha_mostrar").datepicker("setDate","<?php echo $fecha_mostrar;?>");		    
			
	   		var $main = $('#div_main'),
	    		$extra = $('#div_extra'),	    		
	        	$overlay = $('<div>', {
		            css: {
		                position: 'absolute',
		                //width: '1000px',//$main.outerWidth(),
		                width:$main.width(),
		                height: $main.height(),
		                top: $main.position().top,
		                //'margin-top': '-200px',
		                //left: '50%',
		                backgroundColor: 'rgba(192,192,192,0.5)',
		                zIndex: 10,
		                display: 'none'
						//'margin-left': '-500px'
						     
		            }		            
	    	});
	    	$overlay.append($extra);
	    	$overlay.appendTo($main);
	    	
	    	$("#fecha_mostrar").change(function()
	    	{
	    		$fecha = $("#fecha_mostrar_db").val();
				location.href = "reservar.php?<?php echo "usr_key=$usr_key&id_centro=$id_centro&opcion_key=$opcion_key";?>&fecha="+$fecha;
	    	})
		    
			$(".td_fecha_hora_reserva").each(function()
			{
				$alto_td = $(this).height();
				$(this).css("vertical-align","middle");
				
				$(this).find(".div_cliente").each(function()
				{
					$(this).height($alto_td);
					
					$pad = 0;
					
					$(this).find(".div_cliente_nombre").each(function()
					{
						$alto_2 = $(this).height();
						
						$pad = ($alto_td - $alto_2) / 2;
						$pad = $pad + "px";
						
						$(this).css("margin-top", $pad);
					});
					
					/*$alto_2 = $alto_td / 2;
					$alto_2 = $alto_2 * -1;
					$alto_2 = $alto_2 + "px";
					
					$(this).css("position","relative");
					$(this).css("top","50%");
					$(this).css("margin-top",$alto_2);*/
				})
				
			});
			
			$("#opcion_reserva").change(function()
			{
				$opcion_reserva = $(this).val();
				if($opcion_reserva > 0)
				{
					if($('#opcion_reserva').val() == 1)
			    	{
			    		$('#operacion').val("modificar");
			    		$hora_inicio = $('#hora_inicio');
				    	$hora_fin = $('#hora_fin');
				    	$fecha_db = $("#fecha_db").val();
				    	$hora_inicio.empty();			    	
				    	$hora_fin.empty();
				    	
				    	$hora_inicio.removeAttr('disabled');			    	
				    	$hora_fin.removeAttr('disabled');
						$hora_inicio.append("<option value=''>Seleccione...</option>");
						$hora_fin.append("<option value=''>Seleccione...</option>");
						var hora_valor;
						for(var i = 0; i <= 48; i ++)
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
					        				        		
					        $hora_inicio.append('<option value="' + $fecha_db + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
					        $('#seleccionar_cliente').removeAttr('disabled');					        
							$('.opciones_reserva').css('display','none');							
							$('.opciones_reserva').empty();
							$('#pago_adelantado').removeAttr('readonly');
							$('#comentarios').removeAttr('readonly');
							$("#guardar_reserva").removeAttr("disabled");
						}						
			    	}			    	
			    	if($('#opcion_reserva').val() == 5)
			    	{	
			    		$('#operacion').val("cancelar");
			    		$('#guardar_reserva').removeAttr('disabled');
			    		
			    	}
					
				}
			});
			
			$('#hora_inicio').change(function()
			{
				$hora_inicio = $('#hora_inicio');
				var hora_inicio = $('#hora_inicio').val();
				hora_inicio = hora_inicio.substring(11, 19);	
				
				hora_inicio = TransformarHoraANro(hora_inicio);
				$hora_fin = $('#hora_fin');
				$fecha_db = $("#fecha_db").val();
				$('#msg_error').empty();
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
						$hora_fin.append('<option value="' + $fecha_db + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');							
				}
					
				hora_valor = "2359";
				if(parseInt(hora_valor) > parseInt(hora_inicio))
					$hora_fin.append('<option value="' + $fecha_db + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
			});
			
			$(".td_fecha_hora_reserva").live("click", function()
			{
				var $this = $(this);
				
				$p_crear_rc = $("#p_crear_rc").val();
				$p_ver_rc = $("#p_ver_rc").val();
				$p_modificar_rc = $("#p_modificar_rc").val();
				$p_cancelar_rc = $("#p_cancelar_rc").val();
				/*
				alert("Crear: " + $p_crear_rc);
				alert("Ver: " + $p_ver_rc);
				alert("Modificar: " + $p_modificar_rc);
				alert("Cancelar: " + $p_cancelar_rc);
				*/
				        //$this.css( 'z-index', 11 );				
				$fecha_hora_ini = $this.find("#fecha_hora_ini").val();
				$fecha_hora_ini_str = $this.find("#fecha_hora_ini_str").val();
				
				$id_reserva = $this.find("#res_id_reserva").val();
				
				$fecha = $fecha_hora_ini.substring(0, 10);
				$hora = $fecha_hora_ini.substring(11,19);
				
				$("#fecha").val($fecha_hora_ini_str.substring(0,10));
				$("#fecha_db").val($fecha);
				
				$hora_inicio = $("#hora_inicio");
				$hora_inicio.empty();
				$hora_inicio.append("<option value='" + $fecha_hora_ini + "'>" + $fecha_hora_ini_str.substring(11, 20) + "</option>");
				
				$hora_inicio_nro = TransformarHoraANro($hora);				
				
				$hora_fin = $("#hora_fin");
				$hora_fin.empty();
				
				$("#seleccionar_cliente").attr("disabled","disabled");
				$("#comentarios").attr("readonly","readonly");
				$("#pago_adelantado").attr("readonly","readonly");
				$(".opciones_reserva").css("display","none");
				$("#guardar_reserva").attr("disabled","disabled");
				
				$("#id_reserva").val($id_reserva);
				
				
				if($id_reserva == 0)
				{
					if($p_crear_rc)
					{
						$("#seleccionar_cliente").removeAttr("disabled");
						$("#comentarios").removeAttr("readonly");
						$("#pago_adelantado").removeAttr("readonly");
						$("#comentarios").removeAttr("readonly");
						$("#guardar_reserva").removeAttr("disabled");
						$("#operacion").val("registrar");
						
						$hora_fin.append("<option value=''>Seleccione...</option>");
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
							
							if(parseInt(hora_valor) > parseInt($hora_inicio_nro))
								$hora_fin.append('<option value="' + $fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
						}
						        //Agregando último horario
						hora_valor = "2359";
						if(parseInt(hora_valor) > parseInt($hora_inicio_nro))
							$hora_fin.append('<option value="' + $fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
						
						$overlay.data('current', $this).show();
						$extra.data('current', $this).show();
					}
					
				}
				else
				{
					if($p_ver_rc == 1 || $p_modificar_rc == 1 || $p_cancelar_rc == 1)					
					{
						
						$fecha_hora_fin = $this.find("#fecha_hora_fin").val();
						$fecha_hora_fin_str = $this.find("#fecha_hora_fin_str").val();
						$res_nombre_cliente = $this.find("#res_nombre_cliente").val();
						$res_id_cliente = $this.find("#res_id_cliente").val();
						$res_comentarios = $this.find("#res_comentarios").val();
						$res_pago_adelantado = $this.find("#res_pago_adelantado").val();
						$res_creado_por = $this.find("#res_creado_por").val();
						$res_fecha_creacion = $this.find("#res_fecha_creacion").val();
						
						$nombre_cliente = $("#nombre_cliente");
						$id_cliente = $("#id_cliente");
						$comentarios = $("#comentarios");
						$pago_adelantado = $("#pago_adelantado");
						$fecha_creacion = $("#fecha_creacion");
						$creado_por = $("#creado_por");
						
						$(".opciones_reserva").css("display","block");
						
						$hora_fin.append("<option value='" + $fecha_hora_fin + "'>" + $fecha_hora_fin_str + "</option>");
						$nombre_cliente.val($res_nombre_cliente);
						$id_cliente.val($res_id_cliente);
						$comentarios.val($res_comentarios);
						$creado_por.val($res_creado_por);
						$fecha_creacion.val($res_fecha_creacion);
						$pago_adelantado.val(parseFloat($res_pago_adelantado, 10).toFixed(2));
						
						$overlay.data('current', $this).show();
						$extra.data('current', $this).show();
						
					}
					else
						alert("No cuenta con permisos para ver el detalle de la Reserva!");
					
				}
				
				
			});
			
			$(".td_fecha_hora_reserva_libre").live("click", function()
			{
				var $this = $(this);
				
				$p_crear_rc = $("#p_crear_rc").val();
				$p_ver_rc = $("#p_ver_rc").val();
				$p_modificar_rc = $("#p_modificar_rc").val();
				$p_cancelar_rc = $("#p_cancelar_rc").val();
				/*
				alert("Crear: " + $p_crear_rc);
				alert("Ver: " + $p_ver_rc);
				alert("Modificar: " + $p_modificar_rc);
				alert("Cancelar: " + $p_cancelar_rc);
				*/
				        //$this.css( 'z-index', 11 );				
				$fecha_hora_ini = $this.find("#fecha_hora_ini").val();
				$fecha_hora_ini_str = $this.find("#fecha_hora_ini_str").val();
				
				$id_reserva = $this.find("#res_id_reserva").val();
				
				$fecha = $fecha_hora_ini.substring(0, 10);
				$hora = $fecha_hora_ini.substring(11,19);
				
				$("#fecha").val($fecha_hora_ini_str.substring(0,10));
				$("#fecha_db").val($fecha);
				
				$hora_inicio = $("#hora_inicio");
				$hora_inicio.empty();
				$hora_inicio.append("<option value='" + $fecha_hora_ini + "'>" + $fecha_hora_ini_str.substring(11, 20) + "</option>");
				
				$hora_inicio_nro = TransformarHoraANro($hora);				
				
				$hora_fin = $("#hora_fin");
				$hora_fin.empty();
				
				$("#seleccionar_cliente").attr("disabled","disabled");
				$("#comentarios").attr("readonly","readonly");
				$("#pago_adelantado").attr("readonly","readonly");
				$(".opciones_reserva").css("display","none");
				$("#guardar_reserva").attr("disabled","disabled");
				
				$("#id_reserva").val($id_reserva);
				
				
				if($id_reserva == 0)
				{					
					if($p_crear_rc)
					{
						$("#seleccionar_cliente").removeAttr("disabled");
						$("#comentarios").removeAttr("readonly");
						$("#pago_adelantado").removeAttr("readonly");
						$("#comentarios").removeAttr("readonly");
						$("#guardar_reserva").removeAttr("disabled");
						$("#operacion").val("registrar");
						
						$hora_fin.append("<option value=''>Seleccione...</option>");
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
							
							if(parseInt(hora_valor) > parseInt($hora_inicio_nro))
								$hora_fin.append('<option value="' + $fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
						}
						        //Agregando último horario
						hora_valor = "2359";
						if(parseInt(hora_valor) > parseInt($hora_inicio_nro))
							$hora_fin.append('<option value="' + $fecha + " " + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
						
						$overlay.data('current', $this).show();
						$extra.data('current', $this).show();
					}
					
				}
				else
				{
					if($p_ver_rc == 1 || $p_modificar_rc == 1 || $p_cancelar_rc == 1)					
					{
						
						$fecha_hora_fin = $this.find("#fecha_hora_fin").val();
						$fecha_hora_fin_str = $this.find("#fecha_hora_fin_str").val();
						$res_nombre_cliente = $this.find("#res_nombre_cliente").val();
						$res_id_cliente = $this.find("#res_id_cliente").val();
						$res_comentarios = $this.find("#res_comentarios").val();
						$res_pago_adelantado = $this.find("#res_pago_adelantado").val();
						$res_creado_por = $this.find("#res_creado_por").val();
						$res_fecha_creacion = $this.find("#res_fecha_creacion").val();
						
						$nombre_cliente = $("#nombre_cliente");
						$id_cliente = $("#id_cliente");
						$comentarios = $("#comentarios");
						$pago_adelantado = $("#pago_adelantado");
						$fecha_creacion = $("#fecha_creacion");
						$creado_por = $("#creado_por");
						
						$(".opciones_reserva").css("display","block");
						
						$hora_fin.append("<option value='" + $fecha_hora_fin + "'>" + $fecha_hora_fin_str + "</option>");
						$nombre_cliente.val($res_nombre_cliente);
						$id_cliente.val($res_id_cliente);
						$comentarios.val($res_comentarios);
						$creado_por.val($res_creado_por);
						$fecha_creacion.val($res_fecha_creacion);
						$pago_adelantado.val(parseFloat($res_pago_adelantado, 10).toFixed(2));
						
						$overlay.data('current', $this).show();
						$extra.data('current', $this).show();
						
					}
					else
						alert("No cuenta con permisos para ver el detalle de la Reserva!");
					
				}
				
			});
			
			$('#seleccionar_cliente').click(function()
			{
				mywindow = showModalDialog("../clientes/buscar.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
				
				//var cliente = $.cookie("id_clientex");
				id_cliente = mywindow.id_cliente;
				nombre_cliente = mywindow.nombre_cliente;
				
				$('#nombre_cliente').val(nombre_cliente);								
				$('#id_cliente').val(id_cliente);
				//alert("Clente: " + id_cliente);
				//var url = "../procesar_cliente.php?id=" + cliente + "<?php //echo "&usr_key=$usr_key&id_centro=$id_centro&opcion_key=$opcion_query_cliente";?>"
				    
			   	/*$.getJSON(url, function(data) 
				{
						//var items = [];
					$.each(data, function(key, val) 
					{
						$('#nombre_cliente').val(val.nombres + ' ' + val.apellidos);								
						$('#id_cliente').val(val.id);
					});
				});*/
				
			});
			 
				    
		});
	
		function CerrarVentanaReserva()
		{
			var fecha = "<?php if($fecha == "") echo date('Y-m-d'); else echo $fecha; ?>";
			location.href = "reservar.php?<?php echo "usr_key=$usr_key&id_centro=$id_centro&opcion_key=$opcion_key";?>&fecha="+fecha;
		}
		
		function GuardarReserva()
		{
			resultado = true;
			var id_cliente = document.getElementById("id_cliente").value;
			
			var pago_adelantado = document.getElementById("pago_adelantado").value;
			var hora_inicio = document.getElementById("hora_inicio").value;
			var hora_fin = document.getElementById("hora_fin").value;
			var operacion = document.getElementById("operacion").value;
			var id_reserva = document.getElementById("id_reserva").value;
			var msg = "Se han encontrado los siguientes errores:\n";
			
			if(operacion == "registrar" || operacion == "modificar")
			{
				if(hora_fin == "")
				{
					resultado = false;
					msg += "\n+ No ha escogido una hora de Fin de Reserva!";
				}
					
				if(id_cliente == "")
				{
					resultado = false;
					msg += "\n+ No ha seleccionado Cliente!"
				}
						
				if(pago_adelantado == "")
				{
					resultado = false;
					msg += "\n+ No ha ingresado el Pago Adelantado!"
				}
						
				if(resultado)
				{
					document.reserva.id_cliente.value = document.getElementById("id_cliente").value;
					document.reserva.id_usuario.value = document.getElementById("id_usuario").value;
					document.reserva.operacion.value = document.getElementById("operacion").value;
					document.reserva.hora_inicio.value = document.getElementById("hora_inicio").value;
					document.reserva.hora_fin.value = document.getElementById("hora_fin").value;
					document.reserva.pago_adelantado.value = document.getElementById("pago_adelantado").value;
					document.reserva.id_reservax.value = document.getElementById("id_reserva").value;					
					document.reserva.comentarios.value = document.getElementById("comentarios").value;
					
					document.reserva.submit();
				}					
				else
					alert(msg);	
				}
				if(operacion == "cancelar")
				{
					if(confirm('¿Esta seguro de cancelar la reserva?'))
					{
						document.reserva.id_reservax.value = document.getElementById("id_reserva").value;
						document.reserva.operacion.value = document.getElementById("operacion").value;						
						document.reserva.submit();
					}
				}
			}

		</script>
	</head>
	<body>
		
		<div id="div_main">
		<?php 
			include("../header.php");		
		?>
				
		<div id = "main" align="center" >
			<form name="reserva" action="<?php echo $enlace_procesar; ?>" method="POST">
				<input name="id_cliente" type="hidden"/>
				<input name="id_usuario" type="hidden" />
				<input name="operacion" type="hidden" />
				<input name="id_reservax" type="hidden" />
				<input name="hora_inicio" type="hidden" />
				<input name="hora_fin" type="hidden" />
				<input name="pago_adelantado" type="hidden" />
				<input id="fecha_mostrar_db" name="fecha_mostrar_db" type="hidden">
				<input name="comentarios" type="hidden" />
				<input name="id_cliente_seleccionado" type="hidden" value="<?php echo $id_cliente_seleccionado;?>" />
				<div id="div_reservas_semana" >
					
					<div id="div_reservas_titulo" align="center">
						<span id="reservas_titulo"><?php echo "Reservas Mostradas entre las fechas: $dia_1_str y $dia_7_str";?></span>
						<div id="ir_a_fecha">
							<span ><u>Ir a fecha:</u></span>
							<input id="fecha_mostrar" type="text" readonly="readonly"/> 
						</div>
					</div>
					
					
					<div id = "div_tabla_reservas">
						<table id="tabla_reservas" >
							<tr>
								<td width="90px" style="border: none;"></td>
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
								<td style="border: none;" align="center">
									<div class="div_fecha_titulo">
										<div align="center"><span class="span_fecha_titulo_nombre_dia"><?php echo $nombre_dia;?></span></div>
										<div align="center"><span class="span_fecha_titulo_fecha"><?php echo $fecha_str;?></span></div>
									</div>
								</td>
							<?php
							}?>
							</tr>
							<?php
							
							$minuto = 0;
							$hora= $hora_inicial;
							$class_hora = "hora_0";
							$class_div_horario = "div_horario_0";
							$class_td_horario = "td_horario_0";
							$xxi = 0;
							for($i = $hora * 2; $i < 48; $i++)
							{
								
								?>
								<tr>
								<?php
								echo "<td align=\"center\" class=\"$class_td_horario\">";
								$hora_str = date("h:i A.", mktime($hora, $minuto, 0, 1,1, 2000));
									
								echo "<div class=\"$class_div_horario\" align=\"center\"><span class=\"$class_hora\">$hora_str</span></div>";
								echo "</td>";
								
								$dia = 1;
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
									
									$fecha_hora = date("Y-m-d", strtotime( date('Y-m-d', strtotime($d)) ))." ".date("H:i:s", mktime($hora, $minuto, 0, 1,1, 2000));
									$fecha_hora_m30 = date("Y-m-d H:i:s", strtotime('+30 minutes',strtotime( date('Y-m-d H:i:s', strtotime($fecha_hora)) )));
									$fecha_hora_str = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($fecha_hora))));
									
									$lista_reservas_activas = $resBLO->ListarReservaActivaXFechaIniyFechaFin($id_centro, $fecha_hora, $fecha_hora_m30);
									
									$title = "Fecha: $nombre_dia $fecha_str - Hora: $hora_str";
									
									if(!is_null($lista_reservas_activas))
									{
										$res = $lista_reservas_activas[0];
										
										if($res->fecha_hora_inicio == $fecha_hora)
										{
											$hora_inicio = strtotime($res->fecha_hora_inicio);
											$hora_fin = strtotime($res->fecha_hora_fin);
											
											$reserva_minutos = round(abs($hora_fin - $hora_inicio) / 60,2);
											$row_span = round($reserva_minutos / 30);
												
											$reserva_hora_inicio = date("h:i A.", strtotime( date('Y-m-d H:i:s', strtotime($res->fecha_hora_inicio)) ));
											$reserva_hora_fin = date("H:i:s", strtotime( date('Y-m-d H:i:s', strtotime($res->fecha_hora_fin)) ));
											$reserva_hora_fin_str = date("h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($res->fecha_hora_fin)) ));
											
											$fecha_creacion = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($res->fecha_hora_registro)) ));
												
											$title = "Fecha: $nombre_dia $fecha_str [Hora Inicio: $reserva_hora_inicio - Hora Fin: $reserva_hora_fin_str] ";
											$pago_adelantado = $res->pago_adelantado;
											$pago_adelantado = "S/.".number_format ($pago_adelantado, 2);
											
											?>
											
											<td class="td_fecha_hora_reserva" title="<?php echo $title;?>" rowspan="<?php echo $row_span;?>" >
												<input id="fecha_hora_ini_str" type="hidden" value="<?php echo $fecha_hora_str;?>"/>
												<input id="fecha_hora_ini" type="hidden" value="<?php echo $fecha_hora;?>"/>
												<input id="fecha_hora_fin_str" type="hidden" value="<?php echo $reserva_hora_fin_str;?>"/>
												<input id="fecha_hora_fin" type="hidden" value="<?php echo $reserva_hora_fin;?>"/>
												<input id="res_id_reserva" type="hidden" value="<?php echo $res->id;?>"/>
												<input id="res_id_cliente" type="hidden" value="<?php echo $res->id_cliente;?>"/>
												<input id="res_nombre_cliente" type="hidden" value="<?php echo $res->cliente_nombres_apellidos;?>"/>
												<input id="res_comentarios" type="hidden" value="<?php echo $res->comentarios;?>"/>
												<input id="res_pago_adelantado" type="hidden" value="<?php echo $res->pago_adelantado;?>"/>
												<input id="res_creado_por" type="hidden" value="<?php echo $res->usuario_creacion;?>"/>
												<input id="res_fecha_creacion" type="hidden" value="<?php echo $fecha_creacion;?>"/>
												<input id="p_crear_rc" type="hidden" value="<?php echo $permiso_crear_reserva_cancha->isOK ? 1 : 0;?>" />
												<input id="p_ver_rc" type="hidden" value="<?php echo $permiso_ver_detalle_reserva_cancha->isOK ? 1 : 0;?>" />
												<input id="p_modificar_rc" type="hidden" value="<?php echo $permiso_modificar_reserva_cancha->isOK ? 1 : 0;?>" />
												<input id="p_modificar_rc" type="hidden" value="<?php echo $permiso_modificar_reserva_cancha->isOK ? 1 : 0;?>" />
												<input id="p_cancelar_rc" type="hidden" value="<?php $permiso_cancelar_reserva_cancha->isOK ? 1 : 0;?>" />
												<div class="div_cliente" align="center" style="background-color: <?php echo $res->colorweb;?>">
													<div class="div_cliente_nombre">
														<?php echo "[<b>$xxi</b> - $d - $dia]". $res->cliente_nombres_apellidos;?></br> 
														[A Cta. <?php echo $pago_adelantado;?>]
													</div>
												</div>
												
											</td>
											
										
										<?php	
										}
										
									}
									else
										echo "<td class=\"td_fecha_hora_reserva_libre\"><b>$xxi</b> - $d: $title</td>";	
									$dia++;
									$xxi ++;
								}
									
								?>
								</tr>
							<?php
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
								
							}
							?>
						</table>
					</div>
					
					<div id="div_leyenda_estados">
					<?php
					$lista_estados = $resBLO->ListarEstadoTodos();
					if(!is_null($lista_estados))
					{
						foreach($lista_estados as $e)
						{?>
						<div class="div_reserva_estado">
							<div class="etiqueta_leyenda"><span><?php echo $e->descripcion;?></span></div>
							<div class="leyenda_estado" style="background-color: <?php echo $e->colorweb;?>"></div>
						</div>
						<?php	
						}	
					}
					?>
					</div>	
				</div>
				<div id = "div_extra">
					<input id="id_cliente" type="hidden"/>
					<input id="id_usuario" type="hidden" value="<?php echo $usuario->id; ?>"/>
					<input id="operacion" type="hidden" />
					<input id="fecha_db" type="hidden" />
					<input id="id_reserva" type="hidden" />
					<div style="float:left; border: dotted 1px #3399FF; background-color: #FFFFFF;
					width: 400px; margin-left: 350px; ">
						<table style="color:#585858; margin-left: 10px;">
							<tr>
								<td colspan="3">
									<div style="float: left; width: 300px;">
										<img src="<?php echo $global_images_folder; ?>logo-delocal.png" style="width:63px; height:72px;"/></br>
										<span style="font-size:11px; font-weight: bold">Datos de la Reserva:</span>	
									</div>
								</td>					
							</tr>
							<tr height="20px"></tr>
							<tr>
								<td width="100px" colspan="2">
									<span style="font-size:11px;">Fecha:</span>
								</td>
								<td>
									<input id="fecha" class="clase12" readonly=readonly style="font-size:11px; width: 70px; text-align: center;"/>
								</td>
							</tr>
							<tr>
								<td width="130px" colspan="2">
									<span style="font-size:11px;">Hora Inicio:</span>
								</td>
								<td>
									<select id="hora_inicio"  value="" class="clase12" style="font-size:11px;"/>
								</td>
							</tr>
							<tr>
								<td width="130px" colspan="2">
									<span style="font-size:11px;">Hora Fin:</span>
								</td>
								<td>
									<!--input name="hora_fin" value=""
									class="clase12" readonly=readonly style="font-size:11px; width: 70px; text-align: center;"/-->
									<select id="hora_fin" style="font-size:11px;" class="clase12">	</select>									
								</td>
							</tr>
							<tr>
								<td width="100px" colspan="2">
									<span style="font-size:11px;">Cliente:</span>
								</td>
								<td>
									<input name="cliente" type="hidden" />
									<input name="nombre_cliente" id = "nombre_cliente" value=""
										class="clase12" readonly=readonly style="font-size:11px; width: 210px; text-align: center;"/>
									<input type="button" style="font-size:11px;" value="..." id="seleccionar_cliente"/>
								</td>
							</tr>
							<tr>
								<td width="100px">
									<span style="font-size:11px;">A cuenta:</span>									
								</td>
								<td align="right">
									<span style="font-size:11px;">S/.</span>	
								</td>								
								<td>
									<input id="pago_adelantado" value="" 
									class="clase12" style="font-size:11px; width: 70px; text-align: center;"/>
								</td>
							</tr>
							<tr>
								<td width="100px" colspan="2">
									<span style="font-size:11px;">Comentarios:</span>									
								</td>
								<td>
									<textarea id="comentarios" cols="35" rows="3" style="" class="clase12"></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="opciones_reserva" style="display: none;">
										<span style="font-size:11px;">Creado por:</span>
									</div>
								</td>
								<td>
									<div class="opciones_reserva" style="display: none;">
										<input id="creado_por" readonly="readonly" value="" style="font-family: Helvetica; font-size:11px; width: 120px; text-align: center;"/>
									</div>
								</td>	
								</td>								
							</tr>
							<tr>
								<td colspan="2">
									<div class="opciones_reserva" style="display: none;">
										<span style="font-size:11px;">Fecha Creación:</span>
									</div>
								</td>
								<td>
									<div class="opciones_reserva" id="div_fecha_creacion" style="display: none;">
										<input id="fecha_creacion" readonly="readonly" value="" style=""/>
									</div> 
								</td>								
							</tr>
							<tr>
								<td colspan="2">
									<div class="opciones_reserva" style="display: none;">
										<span style="font-size:11px;">Opciones:</span>
									</div>
								</td>
								<td>
									<div class="opciones_reserva" style="display: none;">
										<select id="opcion_reserva" style="font-size:11px;" class="clase12">
											<option value="0">Seleccione...</option>
											<option value="1" <?php echo $permiso_modificar_reserva_cancha->isOK ? "" : "disabled=\"disabled\"";?>> Modificar Reserva</option>
											<option value="5" <?php echo $permiso_cancelar_reserva_cancha->isOK ? "" : "disabled=\"disabled\"";?>> Cancelar Reserva</option>
											
										</select>
									</div>	
								</td>								
							</tr>
							<tr>
								<td width="100px" align="left" colspan="2">
									<input type="button" class="clase12" style="font-size: 11px;" id="guardar_reserva" value="Guardar" 
									onclick="GuardarReserva()"/>									
								</td>
								<td align="right">
									<input type="button" class="clase12" style="font-size: 11px;" id="cerrar_ventana" value="Cerrar" onclick="CerrarVentanaReserva()"/>									
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<span class="clase12" style="font-size: 11px; color: red;" id="msg_error"></span>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
			</form>
		</div>
		</div>
	</body>
</html>
	
