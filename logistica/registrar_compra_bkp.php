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
include ("../clases/almacen.php");
include ("../clases/compra.php");
include ("../clases/comprobante_pago.php");
include ("../clases/tipo_documento.php");
include ("../clases/caja.php");
include ("../clases/transaccion.php");
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_compra.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";
$enlace_query_producto = "../procesar_producto.php";
$enlace_query_compra = "../procesar_compra.php?usr_key=$usr_key&opcion_key=$opcion_key&id_centro=$id_centro";
$enlace = $_SERVER['PHP_SELF']."?usr_key=$usr_key&opcion_key=$opcion_key&id_centro=$id_centro";

$opcBLO = new OpcionBLO();
$almBLO = new AlmacenBLO();
$comBLO = new CompraBLO($id_centro);
$cpBLO = new ComprobantePagoBLO();
$tdBLO = new TipoDocumentoBLO();
$caBLO = new CajaBLO();
$traBLO = new TransaccionBLO();


/*$permiso_crear_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_crear_turno, $usuario->id);
$permiso_cerrar_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_cerrar_turno, $usuario->id);*/

$lista_tm_usuario = $traBLO->ListarMotivoTransaccionesXIdUsuario($id_usuario);

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
			#div_main {  width: 1100px; border: dotted 1px #0099CC; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; 
				overflow: hidden; border-radius: 10px 10px 10px 10px; background-color: #FFFFFF; }
			.etiqueta { font-family: Helvetica; font-size: 11px; font-weight: bold; color: #585858; float: left; }
			select { font-family: Helvetica; font-size: 11px; }
			
			.dato {color: #585858; }
			.texto0_5, .cantidad, .precio_neto_unitario_mn, .impuesto_unitario_mn, .precio_total_unitario_mn, .precio_total_mn
				{ width: 45px;font-size: 11px; text-align: center; }
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
			.texto_2, .eliminar_fila_producto { width: 100px; text-align: center; font-size: 11px; }
			.texto_3 { width: 150px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_10 { width: 400px; text-align: center; font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #585858; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			#tabla_info { width: 1050px; }
			
			#div_info, #div_lista_productos { border-radius: 10px 10px 10px 10px; border: dotted 1px #0099CC; width: 1052px; padding-top: 10px; padding: 10px 10px 10px 10px; 
				margin-bottom: 20px; -moz-box-shadow: 3px 3px 4px #000; -webkit-box-shadow: 3px 3px 4px #000; box-shadow: 3px 3px 4px #585858;
				border-collapse: collapse; }
			
			#div_lista_productos { float: left; margin-left: 13px;}
				
			#div_producto { float: left; }
			
			.td_titulo { border-bottom: dotted 1px #0099CC; padding-bottom: 3px; }
			
			#div_titulo_producto { float: left; width: 1050px; }
			#tabla_producto { }
			#div_tabla_producto { width: 1050px; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; float: left; margin-bottom: 10px;  }
			
			#tabla_lista_productos { border-collapse: collapse; }
			#tabla_lista_productos thead { color: #0099CC; font-family: Helvetica; font-size: 12px; font-weight: bold; border-top: dotted 1px #0099CC;
				border-bottom: dotted 1px #0099CC; }
			#tabla_lista_productos td { font-family: Helvetica; font-size: 11px; color: #585858;}
			.ui-menu-item { font-family: Helvetica; font-size: 11px;}
			
			#tabla_lista_productos tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_productos tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }
			
			#div_operacion { margin-top: 15px; display: none; }
			
			.div_eliminar_producto { border: solid 1px #CE1212; border-radius: 10px 10px 10px 10px; width: 9px; height: 12px; color: #CE1212; 
				font-weight: bold; font-size: 10px; padding-left: 3px; float: left; }
			.div_eliminar_producto:hover { background-color: #FAF7A8; cursor: pointer; }
			
			#monto_neto_mn { font-weight: bold; }
			#monto_impuesto_mn { font-weight: bold; }
			#monto_total_mn { font-weight: bold; }
			#monto_percepcion_mn { font-weight: bold; }
			 
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		<script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script>
		<script language="JavaScript" src="../js/jquery-ui.min.js"></script>
		
		
		<link rel="stylesheet" href="../styles/jquery-ui.css" type="text/css" media="all"/>
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		
		<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script-->
		<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js"></script-->
		
		
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
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		function roundNumber(number, digits) {
            var multiple = Math.pow(10, digits);
            var rndedNum = Math.round(number * multiple) / multiple;
            return rndedNum;
        }
        
        function AgregarNroSerie()
        {
        	$nro_serie = $("#nro_serie_t").val();
        	$("#nro_serie").val($nro_serie);
        	
        	
        	if($nro_serie != "")
        	{
        		$nro_serie = "&nro_serie=" + $nro_serie;
				
				var url = "<?php echo $enlace_query_producto;?>?operacion=query" + $nro_serie;
					
				$.getJSON(url, function(data)
				{
					if(data != null)
					{
					    $.each(data, function(key, val) 
						{
							$descripcion = val.descripcion_corta;
							
							$("#id_producto").val(val.id);
							$("#marca").val(val.marca);
							$("#descripcion").val($descripcion);
							AgregarFilaProducto();
							$("#nro_serie_t").val("");
							
						});
						
					}
				});
				
        	}
        	else
        		alert("Nro de Serie Vacía");
				
        }
        
        function AgregarFilaProducto()
        {
        	$id_producto = $("#id_producto").val();
				
			if($id_producto > 0)
			{
				$descripcion = $("#descripcion").val();
				
				$marca = $("#marca").val();
				$nro_serie = $("#nro_serie").val();
				
				$nro_filas = $('#tabla_lista_productos tr').length;
				
	        	$tr = "<tr class=\"fila_producto\">";
				$tr = $tr + "<td align=\"center\" class=\"fila_id\">" + $nro_filas + "</td>";
				$tr = $tr + "<td align=\"center\">" + $nro_serie + "</td>";
				$tr = $tr + "<td class=\"fila_descripcion\">" + $descripcion;
				$tr = $tr + "<input type=\"hidden\" class=\"id_producto\" name=\"id_producto_" + $nro_filas + "\" value=\"" + $id_producto +  "\"/>"; 
				$tr = $tr + "</td>";
				$tr = $tr + "<td align=\"center\">" + $marca + "</td>";
				$tr = $tr + "<td align=\"center\"><input type=\"number\" class=\"cantidad\" value=\"0\"  onkeypress='validate(event)' tabindex=\"" + $nro_filas + "\"/></td>";
						
				$tr = $tr + "<td align=\"center\"><input type=\"text\" class=\"precio_neto_unitario_mn\" value=\"0.00\" readonly=\"readonly\" /></td>";
				$tr = $tr + "<td align=\"center\"><input type=\"text\" class=\"impuesto_unitario_mn\" readonly=\"readonly\" value=\"0.00\" /></td>";
				$tr = $tr + "<td align=\"center\"><input type=\"number\" class=\"precio_total_unitario_mn\" value=\"0.00\" onkeypress='validate(event)' tabindex=\"" + $nro_filas + "\"/></td>";
				$tr = $tr + "<td align=\"center\"><input type=\"text\" class=\"precio_total_mn\" readonly=\"readonly\"/></td>";
				//$tr = $tr + "<td><div class=\"div_eliminar_producto\">X</div></td>";
				$tr = $tr + "<td><select class=\"eliminar_fila_producto\">";
				$tr = $tr + "<option value=\"0\">Seleccione...</option>";
				$tr = $tr + "<option value=\"1\">Eliminar</option>";
				$tr = $tr + "</select></td>";
				$tr = $tr + "</tr>";
					        
				$("#tabla_lista_productos").append($tr);
					
				$("#id_producto").val(0);
				$("#descripcion").val("");
				
				if($("#div_operacion").css("display") == "none")
					$("#div_operacion").css("display", "block");
				    
				ActualizarSubTotales();    				        
			}
			else
				alert("No ha seleccionado Producto")        	
        }
        
        function ActualizarSubTotales()
        {
        	
        	$monto_neto_mn = 0;
        	$impuesto_mn = 0;
        	$monto_total_mn = 0;
        	$monto_neto_mn = 0;
        	
        	$("#tabla_lista_productos tr.fila_producto").each(function()
        	{
        		$fila = $(this);
        		
				$cantidad = $fila.find(".cantidad").val();        		
        		$precio_neto_unitario_mn = $fila.find(".precio_neto_unitario_mn").val();
        		$impuesto_unitario_mn = $fila.find(".impuesto_unitario_mn").val();
        		
        		if($cantidad == "")
        			$cantidad = 0;
        		
        		if($precio_neto_unitario_mn == "" )
        			$precio_neto_unitario_mn = 0;
        		
        		if($impuesto_unitario_mn == "" )
        			$impuesto_unitario_mn = 0;
        			
        		$cantidad = parseFloat($cantidad);
        		$precio_neto_unitario_mn = parseFloat($precio_neto_unitario_mn);
        		$impuesto_unitario_mn = parseFloat($impuesto_unitario_mn);
        		
        		$monto_neto_mn += $precio_neto_unitario_mn * $cantidad;
        		$impuesto_mn += $impuesto_unitario_mn * $cantidad;
        		
        	});
        	
        	$("#monto_neto_mn").val($monto_neto_mn.toFixed(2));
        	$("#monto_impuesto_mn").val($impuesto_mn.toFixed(2));
        	
        	ActualizarTotal();
        }
        
        function ActualizarTotal()
        {
        	$monto_percepcion_mn = $("#monto_percepcion_mn").val();
        	$monto_neto_mn = $("#monto_neto_mn").val();
        	$impuesto_mn = $("#monto_impuesto_mn").val();
        	
        	if($monto_percepcion_mn == "")
        		$monto_percepcion_mn = 0;
        	
        	if($monto_neto_mn == "")
        		$monto_neto_mn = 0;
        		
        	if($impuesto_mn == "")
        		$impuesto_mn = 0;
        	
			$monto_percepcion_mn = parseFloat($monto_percepcion_mn);
        	$monto_neto_mn = parseFloat($monto_neto_mn);
        	$impuesto_mn = parseFloat($impuesto_mn);
        	
        	$monto_total_mn = $monto_neto_mn + $impuesto_mn + $monto_percepcion_mn;
        	
        	$("#monto_total_mn").val($monto_total_mn.toFixed(2));
        	
        }
		
		$(function()
		{
			$fecha = new Date();
			
			$('#fecha_str').datepicker({
				//minDate: 0,
				dateFormat: 'dd-mm-yy', 
				altField: '#fecha', 
				altFormat: 'yy-mm-dd'
				});
			$("#fecha_str").datepicker("setDate", $fecha);
			
			$url = "<?php echo $enlace_query_producto;?>?operacion=query&descripcion_corta=";
			
			$("#descripcion").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: $url + request.term,
                        dataType: "json",
                        type: "POST",
                        success: function (data) {
                            response(
                            	$.map( data, function( item ) 
                            	{
	                            	return{
	                                value: item.id,
	                                label: item.descripcion_corta,
	                                marca: item.marca,
	                                nro_serie: item.nro_serie,
	                                descripcion_corta: item.descripcion_corta
	                                //descripcion_corta: item.descripcion_corta + " [" + item.dimension + " " + item.unidad_medida.toUpperCase() + "]"
	                            	}
                        		})
                           
                           	)
						}
                    });

                },
                select: function(event, ui) {
			        event.preventDefault();
			        $("#descripcion").val(ui.item.label);
			        $("#id_producto").val(ui.item.value);
			        $("#nro_serie").val(ui.item.nro_serie);
			        $("#marca").val(ui.item.marca);
			        $("#unidad_medida").val(ui.item.unidad_medida);
			        $("#descripcion_corta_aux").val(ui.item.label);
			    },
			    focus: function(event, ui) {
			        event.preventDefault();
			        $("#descripcion").val(ui.item.label);
			    },
			    change: function(event, ui)
			    {
			    	if($("#descripcion").val() != $("#descripcion_corta_aux").val())
			    		$("#id_producto").val(0);
			    },
                minLength: 3 
            });
            
            $("#monto_percepcion_mn").change(function()
            {
            	ActualizarTotal();
            });
			
			$("#seleccionar_proveedor").click(function()
			{
				mywindow = showModalDialog("buscar_proveedor.php", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
				
				//var cliente = $.cookie("id_clientex");
				id_proveedor = mywindow.id_proveedor;
				razon_social = mywindow.razon_social;
				id_tipo_documento = mywindow.id_tipo_documento;
				nro_documento = mywindow.nro_documento;
				
				$('#id_proveedor').val(id_proveedor);								
				$('#razon_social').val(razon_social);
				
				$("#id_tipo_documento option").each(function(i){
			        if($(this).val() == id_tipo_documento)
			        	$(this).attr("selected","selected");
			    });
				
				$('#prov_nro_documento').val(nro_documento);
			});
			
			$("#agregar_nro_serie").click(function()
			{
				AgregarNroSerie();
			});
			
			$("#agregar_descripcion").click(function()
			{
				AgregarFilaProducto();
				
			        //$("#selected-customer").val(ui.item.label);
			});
			
			$(".cantidad").live("change", function()
			{
				$fila = $(this).parent().parent();
				$cantidad = $(this).val();
				$precio_total_unitario_mn = $fila.find(".precio_total_unitario_mn").val();				
				
				if($cantidad != "" && $precio_total_unitario_mn != "")
				{
					$precio_total_unitario_mn = $fila.find(".precio_total_unitario_mn").val();
					$precio_total_unitario_mn = roundNumber(parseFloat($precio_total_unitario_mn), 2);
					
					
					$precio_neto_unitario_mn = $precio_total_unitario_mn / 1.18;
					$precio_neto_unitario_mn = roundNumber(parseFloat($precio_neto_unitario_mn), 2);										
					$impuesto_unitario_mn = $precio_total_unitario_mn - $precio_neto_unitario_mn;
					
					$fila.find(".impuesto_unitario_mn").val($impuesto_unitario_mn.toFixed(2));
					$fila.find(".precio_neto_unitario_mn").val($precio_neto_unitario_mn.toFixed(2));
					
					$precio_total_mn = roundNumber($cantidad * $precio_total_unitario_mn, 2);
					
					$fila.find(".precio_total_mn").val($precio_total_mn.toFixed(2));	
				}
				else
				{
					$fila.find(".impuesto_unitario_mn").val("");
					$fila.find(".precio_neto_unitario_mn").val("");
					$fila.find(".precio_total_mn").val("");
					
				}
				
				ActualizarSubTotales();
				
			});
			
			$(".precio_total_unitario_mn").live("change", function()
			{
				$fila = $(this).parent().parent();
				
				$precio_total_unitario_mn = $(this).val();
				$cantidad = $fila.find(".cantidad").val();				
				
				if($cantidad != "" && $precio_total_unitario_mn != "")
				{
					$precio_total_unitario_mn = $fila.find(".precio_total_unitario_mn").val();
					$precio_total_unitario_mn = roundNumber(parseFloat($precio_total_unitario_mn), 2);
					
					$precio_neto_unitario_mn = $precio_total_unitario_mn / 1.18;
					$precio_neto_unitario_mn = roundNumber(parseFloat($precio_neto_unitario_mn), 2);										
					$impuesto_unitario_mn = $precio_total_unitario_mn - $precio_neto_unitario_mn;
					
					$fila.find(".impuesto_unitario_mn").val($impuesto_unitario_mn.toFixed(2));
					$fila.find(".precio_neto_unitario_mn").val($precio_neto_unitario_mn.toFixed(2));
					
					$precio_total_mn = roundNumber($cantidad * $precio_total_unitario_mn, 2);
					
					$fila.find(".precio_total_mn").val($precio_total_mn.toFixed(2));	
				}
				else
				{
					$fila.find(".impuesto_unitario_mn").val("");
					$fila.find(".precio_neto_unitario_mn").val("");
					$fila.find(".precio_total_mn").val("");
					
				}
				
				ActualizarSubTotales();
			});
			
			//$('.eliminar_fila_producto').bind('change', function(ev)
			$(".eliminar_fila_producto").live("change", function(ev) 
			{		
				if($(this).val() == 1)
			  	{
			  		if(confirm("¿Seguro que desea Eliminar el Producto de la lista?"))
			  		{
			  			$fila = $(this).parent().parent();
			  			$fila.remove();
			  			
			  			$cont = 1;
			  			/*$("#tabla_lista_productos").find(".fila_id").each(function()
			  			{
			  				$(this).empty();
			  				$(this).append($cont);
			  				
			  				$cont++;
			  			});*/
			  			
			  			$('#tabla_lista_productos tr.fila_producto').each(function()
			  			{
			  				$fila_id = $(this).find(".fila_id");
			  				$id_producto = $(this).find(".id_producto");
			  				$precio_total_unitario_mn = $(this).find(".precio_total_unitario_mn");
			  				$cantidad = $(this).find(".cantidad");
			  				
			  				$fila_id.empty();
			  				$fila_id.append($cont);
			  				
			  				$id_producto.attr("name", "id_producto_" + $cont);
			  				$id_producto.attr("id", "id_producto_" + $cont);
			  				
			  				$precio_total_unitario_mn.attr("name", "precio_total_unitario_mn_" + $cont);
			  				$precio_total_unitario_mn.attr("id", "precio_total_unitario_mn_" + $cont);
			  				
			  				$cantidad.attr("name", "cantidad_" + $cont);
			  				$cantidad.attr("id", "cantidad_" + $cont);
			  				
			  				$cont++;			  				
			  			});
			  			
			  			$nro_filas = $('#tabla_lista_productos tr').length;
			  			
			  			if($nro_filas == 1)
							$("#div_operacion").css("display", "none");
							
						ActualizarSubTotales();
					
			  		}
			  		else
			  		{
			  			$(this).val(0);
			  			//return false;
			  		}
			  		
			  	}
			  	
			});	
			
			$("#id_compra_tipo_arr").change(function()
			{
				
				$id_compra_tipo_arr = $(this).val();
				
				$("#id_compra_tipo").val("");
				$("#id_transaccion_motivo").val("");
				$("#transaccion_motivo").val("");
				$("#id_transaccion_grupo").val("");
				
				$("#crear_compra").attr("disabled", "disabled");
				
				if($id_compra_tipo_arr != "0")
				{
					$id_compra_tipo_arr = $id_compra_tipo_arr.split(":");
					
					if($id_compra_tipo_arr.length == 4)
					{
						$id_compra_tipo = $id_compra_tipo_arr[0];
						$id_transaccion_motivo = $id_compra_tipo_arr[1];
						$transaccion_motivo = $id_compra_tipo_arr[2];
						$id_transaccion_grupo = $id_compra_tipo_arr[3];
						
						$("#id_compra_tipo").val($id_compra_tipo);
						$("#id_transaccion_motivo").val($id_transaccion_motivo);
						$("#transaccion_motivo").val($transaccion_motivo);
						$("#id_transaccion_grupo").val($id_transaccion_grupo);
						
						$("#crear_compra").removeAttr("disabled");
						
					}
					
				}
				
				
			});
			
			$("#crear_compra").click(function()
			{
				$res = true;
				
				$id_compra_tipo = $("#id_compra_tipo").val();
				$id_caja = $("#id_caja").val();
				$id_comprobante_pago_tipo = $("#id_comprobante_pago_tipo").val();
				$nro_comprobante = $("#nro_comprobante").val();
				$id_proveedor = $("#id_proveedor").val();
				$id_tipo_documento = $("#id_tipo_documento").val();
				$prov_nro_documento = $("#prov_nro_documento").val();
				$monto_percepcion_mn = $("#monto_percepcion_mn").val();
				
				$nro_items = $('#tabla_lista_productos tr.fila_producto').length;
				
				$cont = 1;
				
			  	$('#tabla_lista_productos tr.fila_producto').each(function()
			  	{
			  		$precio_total_unitario_mn = $(this).find(".precio_total_unitario_mn");
			  		$cantidad = $(this).find(".cantidad");
			  				
			  		$precio_total_unitario_mn.attr("name", "precio_total_unitario_mn_" + $cont);
			  		$precio_total_unitario_mn.attr("id", "precio_total_unitario_mn_" + $cont);
			  			
			  		$cantidad.attr("name", "cantidad_" + $cont);
			  		$cantidad.attr("id", "cantidad_" + $cont);
			  				
			  		$cont++;			  				
			  	});
				
				$("#nro_items").val($nro_items);
				
				$msg = "Error(es) Encontrado(s):\n\n"
				
				if($id_compra_tipo == 0)
				{
					$msg = $msg + "+ No ha seleccionado Tipo de Compra.";
					$res = false;
				}
					
				if($id_caja == 0)
				{
					$msg = $msg + "+ No ha seleccionado Caja de Origen.";
					$res = false;
				}
				
				if($id_comprobante_pago_tipo == 0)
				{
					$msg = $msg + "+ No ha seleccionado Tipo de Comprobante de Pago.";
					$res = false;
				}
				
				if($nro_comprobante == "")
				{
					$msg = $msg + "+ No ha ingresado Nro. de Comprobante.";
					$res = false;
				}
				if($id_proveedor == 0)
				{
					$msg = $msg + "+ No ha seleccionado Proveedor.";
					$res = false;
				}
				if($id_tipo_documento == 0)
				{
					$msg = $msg + "+ No ha seleccionado Tipo de Documento para el Proveedor.";
					$res = false;
				}
				if($prov_nro_documento == 0)
				{
					$msg = $msg + "+ No ha ingresado el Nro. de Documento para el Proveedor.";
					$res = false;
				}
				
				if($monto_percepcion_mn == "")
				{
					$msg = $msg + "+ No ha ingresado el Monto de la Percepción.";
					$res = false;
				}
				
				if($nro_items == 0)
				{
					$msg = $msg + "+ No ha ingresado ningún Item.";
					$res = false;
				}
				
				if($res)
				{
					$("#operacion").val("crear");
					$("#compra").submit();
				}
				else
					alert($msg);
			});
			
			
			
		});
			
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
	<form id="compra" name="compra" method="post" action="<?php echo $enlace_procesar; ?>">
		<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>"/>
		<input type="hidden" id="id_centro" name="id_centro" value="<?php echo $id_centro;?>"/>
		<input type="hidden" id="permiso" value=""/>
		<input type="hidden" id="nro_items" name="nro_items" value=""/>
		<input type="hidden" id="id_compra_tipo" name="id_compra_tipo" />
		<input type="hidden" id="id_transaccion_motivo" name="id_transaccion_motivo" />
		<input type="hidden" id="id_transaccion_grupo" name="id_transaccion_grupo" />
		<input type="hidden" id="operacion" name="operacion" />
		
		
		<div id="div_info" align="center">
			<table id="tabla_info" >
				<tr><td colspan="8" align="center" class="td_titulo"><span class="titulo_1">REGISTRO DE COMPRAS</span></td></tr>
				<tr height="20px"><td colspan="8" class="td_titulo"></td></tr>
				<tr>
					<td width="90px" class="td_titulo"><div class="etiqueta">Tipo Compra: </div></td>
					<td class="td_titulo">
						<select id="id_compra_tipo_arr" class="dato">
							<option value="0">Seleccione...</option>
							<?php
							
							$lista = $comBLO->ListarTiposTodos();
							
							if(!is_null($lista_tm_usuario) && !is_null($lista))
							{
								foreach($lista_tm_usuario as $tm)
								{
									foreach($lista as $ct)
										if($tm->id_transaccion_motivo == $ct->id_transaccion_motivo)
											echo "<option value=\"$ct->id:$ct->id_transaccion_motivo:".strtoupper($ct->transaccion_motivo).":$ct->id_transaccion_grupo\">$ct->descripcion</option>";
								}
								
							}
							
							
								
							?>
						</select>
					</td>
					<td width="30px" class="td_titulo"></td>
					<td width="120px" class="td_titulo"><div class="etiqueta">Motivo Transacción: </div></td>
					<td class="td_titulo"><input class="texto_3 dato" id="transaccion_motivo" align="center" readonly='readonly'/></td>
					<td width="50px" class="td_titulo"></td>
					<td width="150px" class="td_titulo"><div class="etiqueta">Fecha: </div></td>
					<td width="80px" class="td_titulo">
						<input class="texto_2 dato" id="fecha_str"align="center" readonly='readonly'/>
						<input id="fecha" name="fecha" align="center" value="" type="hidden"/>
						</td>
				</tr>
				<tr>
					<td class="td_titulo"><div class="etiqueta">Caja Origen: </div></td>
					<td class="td_titulo">
						<select id="id_caja" name="id_caja" class="texto_3 dato">
							<option value="0">Seleccione...</option>
							<?php
							
							$lista = $caBLO->ListarCajaHabilitadaSalidaXIdUsuario($id_usuario, $id_centro);
							if(!is_null($lista))
								foreach($lista as $ca)
									echo "<option value=\"$ca->id_caja\">$ca->caja</option>";
							?>
						</select>
					</td>
					<td class="td_titulo"></td>
					<td class="td_titulo"><div class="etiqueta">Tipo Comprobante: </div></td>
					<td class="td_titulo">
						<select id="id_comprobante_pago_tipo" name="id_comprobante_pago_tipo" class="dato" style="width: 180px;">
							<option value="0">Seleccione...</option>
							<?php
							$lista = $cpBLO->ListarTiposTodos();
							if(!is_null($lista))
							{
								if(count($lista) > 0)
								{
									foreach($lista as $ct)
										echo "<option value=\"$ct->id\">$ct->descripcion_corta</option>";	
								}
								else 
									echo "<option value=\"0\">No tiene cajas disponibles</option>";
								
							}
							?>
						</select>
					</td>
					<td class="td_titulo"></td>
					<td class="td_titulo"><div class="etiqueta">Nro. Comprobante: </div></td>
					<td class="td_titulo"><input class="texto_3 dato" id="nro_comprobante" name="nro_comprobante" align="center"/></td>
					
				</tr>
				<tr>
					<td class="td_titulo"><div class="etiqueta">Proveedor: </div></td>
					<td class="td_titulo">
						<input type="hidden" id="id_proveedor" name="id_proveedor" />
						<input class="texto_3 dato" id="razon_social" name="razon_social" align="center" readonly='readonly'/>
						<input type="button" style="font-size:11px;" value="..." id="seleccionar_proveedor"/>
					</td>
					<td class="td_titulo"></td>
					<td class="td_titulo"><div class="etiqueta">Prov.Tipo Doc. : </div></td>
					<td class="td_titulo">
						<select id="id_tipo_documento" name="id_tipo_documento" class="dato">
							<option value="0">Seleccione...</option>
							<?php
							
							$lista = $tdBLO->ListarTodos();
							if(!is_null($lista))
								foreach($lista as $td)
									echo "<option value=\"$td->id\">$td->descripcion</option>";
							?>
						</select>
					</td>
					<td class="td_titulo"></td>
					<td class="td_titulo"><div class="etiqueta">Prov. Nro Doc. : </div></td>
					<td class="td_titulo">
						<input class="texto_2 dato" id="prov_nro_documento" name="prov_nro_documento" align="center"/>
					</td>
					
				</tr>
				
				<tr>
					<td class="td_titulo"><div class="etiqueta">M.Neto S/.: </div></td>
					<td class="td_titulo"><input class="texto_2 dato" id="monto_neto_mn" name="monto_neto_mn" align="center" readonly='readonly' value="0.00"/></td>
					<td class="td_titulo"></td>
					<td class="td_titulo"><div class="etiqueta">IGV S/.: </div></td>
					<td class="td_titulo"><input class="texto_2 dato" id="monto_impuesto_mn" name="monto_impuesto_mn" align="center" readonly='readonly'  value="0.00" /></td>
					<td class="td_titulo"></td>
					<td class="td_titulo"><div class="etiqueta">Percepcion S/.: </div></td>
					<td class="td_titulo"><input type="number" class="texto_2 dato" id="monto_percepcion_mn" name="monto_percepcion_mn" align="center" value="0.00" onkeypress='validate(event)'/></td>
					
					
				</tr>
				<tr>
					<td class="td_titulo"><div class="etiqueta">M.Total S/.: </div></td>
					<td class="td_titulo"><input class="texto_2 dato" id="monto_total_mn" name="monto_total_mn" align="center" readonly='readonly' value="0.00" /></td>
					
					<td class="td_titulo" colspan="6"></td>
				</tr>
			</table>
		</div>
		<div id="div_lista_productos">
			<div id="div_producto">
				<input id="id_producto" name="id_producto" type="hidden" value="0"/>
				<input id="marca" name="marca" type="hidden" />
				<input id="unidad_medida" name="unidad_medida" type="hidden" />
				<input id="nro_serie" name="nro_serie" type="hidden" />
				<input type="hidden" name="descripcion_corta_aux" id="descripcion_corta_aux" value="" />
				<div id="div_titulo_producto" align="left">
					<span class="titulo_2" id="titulo_producto">Ingrese Datos del Producto o Insumo a la Lista de la Compra: </span>
				</div>
				<div id="div_tabla_producto">
					<table id="tabla_producto">
						<tr>
							<td><div class="etiqueta">Ingrese Nro. Serie: </div></td>
							<td><input class="texto_3" id="nro_serie_t" name="nro_serie_t" align="center"/></td>
							<td width="80px"><input type="button" class="texto_1_5" value="Agregar" id="agregar_nro_serie" /></td>
							<td><div class="etiqueta">Ingrese Descripción: </div></td>
							<td><input class="texto_10" id="descripcion" name="descripcion" align="center"/></td>
							<td width="80px"><input type="button" class="texto_1_5" value="Agregar" id="agregar_descripcion" /></td>
						</tr>
					</table>	
				</div>
				
			</div>
			<div id="div_tabla_lista_productos">
				<table id="tabla_lista_productos">
					<thead>
						<th width=20px>#</th>
						<th width=100px>Nro Serie</th>
						<th width=220px>Producto</th>
						<th width=130px>Marca</th>
						<th width=100px>Cantidad</th>
						<th width=100px>P.Neto Unit.S/.</th>
						<th width=95px>Impto Unit S/.</th>
						<th width=100px>P. Total Unit. S/.</th>
						<th width=63px>P. Total S/.</th>
						<th wdith=110px>Operación</th>
					</thead>
				</table>
				<div id="div_operacion">
					
					<input id="crear_compra" value="Crear Compra" class="texto_2" alt="Crear Compra" type="button" />
				</div>
			</div>
		</div>
	</form>
	</div>
	
	</body>
</html>