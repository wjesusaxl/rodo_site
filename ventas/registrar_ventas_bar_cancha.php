<?php

session_start();

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

$cliente_buscar_enlace_post = "../procesar_cliente.php";
$cliente_buscar_query_cliente = "../procesar_cliente.php";
$cliente_ruta_imagenes = "../images";
$cliente_tipo_fuente_externa = "ventas_bar_cliente_buscar";

include ("../clases/enc_dec.php");
include ("../clases/centro.php");
include ("../clases/opcion.php");
include ("../clases/usuario.php");
include ('../clases/general.php');
include ('../clases/security.php');
include ("../clases/cuenta_venta.php");
include ("../clases/producto.php");
include ("../clases/caja.php");
include ("../clases/turno_atencion.php");
include ("../clases/lugar_atencion.php");
include ("../clases/almacen.php");
include ("../clases/anuncio.php");
include ("../clases/cliente.php");

//$id_centro_ubicacion = 3; //Bar - Cancha;

if($_POST["id_turno_atencion"])
	$id_turno_atencion = $_POST["id_turno_atencion"];
else
	$id_turno_atencion = 0;

$opcBLO = new OpcionBLO();
$usrBLO = new UsuarioBLO();
$cenBLO = new CentroBLO();

$caBLO = new CajaBLO();
$taBLO = new TurnoAtencionBLO();
$cvBLO = new CuentaVentaBLO();
$laBLO = new LugarAtencionBLO();
$proBLO = new ProductoBLO();
$almBLO = new AlmacenBLO();

$opcion_ver_otro_turno = "3IR964RJ";
$opcion_ingresar_ventas_otro_turno = "84I3F3HS";
$opcion_ver_turno = "D1U76AV5";

$permiso_ver_otro_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_ver_otro_turno, $usuario->id, $id_centro );
$permiso_ingresar_ventas_otro_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_ingresar_ventas_otro_turno, $usuario->id, $id_centro);

$enlace_post = "registrar_ventas_bar_cancha.php?id_centro=$id_centro&usr_key=$usr_key&opcion_key=$opcion_key&op_original_key=$opcion_key";
$enlace_ver_turno = "../administracion/ver_turno.php?id_centro=$id_centro&usr_key=$usr_key&opcion_key=$opcion_ver_turno";
$enlace_procesar = "../procesar_cuenta_venta.php?id_centro=$id_centro&usr_key=$usr_key&op_original_key=$opcion_key";
$enlace_query_producto = "../procesar_producto.php";
$enlace_turno_atencion = "../procesar_turno_atencion?id_centro=$id_centro";


$id_almacen = 0;
$monto_otros_ingresos_mn = 0;
$monto_otros_egresos_mn = 0;
$monto_inicial_mn = 0;

if($id_turno_atencion > 0)
{
	$turno = $taBLO->RetornarXId($id_turno_atencion);
	$id_almacen = $turno->id_almacen;
	
	if(!is_null($turno))
	{
		
		$monto_inicial_mn = $turno->saldo_inicial_mn;
		$monto_otros_ingresos_mn = $taBLO->RetornarMontoTransaccionesPositivas($turno->id);
		$monto_otros_egresos_mn = $taBLO->RetornarMontoTransaccionesNegativas($turno->id);
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
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script src="../js/jquery.fixedheadertable.js"></script>
		<!--script language="JavaScript" src="../js/jquery.cookie.js"></script-->
		<link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
        
		<script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script>
		<script language="JavaScript" src="../js/jquery-ui.min.js"></script>
		
		
		<link rel="stylesheet" href="../styles/jquery-ui.css" type="text/css" media="all"/>
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        
		<!--script src="../js/jquery.livequery.js" /> </script-->
		<!-- Date: 2013-02-18 -->
		
		<script type="text/javascript">
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		function roundNumber(number, digits) {
            var multiple = Math.pow(10, digits);
            var rndedNum = Math.round(number * multiple) / multiple;
            return rndedNum;
        }
        
        function ActualizarMontoTotalTurno()
        {        	
        	$id_turno_atencion = $("#id_turno_atencion").val();
        	$monto_inicial_mn = $("#monto_inicial_mn").val();
        	$monto_otros_ingresos_mn = $("#monto_otros_ingresos_mn").val();
        	$monto_otros_egresos_mn = $("#monto_otros_egresos_mn").val();
        	
        	if($id_turno_atencion != "" && $id_turno_atencion != "0")
        	{
        		$url_turno2 = "<?php echo $enlace_turno_atencion;?>&operacion=query_cuentas_monto_total&id_turno=" + $id_turno_atencion;
        		
        		$.getJSON($url_turno2, function(data)
				{
					if(data != null)
					{
						
						$.each(data, function(key, val) 
						{
							
							$monto_total_ventas_mn = parseFloat(val.total_ingreso_efectivo_mn).toFixed(2);
							$monto_total_mn = parseFloat($monto_inicial_mn) + parseFloat($monto_otros_ingresos_mn);
							$monto_total_mn = $monto_total_mn - parseFloat($monto_otros_egresos_mn) + parseFloat(val.total_ingreso_efectivo_mn);
							$monto_total_mn = $monto_total_mn.toFixed(2);
							
							$html_monto_total_ventas_mn = "<b>S/. " + $monto_total_ventas_mn +  "</b>";
							$("#monto_total_ventas_mn").empty();
							$("#monto_total_ventas_mn").append($html_monto_total_ventas_mn);
							
							$html_monto_total_mn = "<b>S/. " + $monto_total_mn +  "</b>";
							$("#monto_total_mn").empty();
							$("#monto_total_mn").append($html_monto_total_mn);
							
						});
						
								
					}
						
				});
        	}			
        }
        
        function ActualizarMontoTotalCuentas()
        {
        	
        	ActualizarMontoTotalTurno();
        	
        	$(".div_cuenta").each(function()
        	{
        		$tabla_lista_items = $(this).find(".tabla_lista_items");
        		$div_total = $(this).find(".div_total");
        		$cuenta_venta_total = $div_total.find(".cuenta_venta_total");
        		
        		
        		//$total_cuenta_mn = $monto_inicial_mn + $monto_otros_ingresos_mn + $monto_otros_egresos_mn;
        		
        		//alert($total_cuenta_mn);
        		
        		$total_cuenta_mn = 0;
        		
        		$tabla_lista_items.find(".item_monto_total_mn").each(function()
        		{
        			$item_monto_total_mn = $(this).val();
        			
        			$total_cuenta_mn = $total_cuenta_mn + parseFloat($item_monto_total_mn);        			
        		});
        		
        		$cuenta_venta_total.empty();
        		$cuenta_venta_total.append("S/." + parseFloat($total_cuenta_mn).toFixed(2))
        		
        	});
        }
        
        function AgregarFilaItem()
        {
        	$id_producto = $("#id_producto_gral").val();
        	$id_cuenta_venta = $("#id_cuenta_venta_gral").val();
        	$id_usuario = $("#id_usuario").val();
        	
        	$tabla_lista_items = $("#tabla_lista_items_" + $id_cuenta_venta);
								
			if($id_producto > 0 && $id_cuenta_venta > 0 && $id_usuario > 0)
			{
				$url = "<?php echo $enlace_procesar;?>&operacion=registrar_item&id_producto=" + $id_producto + "&id_cuenta_venta=" + $id_cuenta_venta;
				$url = $url + "&id_usuario=" + $id_usuario;
										
				$.getJSON($url, function(data)
				{
					if(data != null)
					{
						$.each(data, function(key, val) 
						{
							if(val.estado == 1)
							{
								$tabla_lista_items.find(".sin_info").remove();
								$nro_fila = $tabla_lista_items.find("tbody tr").length + 1;								
								
								$val_total_mn = val.precio_total_mn * val.cantidad;
								$val_precio_total_mn = roundNumber(parseFloat(val.precio_total_mn), 2);
								$val_total_mn_str = roundNumber(parseFloat($val_total_mn), 2);
								
								$cantidad_opciones = "";
								$array_cantidad = val.opcion_cantidad.split(",");
													
								for($i = 0; $i < $array_cantidad.length; $i++)
								{
														
									$opcion = $array_cantidad[$i].split(":");
									$selected = "";
									if(roundNumber(val.cantidad_default, 2) == roundNumber($opcion[0], 2))
										$selected = "selected='selected'";
															
									$cantidad_opciones = $cantidad_opciones + "<option value=\"" + roundNumber($opcion[0], 2) + "\"" + $selected + ">" + $opcion[1] +"</option>";
								}
													
								$precios_opciones = "";
								$array_precios = val.array_precios.split(";");
								
								for($i = 0; $i < $array_precios.length; $i++)
								{
									$precio = $array_precios[$i].split(",");
									$selected = "";
									if(val.id_producto_precio == $precio[0])
										$selected = "selected='selected'";
														
									$precios_opciones = $precios_opciones + "<option value=\"" + $precio[0] + "\"" + $selected + ">" + $precio[1] + "</option>";
								}
																															
								$tr = "<tr>";
								$tr = $tr + "<td align='center'><div class=\"div_nro_fila\">" + $nro_fila + "</div>";
								$tr = $tr + "<input type=\"hidden\" class=\"id_cuenta_venta_item\" value=\"" + val.id +  "\"/>";
								$tr = $tr + "</td>";
								$tr = $tr + "<td align='center'>" + val.nro_serie + "</td>";
								$tr = $tr + "<td><b>" + val.descripcion_corta + "<b></td>";
								$tr = $tr + "<td align='center'>" + val.marca + "</td>";
								$tr = $tr + "<td align='center'>";
								$tr = $tr + "<input class=\"id_producto_precio_1\" type=\"hidden\" value=\"" + val.id_producto_precio + "\" />"
								$tr = $tr + "<select class=\"id_producto_precio texto_2_5\">" + $precios_opciones;
								$tr = $tr + "</select>";
								$tr = $tr + "</td>";
								$tr = $tr + "<td align='center'>";
								$tr = $tr + "<div class=\"div_precio_total_mn\">";
								$tr = $tr + "S/. " + $val_precio_total_mn.toFixed(2);
								$tr = $tr + "</div>";
								$tr = $tr + "</td>";
								$tr = $tr + "<td align='center'>";
								$tr = $tr + "<input class=\"cantidad_1\" type=\"hidden\" value=\"" + roundNumber(val.cantidad, 2) + "\" />"
								$tr = $tr + "<select class=\"cantidad texto_1_5\">" + $cantidad_opciones;
								$tr = $tr + "</select>";
								$tr = $tr + "</td>";
								$tr = $tr + "<td align='center'>"
								$tr = $tr + "<div class=\"div_item_monto_total_mn\">"
								$tr = $tr + "S/. " + $val_total_mn_str.toFixed(2);
								$tr = $tr + "</div>";
								$tr = $tr + "<input type=\"hidden\" class=\"item_monto_total_mn\" value=\"" + $val_total_mn +"\" />";
								$tr = $tr + "</td>";
								$tr = $tr + "<td align='center'>";
								$tr = $tr + "<select class=\"operacion_item texto_2\">";
								$tr = $tr + "<option value=\"0\">Seleccionar...</option>";
								$tr = $tr + "<option value=\"1\">Anular</option>";
								$tr = $tr + "</select>";
								$tr = $tr + "</td>";
														
								$tr = $tr + "</tr>";
													
								$tabla_lista_items.append($tr);
													
								ActualizarMontoTotalCuentas();
							}
							else
								alert(val.comentarios);	
						});
					}
							
				});
									
			}
			else
				alert("No has seleccionado un Producto!");
			
			$("#id_cuenta_venta_gral").val(0);
			$("#id_producto_gral").val(0);
        	
        	
        }
        
        function ActualizarCuentaCliente(id_cliente)
        {
        	
        	$(".div_cuenta").each(function()
			{
				$id_cuenta_venta = $(this).find(".id_cuenta_venta").val();
				$id_cuenta_venta_gral = $("#id_cuenta_venta_gral").val();
				
				if($id_cuenta_venta == $id_cuenta_venta_gral)
				{
					url = "../procesar_cuenta_venta.php?operacion=actualizar_info_cuenta_venta&id_cuenta_venta=" + $id_cuenta_venta + "&id_cliente=";
					url = url + id_cliente;
					
					$cliente = $(this).find(".cliente");
					
					$.getJSON(url, function(data)
					{
						if(data != null)
						{
							$.each(data, function(key, val)
							{
								if(val.estado == 1)
								{
									$cliente.val(val.cliente);									
									alert("Cliente: " + val.cliente);
								}
								else
									alert(val.comentarios);								
							});							
						}
					});
					
				}
				
			});
        	
        	/*$cliente = $(this);
				
				if($id_cuenta_venta > 0)
				{
					url = "../procesar_cuenta_venta.php?operacion=actualizar_info_cuenta_venta&id_cuenta_venta=" + $id_cuenta_venta + "&id_cliente=";
					url = url + $id_cliente;
					
					$.getJSON(url, function(data)
					{
						if(data != null)
						{
							$.each(data, function(key, val)
							{
								if(val.estado == 1)
								{
									$cliente.val(val.cliente);									
									alert("Cliente: " + val.cliente);
								}
								else
									alert(val.comentarios);								
							});							
						}
					});
				}*/
        }
		
		$(function()
		{
			$("#id_turno_atencion").change(function()
			{
				if($(this).val() > 0)
				{
					$("#venta").attr("action","<?php echo $enlace_post ;?>");
					$("#venta").submit();
				}
			})
			
			$("#crear_cuenta_venta").click(function()
			{
				if(confirm("Desea Crear una nueva Cuenta?"))
				{
					$("#venta").attr("action","<?php echo $enlace_procesar ;?>");
					$("#operacion").val("crear_cuenta_venta");
					$("#venta").submit();
					
				}
			});
			
			
			
			$(".descripcion_corta").live("keydown",function()
			{
				$descripcion_corta = $(this);
				
				$div_cuenta = $(this).parent().parent().parent().parent().parent().parent();
				
				$id_cuenta_venta = $div_cuenta.find(".id_cuenta_venta").val();
				
				$id_producto_aux = $div_cuenta.find(".id_producto_aux");
				$nro_serie_aux = $div_cuenta.find(".nro_serie_aux");
				$descripcion_corta_aux = $div_cuenta.find(".descripcion_corta_aux");
				$marca_aux = $div_cuenta.find(".marca_aux");
				
				$tabla_lista_items = $div_cuenta.find(".div_tabla_lista_items .tabla_lista_items");
				
				$url = "<?php echo $enlace_query_producto;?>?operacion=query&flag_venta=1&descripcion_corta=";
				
				$(this).autocomplete({
					source: function (request, response) 
					{
						$.ajax({
							url: $url + request.term,
							dataType: "json",
							type: "POST",
							success: function (data)
							{
								response(
									$.map(data, function(item)
									{
										return{
											value: item.id,
											label: item.descripcion_corta,
											marca: item.marca,
											nro_serie: item.nro_serie,
											descripcion_corta: item.descripcion_corta
										}
									})
								)
							}
						});
					},
					select: function(event, ui)
					{
						event.preventDefault();
						$descripcion_corta.val(ui.item.label);
						$descripcion_corta_aux.val(ui.item.label);
						$nro_serie_aux.val(ui.item.nro_serie);
						$id_producto_aux.val(ui.item.value);
						$marca_aux.val(ui.item.marca);
						
					},
					focus: function(event, ui) {
				        event.preventDefault();
				        $descripcion_corta.val(ui.item.label);
				    },
				    change: function(event, ui)
				    {
				    	if($descripcion_corta.val() != $descripcion_corta_aux.val())
				    		$id_producto_aux.val(0);
				    },
	                minLength: 3
				})
				
			});
			
			$(".nro_serie").change("click", function()
			{
				$nro_serie = $(this).parent().parent().find(".nro_serie");
				
				$div_cuenta = $(this).parent().parent().parent().parent().parent().parent();
				
				$id_cuenta_venta_gral = $div_cuenta.find(".id_cuenta_venta").val();
				
				if($nro_serie.val() != "")
				{
					$url = "<?php echo $enlace_query_producto;?>?operacion=query&flag_venta=1&nro_serie=" + $nro_serie.val();
					
					$.getJSON($url, function(data)
					{
						if(data != null)
						{
							$.each(data, function(key, val) 
							{
								$("#id_producto_gral").val(val.id);
								$("#id_cuenta_venta_gral").val($id_cuenta_venta_gral);
								
								AgregarFilaItem();
							});
							
						}
						
					});
					
				}
				else
					alert("No ha ingresado un Nro. de Serie Váiido!");
				
				$nro_serie.val("");	
			})
			
			$(".agregar_serie").live("click", function()
			{
				$nro_serie = $(this).parent().parent().find(".nro_serie");
				
				$div_cuenta = $(this).parent().parent().parent().parent().parent().parent();
				
				$id_cuenta_venta_gral = $div_cuenta.find(".id_cuenta_venta").val();
				
				if($nro_serie.val() != "")
				{
					$url = "<?php echo $enlace_query_producto;?>?operacion=query&flag_venta=1&nro_serie=" + $nro_serie.val();
					
					$.getJSON($url, function(data)
					{
						if(data != null)
						{
							$.each(data, function(key, val) 
							{
								$("#id_producto_gral").val(val.id);
								$("#id_cuenta_venta_gral").val($id_cuenta_venta_gral);
								
								AgregarFilaItem();
							});
							
						}
						
					});
					
				}
				else
					alert("No ha ingresado un Nro. de Serie Váiido!");
				
				$nro_serie.val("");
			});
			
			$(".agregar_descripcion").live("click", function()
			{
				$descripcion_corta = $(this).parent().parent().find(".descripcion_corta");
				$descripcion_corta_aux = $div_cuenta.find(".descripcion_corta_aux");
				
				$div_cuenta = $(this).parent().parent().parent().parent().parent().parent();
				$id_cuenta_venta = $div_cuenta.find(".id_cuenta_venta").val();
				
				$id_producto = $div_cuenta.find(".id_producto_aux").val();
				$id_producto_aux = $div_cuenta.find(".id_producto_aux");
				
				$("#id_producto_gral").val($id_producto);
				$("#id_cuenta_venta_gral").val($id_cuenta_venta);
				
				AgregarFilaItem();
				
				$descripcion_corta.val("");
				$descripcion_corta_aux.val("");
				$id_producto_aux.val(0);
				$descripcion_corta.focus();
			});
			
			$(".cantidad").live("change", function()
			{
				$id_cuenta_venta_item = $(this).parent().parent().find(".id_cuenta_venta_item").val();
				$cantidad = $(this).val();
				
				$select_cantidad = $(this);
				$td = $(this).parent();						
				$tr = $td.parent();
				$div_item_monto_total_mn = $tr.find(".div_item_monto_total_mn");
				$input_item_monto_total_mn = $tr.find(".item_monto_total_mn");
				
				$cantidad_1 = $td.find(".cantidad_1");
				$cantidad_1_val = $td.find(".cantidad_1").val();				
				
				$url_cambio = "<?php echo $enlace_procesar;?>&operacion=modificar_item&id_cuenta_venta_item=" + $id_cuenta_venta_item + "&cantidad=" + $cantidad;
				//$url = $url + "&id_usuario=" + $id_usuario;				
										
				$.getJSON($url_cambio, function(data)
				{
					if(data != null)
					{
						$.each(data, function(key, val) 
						{
							if(val.estado == 1)
							{
								
								$input_cantidad_1 = "<input type=\"hidden\" class=\"cantidad_1\" value=\"" + parseFloat(val.cantidad).toFixed(2) + "\" />";
								
								$cantidad_opciones = "";
								
								$array_cantidad = val.opcion_cantidad.split(",");
													
								for($i = 0; $i < $array_cantidad.length; $i++)
								{
														
									$opcion = $array_cantidad[$i].split(":");
									$selected = "";
									if(parseFloat(val.cantidad) == parseFloat($opcion[0]))
										$selected = "selected='selected'";
															
									$cantidad_opciones = $cantidad_opciones + "<option value=\"" + parseFloat($opcion[0]).toFixed(2) + "\"" + $selected + ">" + $opcion[1] +"</option>";
									
									$select_cantidad = "<select class=\"cantidad texto_1_5\">" + $cantidad_opciones + "</select>";
									
								}
								
								$td.empty();
								$td.append($input_cantidad_1);
								$td.append($select_cantidad);
								
								$item_monto_total_mn_val = parseFloat(val.cantidad) * parseFloat(val.precio_total_mn);
								$input_item_monto_total_mn.val($item_monto_total_mn_val);
								$item_monto_total_mn_val = "S/. " + parseFloat($item_monto_total_mn_val).toFixed(2);
									
								$div_item_monto_total_mn.empty();
								$div_item_monto_total_mn.append($item_monto_total_mn_val);
									
								ActualizarMontoTotalCuentas();
							}
							else
							{
								$select_cantidad.val($cantidad_1_val);
								
								alert(val.comentarios);
							}
								
						});
					}
				});
				
			});
			
			$(".id_producto_precio").live("change", function()
			{
				$id_cuenta_venta_item = $(this).parent().parent().find(".id_cuenta_venta_item").val();
				$cantidad = $(this).parent().parent().find(".cantidad").val();
				$id_producto_precio = $(this).val();
				
				$id_producto_precio_1 = $(this).parent().find(".id_producto_precio_1");
				$id_producto_precio_1_val = $(this).parent().find(".id_producto_precio_1").val();
				
				$select_id_producto_precio = $(this);
				
				$tr = $(this).parent().parent();
				$td = $(this).parent();
				
				$div_item_monto_total_mn = $tr.find(".div_item_monto_total_mn");
				$input_item_monto_total_mn = $tr.find(".item_monto_total_mn");
				$div_precio_total_mn = $tr.find(".div_precio_total_mn");
				
				$url_cambio = "<?php echo $enlace_procesar;?>&operacion=modificar_item&id_cuenta_venta_item=" + $id_cuenta_venta_item + "&id_producto_precio=" + $id_producto_precio;
				
				//alert($url_cambio);
				
				$.getJSON($url_cambio, function(data)
				{
					if(data != null)
					{
						$.each(data, function(key, val) 
						{
							if(val.estado == 1)
							{
								$input_id_producto_precio_1 = "<input type=\"hidden\" class=\"id_producto_precio_1\" value=\"" + val.id_producto_precio +"\" />";
								
								$precio_opciones = "";
								
								$array_precio = val.array_precios.split(";");
													
								for($i = 0; $i < $array_precio.length; $i++)
								{
														
									$opcion = $array_precio[$i].split(",");
									$selected = "";
									if(val.id_producto_precio == $opcion[0])
										$selected = "selected='selected'";
															
									$precio_opciones = $precio_opciones + "<option value=\"" + $opcion[0] + "\"" + $selected + ">" + $opcion[1] +"</option>";
									
									$select_precio = "<select class=\"id_producto_precio texto_2_5\">" + $precio_opciones + "</select>";
									
								}
								
								$td.empty();
								$td.append($input_id_producto_precio_1);
								$td.append($select_precio);
									
								$div_precio_total_mn.empty();
									
								$precio_total_mn = parseFloat(val.precio_total_mn);
								$precio_total_mn = "S/. " + parseFloat($precio_total_mn).toFixed(2);
								
								$div_precio_total_mn.append($precio_total_mn);
									
								$item_monto_total_mn_val = parseFloat(val.cantidad) * parseFloat(val.precio_total_mn);
								$input_item_monto_total_mn.val($item_monto_total_mn_val);
								$item_monto_total_mn_val = "S/. " + parseFloat($item_monto_total_mn_val).toFixed(2);
									
								$div_item_monto_total_mn.empty();
								$div_item_monto_total_mn.append($item_monto_total_mn_val);
									
								$id_producto_precio_1.val(val.id_producto_precio);
									
								ActualizarMontoTotalCuentas();
								
							}
							else
							{
								$select_id_producto_precio.val($id_producto_precio_1_val);
								alert(val.comentarios);
							}
						});
					}
					
				});	
						
			});
			
			$(".operacion_item").live("change", function()
			{
				$id_usuario = $("#id_usuario").val();
				$id_cuenta_venta_item = $(this).parent().parent().find(".id_cuenta_venta_item").val();
				
				$tabla_lista_items = $(this).parent().parent().parent().parent().parent();
				
				$tr = $(this).parent().parent();
				
				$operacion_item = $(this).val();
				
				if($operacion_item == 1)
				{
					$url_cambio = "<?php echo $enlace_procesar;?>&operacion=modificar_item&id_cuenta_venta_item=" + $id_cuenta_venta_item + "&flag_anulado=1&id_usuario=" + $id_usuario;
					
					if(confirm("Seguro que desea Eliminar este Item?"))
					{
						$.getJSON($url_cambio, function(data)
						{
							if(data != null)
							{
								$.each(data, function(key, val) 
								{
									if(val.estado == 1)
									{
										alert("Item Anulado!");
										$tr.remove();
										
										$cont = 1;
										$tabla_lista_items.find("tbody tr").each(function()
										{
											
											$div_nro_fila = $(this).find(".div_nro_fila");
											
											$div_nro_fila.empty();
											$div_nro_fila.append($cont);
											
											$cont++;
											
										});
										
										ActualizarMontoTotalCuentas();
										
										$nro_fila = $tabla_lista_items.find("tbody tr").length;
										$tr = "<tr class=\"sin_info\"><td colspan=9>No se han agregado Items a la Cuenta.</td></tr>";
										if($nro_fila == 0)
											$tabla_lista_items.append($tr);
										
										
										
																
									}
								});
							}
								
						});
						
					}
				}
			});
			
			$(".id_lugar_atencion").live("change", function()
			{
				$id_lugar_atencion = $(this).val();
				
				if($id_lugar_atencion > 0)
				{
					$div_cuenta = $(this).parent().parent();
					$id_cuenta_venta = $div_cuenta.find(".id_cuenta_venta").val();
					
					url = "../procesar_cuenta_venta.php?operacion=actualizar_info_cuenta_venta&id_cuenta_venta=" + $id_cuenta_venta + "&id_lugar_atencion=";
					url = url + $id_lugar_atencion;
					
					$.getJSON(url, function(data)
					{
						if(data != null)
						{
							$.each(data, function(key, val)
							{
								if(val.estado == 1)								
									alert("Lugar Atención: " + val.lugar_atencion);
								else
									alert(val.comentarios);								
							});							
						}
					});
				}
				
			});
			
			
			
			$('.cliente').live("click",function()
			{
				//mywindow = showModalDialog("../clientes/buscar.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
								
				$div_cuenta = $(this).parent().parent();
				$id_cuenta_venta = $div_cuenta.find(".id_cuenta_venta").val();
				
				$("#id_cuenta_venta_gral").val($id_cuenta_venta);
				
				$('#cliente_div_main').dialog('open');
				
					
			});
			
			$(".id_operacion_cuenta").live("change", function()
			{
				$id_operacion_cuenta = $(this).val();
				$div_cuenta = $(this).parent().parent();
				$id_cuenta_venta = $div_cuenta.find(".id_cuenta_venta").val();
				
				if($id_operacion_cuenta > 0)
				{
					$("#id_cuenta_venta_gral").val($id_cuenta_venta);
					
					switch($id_operacion_cuenta)
					{
						case "1": 
							$("#operacion").val("cerrar_cuenta"); 
							$mensaje = "Seguro que desea Cerrar la Cuenta?";
							break;
						case "2":
							$("#operacion").val("cancelar_cuenta"); 
							$mensaje = "Seguro que desea Cancelar la Cuenta?";
							break;							
					}
					
					if(confirm($mensaje))
					{
						$("#venta").attr("action", "<?php echo $enlace_procesar;?>");
						$("#venta").submit();	
					}
					
					
				}
			});
			
			$(".turno_key").live("click",function()
			{
				$turno_key = $("#turno_key").val();
				
				if($turno_key != "")
					window.open("<?php echo $enlace_ver_turno;?>&turno_atencion_key=" + $turno_key);
				else
					alert("No se ha encontrado el Turno!");
			});
			
			$(document).live("ajaxStop", function (e) 
			{
	      		//Dialogo Buscar Cliente
	      		$("#cliente_div_main").dialog("option", "position", "center");												
			});
			
		});
		</script>
		
		<style media="screen" type="text/css" >
			body { background-color: #F1F1F1; }
			#div_main {  margin-bottom: 20px; margin: 0 auto; overflow:hidden; width: 1200px; }
			#div_turno_activo { margin-top: 20px; margin-bottom: 20px; }
			#tabla_turno_activo { border: dotted #0099CC 1px; font-family: Helvetica; background-color: #FFFFFF; border-radius: 10px 10px 10px 10px;  }
			#tabla_turno_activo th{ font-size: 12px; color: #0099CC; }
			#tabla_turno_activo td{ font-size: 12px; color: #585858; border-top: dotted 1px #0099CC;  }
			#turnos_abiertos { margin-right: 10px;  }
			.etiqueta { font-family: Helvetica; font-size: 12px; font-weight: bold; color: #585858;}
			.mensaje { font-family: Helvetica; font-size: 12px; color: #585858;}
			#id_caja_turno { font-family: Helvetica; font-size: 12px; }
			#crear_cuenta_venta { font-family: Helvetica; font-size: 11px; }
			
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
			
			.texto_2 { width: 100px; text-align: center; font-size: 11px; }
			.texto_2_5 { width: 130px; text-align: center; font-size: 11px; }
			.texto_3, .compra_operacion, #id_almacen_destino { width: 150px; text-align: center; font-size: 11px; }
			.texto_3_5 { width: 180px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4_5 { width: 230px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_6 { width: 330px; text-align: center; font-size: 11px; }
			.texto_10 { width: 350px; text-align: center; font-size: 11px; }
			
			#div_lista_cuentas { border: dotted #0099CC 1px; border-radius: 10px 10px 10px 10px; padding: 10px 10px 10px 10px; 
				background-color: #FFFFFF; width: 1150px; margin: 0 auto; overflow: hidden; margin-top: 20px; }
			.div_cuenta { float: left; border: dotted #0099CC 1px; border-radius: 10px 10px 10px 10px; width: 1135px; padding: 5px 5px 5px 5px;
				margin-right: 10px; margin-bottom: 30px; -moz-box-shadow: 3px 3px 4px #000; -webkit-box-shadow: 3px 3px 4px #000; box-shadow: 3px 3px 4px #585858;}
			
			.div_cabecera_cuenta { float: left; width: 1130px; }
			.div_agregar_item { float: left; width: 1130px;  margin-bottom: 10px; }
			.div_cod_cuenta { float: left; background-color: #0099CC; padding: 0px 5px 0px 5px; border-radius: 5px 5px 5px 5px; }
			
			.div_info_cuenta { float: left; margin-left: 10px; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; }
			
			.tabla_lista_items th { color: #0099CC;   }
			
			
			.div_total { float: right; font-family: Helvetica; font-size: 16px; font-weight: bold; }
			.mensaje_total {  color: #FF3333; font-family: Helvetica; }
			.cuenta_venta_total {  width: 60px; color: #585858; }
			
			.mensaje_cuenta {font-size: 12px; font-weight: bold; color: #FFFFFF; font-family: Helvetica; }
			.cuenta_venta_codigo { font-family: Helvetica; font-size: 11px; color: #FFFFFF; }
			.cuenta_fecha_creacion { font-family: Helvetica; font-size: 12px; color: #585858; }
			.lbl_almacen { font-family: Helvetica; font-size: 12px; color: #585858; }
			
			.mensaje2 { font-family: Helvetica; font-size: 13px; color: #585858; font-weight: bold; }
			.label { font-family: Helvetica; font-size: 12px; padding-right: 5px; color: #0099CC; font-weight: bold; }
			.label2 { font-family: Helvetica; font-size: 12px; color: #585858; font-weight: bold; padding-right: 5px; }
			.label3 { font-family: Helvetica; font-size: 12px; color: #0099CC; font-weight: bold; padding-right: 5px; padding-left: 5px; }
			
			.div_lista_productos {float: left; width: 1130px; margin-top: 20px; font-family: Helvetica; font-weight: bold;
				color: #0099CC; font-size: 12px; text-shadow:1px 1px 1px #333; margin-bottom: 10px; }
			
			#div_titulo_lista_cuentas { float: left; font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; width: 1130px; 
				margin-bottom: 10px; text-shadow:1px 1px 1px #333; }
			
			.div_tabla_lista_items { float: left; padding-left: 15px; }
			.tabla_lista_items { border-collapse: collapse; }
			.tabla_lista_items th { border-top: dotted #0099CC 1px; border-bottom: dotted #0099CC 1px; font-family: Helvetica; font-size: 12px; 
				color: #0099CC;  }
			.tabla_lista_items tr { font-family: Helvetica; font-size: 12px; color: #585858; }
			.tabla_lista_items tr:nth-child(even) { background-color:#DAF1F7; }
			.tabla_lista_items tr:nth-child(odd) { background-color:#FFFFFF; }
			.tabla_lista_items { }
			
			.txt_item { width: 200px; padding-left: 5px; padding-right: 10px; }
			.btn_agregar_item { width: 100px; font-family: Helvetica; font-size: 12px; }
			
			.cliente { font-family: Helvetica; font-size: 11px; width: 250px; color: #585858; text-align: center; }
			.sin_promociones {color: #999999; }
			.lbl_mensaje_cambiar_cliente { color: #FF3333; font-size: 10px; padding-left: 2px; font-family: Helvetica; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px; }
			.operacion_cuenta { float: left; margin-left: 10px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; margin-top: 10px; 
				padding-left: 10px; padding-right: 10px; padding-top: 3px; padding-bottom: 3px; }
			.turno_key { text-decoration: underline; color: blue; }
			.turno_key:hover { cursor:pointer;}
			
		</style>
	</head>
	<body>
		<?php include ("../header.php"); ?>
		<div id="div_main" align="center">
		<form id="venta" name="venta" action="<?php echo $enlace_post;?>" method="POST">
			<input type="hidden" id="operacion" name="operacion" />
			<input type="hidden" id="id_almacen" name="id_almacen" value="<?php echo $id_almacen;?>" />
			<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>" />
			<input type="hidden" id="id_cuenta_venta_gral" name="id_cuenta_venta_gral" value="0"/>
			<input type="hidden" id="id_producto_gral" name="id_producto_gral" value="0" />
			<input type="hidden" id="monto_inicial_mn" value="<?php echo $monto_inicial_mn;?>" />
			<input type="hidden" id="monto_otros_ingresos_mn" value="<?php echo $monto_otros_ingresos_mn;?>" />
			<input type="hidden" id="monto_otros_egresos_mn" value="<?php echo $monto_otros_egresos_mn;?>" />
			<div id="div_turnos">
				<span class="etiqueta" id="turnos_abiertos">Turnos Abiertos:</span>
				<select id ="id_turno_atencion" name ="id_turno_atencion" class="texto_10">
					
				<?php
					echo "<option value=\"0\">Seleccione...</option>";
					$lista_turnos_activos = $taBLO->ListarTurnosActivos($id_centro);
					if(!is_null($lista_turnos_activos))
					{
							
						if(count($lista_turnos_activos) > 0)
						{
							foreach($lista_turnos_activos as $ta)
							{
								$disabled = "disabled='disabled'";
								$selected = "";
								
								$permiso_info_ventas = ($permiso_ver_otro_turno->isOK || $permiso_ingresar_ventas_otro_turno->isOK || $ta->id_usuario == $id_usuario);
								
								if($permiso_info_ventas) 
									$disabled = "";
								
								if($ta->id == $id_turno_atencion)
									$selected = "selected='selected'";
									
								$fecha_hora_inicio = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($ta->fecha_hora_inicio)) ));
								echo "<option value=\"$ta->id\" $disabled $selected>$ta->usuario ($fecha_hora_inicio): $ta->cod_caja [$ta->codigo]</option>";
								
								
								
							}
						}
						else
							echo "<span class=\"mensaje\">No hay información que mostrar</span>";
						
					}
					else
						echo "<span class=\"mensaje\">No hay información que mostrar</span>";
				
				?>
				</select>
			</div>
			
			<?php
			$permiso_info_ventas = ($permiso_ver_otro_turno->isOK || $permiso_ingresar_ventas_otro_turno->isOK || $turno->id_usuario == $id_usuario);
			
			if($id_turno_atencion > 0 && $permiso_info_ventas)
			{?>
			<div id ="div_turno_activo" align="center">
			<?php
			
			$fecha_hora_inicio = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($turno->fecha_hora_inicio)) ));
			$monto_total_ventas_mn = $taBLO->RetornarMontoTotalCuentaVentaXIdTurnoAtencion($id_turno_atencion);
			$monto_inicial_mn = $turno->saldo_inicial_mn;
			$monto_otros_ingresos_mn = $taBLO->RetornarMontoTransaccionesPositivas($id_turno_atencion);
			$monto_otros_egresos_mn = $taBLO->RetornarMontoTransaccionesNegativas($id_turno_atencion);
			//$total_transacciones_mn = $turno->saldo_inicial_mn + $monto_total_ventas_mn;
			$total_transacciones_mn = $monto_inicial_mn + $monto_total_ventas_mn + $monto_otros_ingresos_mn - $monto_otros_egresos_mn;
			?>
				<div id="div_tabla_turno_activo">
					<input type="hidden" id="turno_key" value="<?php echo $turno->auto_key;?>"/>
					<table id = "tabla_turno_activo">				
						<thead>
							<th width=200px>Caja</th>
							<th width=60px>Turno</th>
							<th width=80px>Usuario</th>						
							<th width=140px>Hora Inicio</th>
							<th width=100>Monto Inicial</th>
							<th width=100px>Total Ventas</th>
							<th width=110px>Otros Ingresos</th>
							<th width=100px>Total Egresos</th>							
							<th width=120px>TOTAL TURNO</th>
							<th width=100px>Almacén</th>
						</thead>
						<tbody>
							<tr>
								<td align="center"><b><?php echo strtoupper($turno->caja);?></b></td>
								<td align="center"><span class="turno_key" title="Ver Detalle de Turno"><?php echo $turno->codigo;?></span></td>
								
								<td align="center"><b><?php echo $turno->usuario;?></b></td>							
								<td align="center"><?php echo $fecha_hora_inicio;?></td>
								<td align="center"><?php echo "S/. ".number_format($turno->saldo_inicial_mn, 2);?></td>
								<td align="center"><div id="monto_total_ventas_mn"><b><?php echo "S/. ".number_format($monto_total_ventas_mn, 2);?></b></div></td>
								<td align="center"><?php echo "S/. ".number_format($monto_otros_ingresos_mn, 2);?></td>
								<td align="center"><?php echo "S/. ".number_format($monto_otros_egresos_mn, 2);?></td>
								<td align="center"><div id="monto_total_mn"><b><?php echo "S/. ".number_format($total_transacciones_mn, 2);?></b></div></td>
								<td align="center"><b><?php echo strtoupper($turno->almacen);?></b></td>
							</tr>
							<?php
							if($permiso_ingresar_ventas_otro_turno->isOK || $turno->id_usuario == $id_usuario)
							{?>
							<tr>
								<td colspan="10">
									<input type="button" id="crear_cuenta_venta" class="texto_2" value="Crear Cuenta"/>
								</td>
								
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				
				<?php
				
				
				//echo "ID Turno: $id_turno_atencion</br>";
				if($id_turno_atencion > 0)
				{
				?>
					
					<?php
					$lista_cuentas = $cvBLO->ListarCuentaVentaActivaXIdTurnoAtencion($id_turno_atencion);
					if(!is_null($lista_cuentas))
					{
						if(count($lista_cuentas) > 0)
							$display="display:block;";
						else
							$display="display:none;";
						?>
					
					<div id="div_lista_cuentas" style="<?php echo $display;?>">
						
						<?php
						
						foreach($lista_cuentas as $cv)
						{
							$i = 1;
							
							$monto_total_mn = $cvBLO->RetornarMontoTotalXIdCuenta($cv->id);
							$fecha_hora_creacion = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($cv->fecha_hora)) ));
						
							$id_lugar_atencion = $cv->id_lugar_atencion;
						?>
							<div class="div_cuenta">
								<input type="hidden" class="id_cuenta_venta" value="<?php echo $cv->id;?>" />
								<input type="hidden" class="id_cliente" value="<?php echo $cv->id_cliente;?>" />
								<input type="hidden" class="id_producto_aux"/>
								<input type="hidden" class="nro_serie_aux"/>
								<input type="hidden" class="marca_aux"/>
								<input type="hidden" class="descripcion_corta_aux" />
								
								<div class="div_cabecera_cuenta">
									<div class="div_cod_cuenta">
										<span class="mensaje_cuenta">Cuenta: </span>
										<span class="cuenta_venta_codigo"><?php echo str_pad($cv->id, 5, "0", STR_PAD_LEFT);?></span>					
									</div>
									<span class="label2">Fecha Creación: </span>
									<span class="cuenta_fecha_creacion"><?php echo $fecha_hora_creacion;?></span>
									<span class="label3"> / </span>
									<span class="label2">Lugar Atención: </span>
									<select class="id_lugar_atencion texto_2">
										<option value="0">Seleccione...</option>							
										<?php
											$las = $laBLO->ListarXIdCentroUbicacion($id_centro);
											foreach($las as $la)
											{
												if($id_lugar_atencion == $la->id)
													$selected = "selected=\"selected\"";
												else
													$selected = "";
												echo "<option value=\"$la->id\" $selected>$la->descripcion</option>";	
											}
										?>
									</select>
									<span class="label3"> / </span>
									<span class="label2">Cliente: </span>
									<input type="text" class="cliente" value="<?php echo $cv->cliente; ?>" readonly="readonly"/>
									<span class="lbl_mensaje_cambiar_cliente">(click para cambiar al cliente)</span>
									<div class="div_total">
										<span class="mensaje_total">Total: </span>
										<span class="cuenta_venta_total"> S/.<?php echo number_format($monto_total_mn, 2);?></span>
									</div>					
								</div>
								<div class="operacion_cuenta">
									<span class="label">Operación:</span>
									<select class="id_operacion_cuenta texto_3">
										<option value="0">Seleccione...</option>
										<option value="1">Cerrar Cuenta</option>
										<option value="2">Cancelar Cuenta</option>
									</select>
								</div>
								
								<div class="div_agregar_item">
									<div class="div_lista_productos" align="center" >Lista de Productos</div>	
									<table>
										<tr>
										<?php
										$readonly = "";
										if(!$permiso_ingresar_ventas_otro_turno->isOK || $ta->id_usuario = $id_usuario)
											$readonly = "readonly='readonly'";
										?>	
											<td><span class="mensaje2">Ingrese Nro. Serie: </span></td>
											<td><input class="nro_serie texto_3" type="text" value="" /></td>
											<td><input class="agregar_serie texto_2" type="button" value="Agregar Serie"<?php echo $readonly;?>/></td>
											<td width="50px"></td>
											<td><span class="mensaje2">Ingrese Descripcion: </span></td>
											<td><input class="descripcion_corta texto_4" type="text" value="" /></td>
											<td><input class="agregar_descripcion texto_2" type="button" value="Agregar Item"<?php echo $readonly;?>/></td>
										</tr>
									</table>
								</div>
								<div class="div_tabla_lista_items">
									<table class="tabla_lista_items" id="tabla_lista_items_<?php echo $cv->id;?>">
										<thead>
											<th width=20px>#</th>
											<th width=120px>Nro Serie</th>
											<th width=280px>Producto</th>								
											<th width=100px>Marca</th>
											<th width=180px>Precio/Promo</th>
											<th width=100px>Precio (S/.)</th>
											<th width=70px>Cantidad</th>
											<th width=100px>Total (S/.)</th>
											<th width=120px>Operación</th>
											
										</thead>
										<tbody>
										<?php
										
										$items = $cvBLO->ListarItemsXIdCuentaVenta($cv->id);							
										if(!is_null($items))
										{
											if(count($items) > 0)
											{
												foreach($items as $cvi)
												{
													$precio_total_mn = "S/. ".number_format($cvi->precio_total_mn, 2);
													
													$lista_precios = $proBLO->ListarPreciosXIdProducto($cvi->id_producto, $id_centro);
													
													if(!is_null($lista_precios))
													{
														//$opciones_precios = "<option value=\"0\">Seleccione...</option>";
														$opciones_precios = "";
														
														foreach($lista_precios as $px)
														{
															$selected = "";
															
															//if($px->id_producto_precio_tipo == $cvi->id_producto_precio_tipo)
															if($px->id == $cvi->id_producto_precio)
																$selected = "selected='selected'";
															
															if($px->codigo != "")
																$opciones_precios = $opciones_precios."<option value=\"$px->id\" $selected>".strtoupper($px->codigo)."</option>";
															else
																$opciones_precios = $opciones_precios."<option value=\"$px->id\" $selected>".strtoupper($px->producto_precio_tipo)."</option>";																
														}														
													}
													
													$precios_html = "<select class=\"id_producto_precio texto_2_5\">";
													$precios_html = $precios_html.$opciones_precios;
													$precios_html = $precios_html."</select>";
													
													$cantidad_html = "<select class=\"cantidad texto_1_5\" id=\"cantidad_$i\" name=\"cantidad_$i\" >\n";
													//$cantidad_html = $cantidad_html."<option value=\"0\">Seleccione...</option>\n";
													$opciones_cantidad = explode(",", $cvi->opcion_cantidad);
													
													$total = $cvi->precio_total_mn * $cvi->cantidad;
													$total_mn = "S/. ".number_format($total, 2);
													
													foreach($opciones_cantidad as $o)
													{
														$opcion = explode(":", $o);
														
														$selected = "";
														if(number_format($cvi->cantidad, 2) == number_format($opcion[0], 2))
															$selected = "selected=\"selected\"";
														
														$cantidad = number_format($opcion[0], 2);
														
														$cantidad_html = $cantidad_html."<option value=\"$cantidad\" $selected>$opcion[1]</option>\n";
													}
													$cantidad_html = $cantidad_html."</select>";
													
													
												?>
												<tr class="fila_item">
													<td align="center">
														<div class="div_nro_fila">
														<?php echo $i; ?>
														</div>
														<input type="hidden" class="id_cuenta_venta_item" value="<?php echo $cvi->id;?>" /></td>										
													<td align="center"><?php echo strtoupper($cvi->nro_serie);?></td>
													<td><b><?php echo strtoupper($cvi->descripcion_corta);?></b></td>										
													<td align="center"><?php echo strtoupper($cvi->marca);?></td>
													<td align="center">
														<input class="id_producto_precio_1" type="hidden" value="<?php echo $cvi->id_producto_precio;?>" />
														<?php echo $precios_html;?></td>
													<td align="center">														
														<div class="div_precio_total_mn">
															<?php echo $precio_total_mn;?>
														</div>
													</td>
													<td align="center">
														<input class="cantidad_1" type="hidden" value="<?php echo number_format($cvi->cantidad, 2);?>" />
														<?php echo $cantidad_html;?>
													</td>
													<td align="center">
														<div class="div_item_monto_total_mn">
															<?php echo $total_mn;?>
														</div>
														<input type="hidden" class="item_monto_total_mn" value="<?php echo $total; ?>" />
														</td>
													<td align="center">
														<select class="operacion_item texto_2">
															<option value="0">Seleccionar...</option>
															<option value="1">Anular</option>
														</select>
													</td>
													
												</tr>											
											<?php
												$i++;
												}
											}
											else
												echo "<tr class=\"sin_info\"><td colspan=9>No se han agregado Items a la Cuenta.</td></tr>";
										}
										else
											echo "<tr><td>Error</td></tr>";
											?>								
										</tbody>
									</table>
								</div>
							</div>	
					
					
						<?php
						}
					}
				}
				?>
				</div>
			<?php
			}
			?>
			</div>
			<?php		
				include ('../clientes/buscar.php');
			?>
		</form>
	</body>
</html>

