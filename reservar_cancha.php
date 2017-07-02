<?php

include ('usuario.php');
include ('general.php');
include ('cuenta_venta.php');
include ('security.php');
include ('reserva_cancha.php');
include ('centro.php');

define("L_LANG", "es_ES");

date_default_timezone_set('America/Lima');

//require_once('calendar/classes/tc_calendar.php');
require_once('calendar/tc_calendar.php');

$enlace = "reservar_cancha.php?key=$usr_key";

function valid_date($date, $format = 'YYYY-MM-DD')
{
    if(strlen($date) >= 8 && strlen($date) <= 10)
    {
        $separator_only = str_replace(array('M','D','Y'),'', $format);
        $separator = $separator_only[0];
        if($separator){
            $regexp = str_replace($separator, "\\" . $separator, $format);
            $regexp = str_replace('MM', '(0[1-9]|1[0-2])', $regexp);
            $regexp = str_replace('M', '(0?[1-9]|1[0-2])', $regexp);
            $regexp = str_replace('DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('YYYY', '\d{4}', $regexp);
            $regexp = str_replace('YY', '\d{2}', $regexp);
            if($regexp != $date && preg_match('/'.$regexp.'$/', $date)){
                foreach (array_combine(explode($separator,$format), explode($separator,$date)) as $key=>$value) {
                    if ($key == 'YY') $year = '20'.$value;
                    if ($key == 'YYYY') $year = $value;
                    if ($key[0] == 'M') $month = $value;
                    if ($key[0] == 'D') $day = $value;
                }
                if (checkdate($month,$day,$year)) return true;
            }
        }
    }
    return false;
}

if(isset($_GET['fecha']))
	$fecha = $_GET['fecha'];
else
{
	if(isset($_POST['fechax']))
		$fecha = $_POST['fechax'];
	else
		$fecha = '';	
}

if(isset($_POST['id_cliente']))
	$id_cliente = $_POST['id_cliente'];
else
	$id_cliente = 0;

if(isset($_POST['operacion']))
	$operacion = $_POST['operacion'];
else
	$operacion = "";

if(isset($_POST['centro']))
	$centro = $_POST['centro'];
else
	$centro = 0;

if(isset($_POST['fecha_seleccionada']))
	$fecha_seleccionada = $_POST['fecha_seleccionada'];
else
	$fecha_seleccionada = "";

if($fecha == "")
	$fecha = $fecha_seleccionada; 

if(isset($_POST['hora_inicio']))
	$hora_inicio = $_POST['hora_inicio'];
else
	$hora_inicio = "";

if(isset($_POST['hora_fin']))
	$hora_fin = $_POST['hora_fin'];
else
	$hora_fin = "";

if(isset($_POST['reserva_estado']))
	$reserva_estado = $_POST['reserva_estado'];
else
	$reserva_estado = 0;

if(isset($_POST['comentarios']))
	$comenterios = $_POST['comentarios'];
else
	$comentarios = "";
	
if(isset($_POST['id_reserva']))
	$id_reserva = $_POST['id_reserva'];
else
	$id_reserva = 0;

if(isset($_POST['pago_adelantado']))
	$pago_adelantado = $_POST['pago_adelantado'];
else
	$pago_adelantado = "";

function ConvertirAYMD($fecha_texto)
{
	$fecha_xx = substr($fecha_texto, 6, 4)."-".substr($fecha_texto, 3, 2)."-".substr($fecha_texto, 0, 2);
	if(valid_date($fecha_xx))
		return $fecha_xx;
	else 
		return $fecha_texto;
}

if($operacion == "registrar" || $operacion == "modificar")
{
		
	if($id_cliente > 0)
	{
		$resBLO = new ReservaCanchaBLO();
		$reserva = new ReservaCancha();
		
		$reserva->id = $id_reserva;
		$reserva->id_centro = $centro;
		$reserva->id_cliente = $id_cliente;
		$reserva->fecha_hora_registro = date('Y-m-d H:m:s');
		$reserva->fecha_hora_inicio = $fecha_seleccionada . ' ' . $hora_inicio;
		$reserva->fecha_hora_fin = $fecha_seleccionada . ' ' . $hora_fin;
		$reserva->pago_adelantado = $pago_adelantado;
		$reserva->comentarios = strtoupper($comentarios);
		$reserva->id_usuario_creacion = $cUsr->id;
		$reserva->estado = 1;
		
		if($operacion == "registrar")
			$resBLO->Registrar($reserva);
		if($operacion == "modificar")
			$resultado = $resBLO->Actualizar($reserva); 		
	}
} 

if($operacion == "cancelar")
{
	if($reserva_estado > 0)
	{
		$resBLO = new ReservaCanchaBLO();
		$resBLO->ActualizarEstado($id_reserva, $reserva_estado);	
	}
	
}


if(isset($_POST['centro']))
{
	$centro = $_POST['centro'];
	$reservaBLO = new ReservaCanchaBLO();	
}
else
	$centro = 1; 


function add_date($givendate, $day=0, $mth=0, $yr=0) 
{
	$cd = strtotime($givendate);
	$newdate = date('Y-m-d h:i:s', mktime(date('h',$cd),
	date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
	date('d',$cd)+$day, date('Y',$cd)+$yr));
    return $newdate;
}

function crear_fecha_hora($fecha_YYYYMMDD, $inicio, $idx)
{
	$horaX = floor(($idx + $inicio) / 2);
	$horaXX = $horaX; 
	
	if($horaX >= 12)
	{
		$horaAMPMx = $horaX - 12;
		$ampm = 'PM';
	}
	else
	{
		$horaAMPMx = $horaX;
		$ampm = 'AM';
	}			
				
	if($idx % 2 == 0)
		$min = '00';
	else 
		$min = '30';
		
	//$hora_xx = "0" . $horaXX . ":" . $min . ":" . "00";
	$hora_xx = $horaXX. $min;
	
	if($horaAMPMx == 0)
		$horaAMPMx = 12;		
										
	$fecha_hora_str_xx = $fecha_YYYYMMDD. " ". str_pad($horaX, 2, "0", STR_PAD_LEFT). ":". $min. ":00";
	 						
	$hora_ampm_str = str_pad($horaAMPMx, 2, "0", STR_PAD_LEFT).':'.$min.' '.$ampm;
										
	$j = $idx + 1;
											
	$horaX = floor(($j + $inicio) / 2);
										
	if($j % 2 == 0)
		$min = '00';
	else 
		$min = '30';
										
	$fecha_hora_str_m30_xx = $fecha_YYYYMMDD. " ". str_pad($horaX, 2, "0", STR_PAD_LEFT). ":". $min. ":00";
	
	$fecha_hora_xx = array();	
	
	$fecha_hora_xx[] = $hora_ampm_str;
	$fecha_hora_xx[] = $fecha_hora_str_xx;
	$fecha_hora_xx[] = $fecha_hora_str_m30_xx;	
	//$fecha_hora_xx[] = substr($hora_xx, strlen($hora_xx) - 8);
	$fecha_hora_xx[] = $hora_xx;
	

	return $fecha_hora_xx;
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>RODO </title>
		<meta name="author" content="Jesus Rodriguez" />
		<script language="JavaScript" src="jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="jquery.cookie.js"></script>
		<!-- Date: 2011-11-28 -->
		<link rel="stylesheet" href="style.css" type="text/css" />
		
		<style media="screen" type="text/css">
			.box {
		    width: 140px;		    
		    color: #FFFFFF;
		    font-family:Helvetica;
		    font-size:11px;
		    font-weight:bold;		    
		}
		
		</style>
		
		
		
		<script type="text/javascript">
			
			function AsignarHora(hora)
			{
				document.getElementById("hid_hora_inicio").value = hora;
				document.getElementById("id_reserva").value = 0;
			}
			   
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
				
			function GuardarReserva()
			{
				resultado = true;
				var fecha = document.getElementById("fecha_1").value;
				var id_cliente = document.getElementById("id_cliente").value;
				var pago_adelantado = document.getElementById("pago_adelantado").value;
				var hora_fin = document.getElementById("hora_fin").value;
				var operacion = document.getElementById("operacion").value;
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
						document.reserva.hora_inicio.value = document.getElementById("hora_inicio").value;
						document.reserva.hora_fin.value = document.getElementById("hora_fin").value;
						document.reserva.comentarios.value = document.getElementById("comentarios").value;
						document.reserva.pago_adelantado.value = document.getElementById("pago_adelantado").value;
						document.reserva.fecha_seleccionada.value = document.getElementById("fecha_1").value;
						document.reserva.submit();	
					}
					else
						alert(msg);	
				}
				if(operacion == "cancelar")
				{
					if(confirm('¿Esta seguro de cancelar la reserva?'))
					{
						document.reserva.fecha_seleccionada.value = document.getElementById("fecha_1").value;
						document.reserva.reserva_estado.value = 5;
						document.reserva.submit();
					}
				}
			}
				
			function MostrarReserva(reserva)
			{
				document.getElementById("id_reserva").value = reserva;
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
					ampm = "PM";
					hora = hora - 12;
				}
				else
					ampm = "AM";
				
				if(hora == 24)
				{
					hora = 0;
					ampm = "AM";
				}
				
				if(hora == 0)		
					hora = 12;
				
				hora_valor = "" + hora;
				var pad = "00";
				hora_valor = pad.substring(0, pad.length - hora_valor.length) + hora_valor;
				hora_valor = hora_valor + ":" + min + " " + ampm;					
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
			   		ampm = "PM";
			   	}
			   	else
			   		ampm = "AM";
			   			
			   	if(hora == 0)
					hora = 12;
			   		
			   	if(hora == 24)
			   	{
			   		hora = 12;
			   		ampm = "AM";
			   	}
			   		
				hora_valor = "" + hora;
				var pad = "00";
				hora_valor = pad.substring(0, pad.length - hora_valor.length) + hora_valor;
				hora_valor = hora_valor + ":" + min + " " + ampm;
				return hora_valor;
			}
			   
		   	$(function() {
	    		var $main = $('#main'),
		    		$extra = $('#extra'),
		        	$overlay = $('<div>', {
			            css: {
			                position: 'absolute',
			                width: '1000px',//$main.outerWidth(),
			                height: '700px',
			                top: $main.position().top,
			                'margin-top': '-200px',
			                //left: '50%',
			                backgroundColor: 'rgba(192,192,192,0.5)',
			                zIndex: 10,
			                display: 'none'
							//'margin-left': '-500px'
							     
			            }		            
			    });
			    $overlay.append($extra);
			    $overlay.appendTo($main);
			    
			    $('#opcion_reserva').change(function()
			    {
			    	
			    	if($('#opcion_reserva').val() == 1)
			    	{
			    		$('#operacion').val("modificar");
			    		$hora_inicio = $('#hora_inicio');
				    	$hora_fin = $('#hora_fin');
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
					        				        		
					        $hora_inicio.append('<option value="' + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
					        $('#seleccionar_cliente').removeAttr('disabled');					        
							$('.opciones_reserva').css('display','none');							
							$('.opciones_reserva').empty();
							$('#pago_adelantado').removeAttr('readonly');
							$('#comentarios').removeAttr('readonly');
						}						
			    	}			    	
			    	if($('#opcion_reserva').val() == 5)
			    	{		    	
			    		$('#operacion').val("cancelar");
			    		$('#guardar_reserva').removeAttr('disabled');
			    	}
			    });
			    
			    $('#hora_inicio').change(function()
			    {
			    	$hora_inicio = $('#hora_inicio');
			    	var hora_inicio = TransformarHoraANro($('#hora_inicio').val());
			    	$hora_fin = $('#hora_fin');
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
							$hora_fin.append('<option value="' + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');							
					}
					
					hora_valor = "2359";
					if(parseInt(hora_valor) > parseInt(hora_inicio))
						$hora_fin.append('<option value="' + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
			    });
			    
			    
			    $('#hora_fin').change(function()
			    {
			    	var fecha = $('#fecha').val();
			    	var hora_inicio = $('#hora_inicio').val();
			    	var hora_fin = $('#hora_fin').val();
			    	var i = 0;
			    	$('#msg_error').empty();
			    	$('#guardar_reserva').attr('disabled','disabled');
			    	var id_reserva = $('#id_reserva').val();
					if(hora_fin != "")
			    	{
			    		var url = "query_reserva.php?fhi="+fecha+"%20"+hora_inicio+"&fhf="+fecha+"%20"+hora_fin+"&idx="+id_reserva+"&key=<?php echo $usr_key;?>";			    	
			    		$.getJSON(url, function(data)			    					        	
					    {					    	
					    	if(data != null)
					    	{					    		
					    		$.each(data, function(key, val) 
								{									
									if(val.estado != 5 && val.estado !=4)
										i++;
								});
								if(i > 0)
						    		$('#msg_error').append("Se encuentra(n) " + i + " conflicto(s) de horario");						    		
					    	}
					    	else
						    	$('#guardar_reserva').removeAttr('disabled');
						    
					    });					    
			    	}
			    });
			    
			    			    			    
			    $main.on('click', '.box', function(e) 
			    {
			        var $this = $(this);
			        //$this.css( 'z-index', 11 );
			          
			        $overlay.data('current', $this).show();
			        $extra.data('current', $this).show();
			        var reserva = $('#id_reserva').val();
			        $('#guardar_reserva').attr('disabled', 'disabled');
			        $('#msg_error').empty();
			        if(reserva == 0)
			        {
			        	var $hora_fin = $('#hora_fin');
				        var min, ampm;
				        var hid_hora_inicio = $('#hid_hora_inicio').val();
				        var hora_valor;
				        var $fecha = $('#fecha_1');				        
				        $('#seleccionar_cliente').removeAttr('disabled');				        
						$('.opciones_reserva').css('display','none');
						$('#operacion').val("registrar");
				        
				        $hora_inicio = $('#hora_inicio');
				        $hora_inicio.empty();
						$hora_inicio.append('<option value="' + TransformarHora(hid_hora_inicio) + '">' + TransformarHoraAMPM(hid_hora_inicio) + '</option>');
				        
				        $hora_fin.empty();
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
				        					        		
				        	if(parseInt(hora_valor) > parseInt(hid_hora_inicio))
				        		$hora_fin.append('<option value="' + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
				        }
				        //Agregando último horario
				        hora_valor = "2359";
				        if(parseInt(hora_valor) > parseInt(hid_hora_inicio))
				        		$hora_fin.append('<option value="' + TransformarHora(hora_valor) + '">' + TransformarHoraAMPM(hora_valor) + '</option>');
			        }
			        else
			        {
			        	$.getJSON("query_reserva.php?id=" + reserva + "&key=<?php echo $usr_key;?>", function(data)
				    	{
							$.each(data, function(key, val) 
							{
								$('#id_cliente').val(val.id);
								var fecha_1 = val.fecha_hora_inicio.substring(0, 10);
								var hora_inicio = val.fecha_hora_inicio.substring(11, 19);
								var hora_fin = val.fecha_hora_fin.substring(11, 19);
								$hora_fin = $('#hora_fin');
								$hora_inicio = $('#hora_inicio');
								$('#fecha_1').val(fecha_1);
								$hora_inicio.empty();
								$hora_inicio.append('<option value="' + hora_inicio + '">' + TransformarAAMPM(hora_inicio) + '</option>');
								$('#seleccionar_cliente').attr('disabled', 'disabled');								
								$('.opciones_reserva').css('display','block');
								$('#pago_adelantado').attr('readonly', 'readonly');
								$('#comentarios').attr('readonly', 'readonly');
								var $hora_fin = $('#hora_fin');
								$hora_fin.empty();
								$hora_fin.append('<option value="' + hora_fin + '">' + TransformarAAMPM(hora_fin) + '</option>');									
								$('#nombre_cliente').val(val.cliente_nombres_apellidos);
								var pago_adelantado = val.pago_adelantado;																	        
								$('#pago_adelantado').val(parseFloat(pago_adelantado, 10).toFixed(2));
								$('#comentarios').val(val.comentarios);
								$('#creado_por').val(val.usuario_creacion);								
								$('#fecha_creacion').val(val.fecha_hora_registro);
						 	});
						});
					}
				});
			    
			    $('#seleccionar_cliente').on('click', function()
				{
				   	mywindow = showModalDialog("buscar_cliente.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
				   	var cliente = $.cookie("cliente");			    					    
				    var url = "query_cliente.php?id=" + cliente + "&key=<?php echo $usr_key;?>"
				    
				   	$.getJSON(url, function(data) 
				   	{
						//var items = [];
						
						$.each(data, function(key, val) 
						{
							$('#nombre_cliente').val(val.nombres + ' ' + val.apellidos);								
							$('#id_cliente').val(val.id);
					  	});
					});
				   	
				    	
				});
				    
				    /*$('#cerrar_ventana').on('click', function() {
				        $overlay.hide();
				        $overlay.data('current').css('z-index', 1);
				    });*/
			});
		
			function IsNumeric(expression)
			{
			    return (String(expression).search(/^\d+$/) != -1);
			}
				
			function CerrarVentanaReserva()
			{
				var fecha = "<?php if($fecha == "") echo date('Y-m-d'); else echo ConvertirAYMD($fecha); ?>";
				location.href = "reservar_cancha.php?key=<?php echo $usr_key; ?>&fecha="+fecha;
			}
				
			function CambiarFecha(fec)
			{				
				document.reserva.fechax.value = fec;
				//alert(document.reserva.fechax.value);				
				document.reserva.submit();
			}
			
			function CambiarFechaCalendario()
			{
				var fecha = document.getElementById('fecha').value;
					//alert(fecha);
				if( fecha != '')
					CambiarFecha(fecha);
			}

		</script>
	</head>
	<body>
		
		<?php 
			include("header.php");		
		?>
				
		<div id = "main" style="float: left; padding-left: 20px; padding-top: 20px; width: 1000px; font-family: Helvetica;">
			
			<form name="reserva" action="<?php echo $enlace; ?>" method="POST">
				<input id="hid_hora_inicio" name="hid_hora_inicio" type="hidden"/>
				<input id="id_cliente" name="id_cliente" type="hidden"/>
				<input name="hora_inicio" type="hidden"/>				
				<input name="hora_fin" type="hidden"/>
				<input name="comentarios" type="hidden" />
				<input name="pago_adelantado" type="hidden" />
				<input name="fecha_seleccionada" type="hidden" />
				<input id="id_reserva" name="id_reserva" type="hidden" />
				<input id="reserva_estado" name="reserva_estado" type="hidden" />
				<input name="operacion" id="operacion" type="hidden"/>
				<input name="fechax" type="hidden"/>				
				<div id = "extra" style="margin-top:180px;">					
					<div style="float:left; border: dotted 1px #3399FF; background-color: #FFFFFF;
					width: 400px; margin-left: 350px; ">
						<table style="color:#585858; margin-left: 10px;">
							<tr>
								<td colspan="3">
									<div style="float: left; width: 300px;">
										<img src="images/logo-delocal.png" style="width:63px; height:72px;"/></br>
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
									<input id="fecha_1" value="<?php if($fecha == "") echo date('Y-m-d'); else echo ConvertirAYMD($fecha); ?>"
									class="clase12" readonly=readonly style="font-size:11px; width: 70px; text-align: center;"/>
								</td>
							</tr>
							<tr>
								<td width="130px" colspan="2">
									<span style="font-size:11px;">Hora Inicio:</span>
								</td>
								<td>
									<select id="hora_inicio" name="hora_inicio" value="" 
									class="clase12" style="font-size:11px;"/>
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
									<textarea id="comentarios" cols="35" rows="3" style="font-size:11px; resize: none;" class="clase12"></textarea>
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
										<input id="fecha_creacion" readonly="readonly" value="" style="font-family: Helvetica; font-size:11px; width: 120px; text-align: center;"/>
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
											<option value="1">Modificar Reserva</option>
											<option value="5">Cancelar Reserva</option>
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
								
				<table id="tb_producto" style="border: dotted 1px #3399FF; width:1000px; background-color: #E6F2FF; color: #585858;" class="clase12">
					<tr style="height: 40px;">
						<td colspan="4" align="center">
							<span style="font-weight: bold; font-size:18px;">RESERVAS DE CANCHA SINTÉTICA</span>	
						</td>						
					</tr>
					<tr>
						<td>
							<span class="clase12" style="font-weight: bold;">Centro:</span>
						</td>
						<td>
							<select name="centro" style="width:100px;" class="clase12" onchange="SeleccionarCentro()">
								<option value=0>Seleccione...</option>
								<?php
									
								if($centro == 0)
									$selected = " selected='selected' ";
								else 
									$selected = '';
									
								$cCenBLO = new CentroBLO();
								$centros = $cCenBLO->Listar(); 
									
								foreach($centros as $c)
								{										
									if($c->id == $centro)
										$selected = " selected='selected' ";
									else 
										$selected = '';	
										
									echo "<option value=" . $c->id  . $selected . ">" . $c->descripcion . "</option>";
								}
								?>
							</select>
						</td>
						<td colspan="2" align="center">
							
						</td>
					</tr>
					<tr>						
						<td colspan="4" align="center">
							<div style="width:445px;">
								<div style="width:205px; float:left; ">
									<span style="font-size:12px;">Reservas mostradas para el dia:</span>
								</div>
								<div style="width:auto; float: left; ">
									<?php
									$fec = date_create($fecha);
									//echo "<span>Fec: ".$fecha."</span>";
									
									$fecha_cal = new tc_calendar("fecha", true, false);
									$fecha_cal->setIcon("calendar/images/iconCalendar.gif");
									if($fecha != '')
									{
										$fec = date_create($fecha);
										$fecha_str = date_format($fec, 'Y-m-d');
										$fecha_cal->setDate(date_format($fec,'d'), date_format($fec,'m'), date_format($fec,'Y'));
										$fecha_m1 = add_date($fecha, 1, 0, 0);
									}
									else
									{
										
										$fecha_cal->setDate(date('d'), date('m'), date('Y'));
										$fecha_m1 = add_date(date('d').'-'.date('m').'-'.date('Y'), 1, 0, 0);
										$fecha_str = date('Y-m-d');										
									}
									$fecha_m1 = date_create($fecha_m1);									
									$fecha_cal->setPath("calendar/");
									$fecha_cal->setYearInterval(2000, 2015);
									$fecha_cal->dateAllow('2012-01-01', '2015-03-01');
									//$fecha->setDateFormat('d-m-Y');
									$fecha_cal->setDateFormat('l, j F Y');
									$fecha_cal->setAlignment('left', 'bottom');
									$fecha_cal->setOnChange("CambiarFechaCalendario()");			
									$fecha_cal->writeScript();
																
									?>
								</div>
								<div style="width:28px; height: 18px; float:left; margin-left: 3px;" onmouseover="this.style.cursor='pointer'" 
								onclick="CambiarFecha('<?php echo date_format($fecha_m1, 'd-m-Y');?>')" 
								title="Ir a la fecha: <?php echo date_format($fecha_m1, 'd-m-Y');?>">
									<img src="images/arrow.png"/>
								</div>
							</div>
						</td>
						
					</tr>
					<tr height="20px;">
						<td colspan="4"></td>
					</tr>
					<tr align="center">
						<td colspan="4">
							<div if="horario" style="float: left; font:Helvetica; color: #585858;">
								<div id="turno_madrugada" style="float: left; width: 330px;">
									<table name='turno_madrugada'>
										<tr>
											<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
										</tr>
										<tr>
											<td colspan="2" align="center" width="100px">
												<span style="font-size:13px; color:#585858; font-weight: bold; ">
													TURNO MADRUGADA
												</span>
												
											</td>
										</tr>
										<tr>
											<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
										</tr>
									
										<?php
										
										for($i = 0; $i < 16; $i++)
										{
											
											$fecha_hora = crear_fecha_hora($fecha_str, 0, $i);
											if($fecha_hora != null)
											{
												$hora_ampm_str = $fecha_hora[0];
												$fecha_hora_str = $fecha_hora[1];
												$fecha_hora_str_m30 = $fecha_hora[2];
												$hora_str = $fecha_hora[3];
												
												$reservaBLO = new ReservaCanchaBLO();
												$reservas = $reservaBLO->RetornarReservaXFechaIniyFechaFin($centro, $fecha_hora_str, $fecha_hora_str_m30);
											}
											
																			
										?>
										<tr>
											<td width="60px" align="center">
												<div style="border: dotted 1px #3399FF; width: 60px;">
													<span style="font:Helvetica; font-size: 11px; color: #585858; font-weight: bold;">
														<?php echo $hora_ampm_str; ?>
													</span>
												</div>
											</td>
											<td align="center">												
												<?php 
												if($reservas != NULL)
												{
													$reservas_libres = array();
													$reservas_ocupadas = array();													
													foreach($reservas as $res)
													{
														if($res->estado == 4 || $res->estado == 5)
															$reservas_libres[] = $res;
														else
															$reservas_ocupadas[] = $res;
													}																										 
													if(count($reservas_ocupadas) == 0)
													{
														$res = $reservas_libres[0];														
														$color = $res->colorweb;														
														$msg = $res->estado_descripcion;
														$titulo = "Disponible para Reservar";
														$onclick = "AsignarHora('$hora_str')"; 
													}
													else
													{														
														$res = $reservas_ocupadas[0];
														$color = $res->colorweb;
														$titulo = "$res->cliente_nombres_apellidos";														
														$msg = $res->estado_descripcion. ": [$res->cliente_nombres_apellidos]";
														$onclick = "MostrarReserva($res->id)";
													}
												}
												else
												{
													$color = '#99CC00';
													$msg = 'Libre';
													$titulo = "Disponible para Reservar";
													$onclick = "AsignarHora('$hora_str')";
												}
													
														
												?>
												<div class="box" title = "<?php echo $titulo ; ?>" style="background-color: <?php echo $color ; ?>;" 
													onmouseover = "this.style.cursor='pointer'; this.style.backgroundColor='#0099CC';"													
													onmouseout = "this.style.backgroundColor='<?php echo $color;?>'"
													onclick= "<?php echo $onclick; ?>">
													<span>
														<?php echo $msg;?>
													</span>
												</div>
											</td>
										</tr>	
										<?php
										
											if($i % 2 == 1 && $i != 0)
											{?>
												<tr>
													<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
												</tr>
											<?php 
											}											
										}
										?>
									</table>
									
								</div>
								<div id="turno_manana" style="float: left; width: 330px;">
									<table name='turno_manana'>
										<tr>
											<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
										</tr>
										<tr>
											<td colspan="2" align="center" width="100px">
												<span style="font-size:13px; color:#585858; font-weight: bold; ">
													TURNO MAÑANA
												</span>
												
											</td>
										</tr>
										<tr>											
											<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
										</tr>
									
										<?php
										
										for($i = 0; $i < 16; $i++)
										{
											
											$fecha_hora = crear_fecha_hora($fecha_str, 16, $i);
											if($fecha_hora != null)
											{
												$hora_ampm_str = $fecha_hora[0];
												$fecha_hora_str = $fecha_hora[1];
												$fecha_hora_str_m30 = $fecha_hora[2];
												$hora_str = $fecha_hora[3];
												
												$reservaBLO = new ReservaCanchaBLO();
												$reservas = $reservaBLO->RetornarReservaXFechaIniyFechaFin($centro, $fecha_hora_str, $fecha_hora_str_m30);
											}
											
																			
										?>
										<tr>
											<td width="60px" align="center">
												<div style="border: dotted 1px #3399FF; width: 60px;">
													<span style="font:Helvetica; font-size: 11px; color: #585858; font-weight: bold;">
														<?php echo $hora_ampm_str; ?>
													</span>
												</div>
											</td>
											<td align="center">												
												<?php 
												if($reservas != NULL)
												{
													$reservas_libres = array();
													$reservas_ocupadas = array();													
													foreach($reservas as $res)
													{
														if($res->estado == 4 || $res->estado == 5)
															$reservas_libres[] = $res;
														else
															$reservas_ocupadas[] = $res;
													}																										 
													if(count($reservas_ocupadas) == 0)
													{
														$res = $reservas_libres[0];														
														$color = $res->colorweb;														
														$msg = $res->estado_descripcion;
														$titulo = "Disponible para Reservar";
														$onclick = "AsignarHora('$hora_str')"; 
													}
													else
													{														
														$res = $reservas_ocupadas[0];
														$color = $res->colorweb;
														$titulo = "$res->cliente_nombres_apellidos";														
														$msg = $res->estado_descripcion. ": [$res->cliente_nombres_apellidos]";
														$onclick = "MostrarReserva($res->id)";
													}
												}
												else
												{
													$color = '#99CC00';
													$msg = 'Libre';
													$titulo = "Disponible para Reservar";
													$onclick = "AsignarHora('$hora_str')";
												}
													
															
												?>
												<div class="box" title = "<?php echo $titulo ; ?>" style="background-color: <?php echo $color ; ?>;" 
													onmouseover = "this.style.cursor='pointer'; this.style.backgroundColor='#0099CC';"													
													onmouseout = "this.style.backgroundColor='<?php echo $color;?>'"
													onclick= "<?php echo $onclick; ?>">
													<span>
														<?php echo $msg;?>
													</span>
												</div>
											</td>
										</tr>	
										<?php
										
											if($i % 2 == 1 && $i != 0)
											{?>
												<tr>
													<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
												</tr>
											<?php 
											}											
										}
										?>
									</table>
									
								</div>

								<div id="turno_noche" style="float: left; width: 330px;">
									<table name='turno_noche'>
										<tr>
											<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
										</tr>
										<tr>
											<td colspan="2" align="center" width="100px">
												<span style="font-size:13px; color:#585858; font-weight: bold; ">
													TURNO NOCHE
												</span>
												
											</td>
										</tr>
										<tr>											
											<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
										</tr>
									
										<?php
										
										for($i = 0; $i < 16; $i++)
										{
											
											$fecha_hora = crear_fecha_hora($fecha_str, 32, $i);
											if($fecha_hora != null)
											{
												$hora_ampm_str = $fecha_hora[0];
												$fecha_hora_str = $fecha_hora[1];
												$fecha_hora_str_m30 = $fecha_hora[2];
												$hora_str = $fecha_hora[3];
												
												$reservaBLO = new ReservaCanchaBLO();
												$reservas = $reservaBLO->RetornarReservaXFechaIniyFechaFin($centro, $fecha_hora_str, $fecha_hora_str_m30);
											}
											
																			
										?>
										<tr>
											<td width="60px" align="center">
												<div style="border: dotted 1px #3399FF; width: 60px;">
													<span style="font:Helvetica; font-size: 11px; color: #585858; font-weight: bold;">
														<?php echo $hora_ampm_str; ?>
													</span>
												</div>
											</td>
											<td align="center">
												<?php
												if($reservas != NULL)
												{
													$reservas_libres = array();
													$reservas_ocupadas = array();													
													foreach($reservas as $res)
													{
														if($res->estado == 4 || $res->estado == 5)
															$reservas_libres[] = $res;
														else
															$reservas_ocupadas[] = $res;
													}																										 
													if(count($reservas_ocupadas) == 0)
													{
														$res = $reservas_libres[0];														
														$color = $res->colorweb;														
														$msg = $res->estado_descripcion;
														$titulo = "Disponible para Reservar";
														$onclick = "AsignarHora('$hora_str')"; 
													}
													else
													{														
														$res = $reservas_ocupadas[0];
														$color = $res->colorweb;
														$titulo = "$res->cliente_nombres_apellidos";														
														$msg = $res->estado_descripcion. ": [$res->cliente_nombres_apellidos]";
														$onclick = "MostrarReserva($res->id)";
													}
												}
												else
												{
													$color = '#99CC00';
													$msg = 'Libre';
													$titulo = "Disponible para Reservar";
													$onclick = "AsignarHora('$hora_str')";
												}
													
														
												?>
												<div class="box" title = "<?php echo $titulo ; ?>" style="background-color: <?php echo $color ; ?>;" 
													onmouseover = "this.style.cursor='pointer'; this.style.backgroundColor='#0099CC';"													
													onmouseout = "this.style.backgroundColor='<?php echo $color;?>'"
													onclick= "<?php echo $onclick; ?>">
													<span>
														<?php echo $msg;?>
													</span>
												</div>
											</td>
										</tr>	
										<?php
										
											if($i % 2 == 1 && $i != 0)
											{?>
												<tr>
													<td colspan="2" height="1px" style="border-top: dotted 2px #0099CC; border-bottom: dotted 2px #0099CC;"></td>
												</tr>
											<?php 
											}											
										}
										?>
									</table>
									
								</div>								
							</div>
							
														
						</td>
					</tr>	
				</table>
				<br>
				<bsr>
			</form>
		</div>
	</body>
</html>
	
