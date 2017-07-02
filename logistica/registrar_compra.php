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
$enlace_procesar_comprobante_pago = "../procesar_comprobante_pago.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";
$enlace_query_producto = "../procesar_producto.php?usr_key=$usr_key&opcion_key=$opcion_key&id_centro=$id_centro";
$enlace_query_compra = "../procesar_compra.php?usr_key=$usr_key&opcion_key=$opcion_key&id_centro=$id_centro";
$enlace_proveedor = "../procesar_proveedor.php?usr_key=$usr_key&opcion_key=$opcion_key&id_centro=$id_centro";

$opcBLO = new OpcionBLO();
$almBLO = new AlmacenBLO();
$comBLO = new CompraBLO($id_centro);
$cpBLO = new ComprobantePagoBLO();
$tdBLO = new TipoDocumentoBLO();
$caBLO = new CajaBLO();
$traBLO = new TransaccionBLO();

$lista_tm_usuario = $traBLO->ListarMotivoTransaccionesXIdUsuario($id_usuario);

function getGUID(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	$charid = strtoupper(md5(uniqid(rand(), true)));
	$hyphen = chr(45);// "-"
	$uuid = substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12);        
	return $uuid;    
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
		
		<style type="text/css">
			body { background-color: #F1F1F1; }
			#div_main {  width: 1100px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
				border-radius: 10px 10px 10px 10px; font-family: Helvetica; font-size: 12px; }					
			#titulo { font-weight: bold; font-size: 14px; color: #0099CC; margin-bottom: 20px; }
			
			.drop_down_registro{
				text-transform: uppercase;
				width: 244px;
				font-size: 12px;
				padding-left: 3px;	
			}
			
			.div_compra fieldset, .div_table_detalle fieldset{ 
				border: none;
				margin: 5px;
				padding: 10px;
				margin: 0 auto;
				overflow: hidden;
			}
			
			.div_compra legend, #fs_totales legend {
				font-weight: bold;
				font-size: 12px;
			}
			
			#fs_general{
				margin: 0 auto;
				overflow: hidden;
				width: 250px;
			}
			
			.div_compra ul, #fs_totales ul, #fs_general ul{ 
				list-style: none; 
				margin: 0px;
				padding:0px;
			}
			
			.div_compra ul li, #fs_totales ul li{
				float: left;				
				padding-left: 5px;
				padding-right: 15px;
				margin-right: 1px;
				border-top:dotted 1px #0099CC;
				/*border-bottom:dotted 1px #0099CC;*/
				padding-top:3px;
				padding-bottom:3px;
				height: 19px;				
			}
			
			#fs_general ul li
			{
				float: left;
				margin-right: 20px;
			} 
			
			
			
			.div_compra label, #fs_totales label {
				float: left;
				font-size: 12px;
				font-weight: bold;
				margin-right: 5px;
				margin-top: 3px;
			}
			
			.lbl_etiqueta{
				font-size: 12px;
				font-weight: bold;
				margin-right: 5px;
				margin-top: 3px;
			}
			
			#nro_serie{
				width: 40px;
				text-align: center;
			}
			
			#nro_comprobante{
				width:70px;
				text-align: center;
			}
			
			#fecha_str{
				width:70px;
				text-align: center;
				margin-right: 57px;
				margin-left: 4px;
			}
			
			#proveedor{
				text-align: center;
				margin-right: 5px;
				width: 440px;
			}
			
			#id_proveedor_tipo_documento{
				margin-right: 5px;
			}
			
			#lbl_beneficiario{
				margin-right: 10px;
			}
			
			
			#btn_buscar_proveedor{
				width: 13px;
				height: 13px;
				padding-top: 2px;				
			}
			
			#btn_buscar_proveedor:hover{
				cursor:pointer;
			}
	
			.ui-menu-item-wrapper{
				font-size: 12px;
			}
			
			#div_detalle{
				margin: 0 auto; 
				overflow:hidden; 
				width: 1030px; 
				 
			}
			.div_table_detalle{
				display: none;
			}
			
			.table_detalle{
				border-collapse: collapse;	
			}
			
			.table_detalle thead {
			    /*background-color: #0099CC;*/
			    color: #0099CC;
			    padding: 5px 0px 5px 0px;
			    border: solid 2px #9C9C9C;
			}
			
			.table_detalle th{
				padding: 3px 0px 3px 0px;
			}
			
			
			.table_detalle tr:nth-child(even){
				background-color: #f3f3f3;
			}
			
			.detalle_producto, .detalle_pack_item{
				width: 310px; text-transform: uppercase;
			}
			
			.detalle_columna_producto
			{
				vertical-align: middle;
			}
			
			.detalle_cantidad, .detalle_cantidadxunidad, .detalle_precio_unitario_mn, .detalle_total_mn, .monto_total{
				width: 65px;
				text-align: center;
			}
			
			.monto_total{
				font-weight: bold;
			}
			
			.detalle_agregar{
				font-weight: bold;
			}
			
			.detalle_texto{
				text-align: center;
			}
			
			.nro_fila{
				vertical-align: middle;
			}
	
		</style>
		
		
		<!--script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script-->
		
		<script language="JavaScript" src="../js/jquery-1.12.3.js"></script>
		<script src="../calendario/jquery.ui.core.js"></script>      
		<script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
		
		<script language="JavaScript" src="../js/jquery-ui-1.12.1.js"></script>
		
		<!--script language="JavaScript" src="../js/datatables.min.js"></script-->
		
		<!--script language="JavaScript" src="../js/jquery-ui-1.11.4.min.js"></script-->
		
		<script language="JavaScript" src="../js/jquery.cascadingdropdown.js"></script>
		<!--script type="text/javascript" src="../js/dataTables.buttons.js"></script-->
		<script type="text/javascript" src="../js/jquery.browser.js"></script>
		
		<!--script src="../js/jquery.autocomplete-min.js"></script-->
		<!--script src="../js/jquery.validate.min.js"></script-->		
		
		
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		
		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css?a=1"/>
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
 		
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key . "&id_centro=$id_centro"; ?>";
		}
		
		function guid() {
			function s4() {
				return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
			}
		  	var x = s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
		  	return x.toUpperCase();
		}
		
		$(function()
		{
			$fecha = new Date();
			
			$('#fecha_str').datepicker({
				
				dateFormat: 'dd-mm-yy', 
				altField: '#fecha', 
				altFormat: 'yy-mm-dd'
				});
			$("#fecha_str").datepicker("setDate", $fecha);
			
			$(".input_element").attr("disabled", "disabled");
			
			$("#div_compra").cascadingDropdown({
				selectBoxes: [
				{
					selector:'#id_compra_tipo_str'
				},
				{
					selector:'#id_comprobante_pago_tipo',
						requires: ['#id_compra_tipo_str'],
						source: function(request, response) {
							var id_compra_tipo_str = $("#id_compra_tipo_str").val();
							var id_compra_tipo_arr = id_compra_tipo_str.split(":");
							var $id_compra_tipo, id_transaccion_motivo, $cod_transaccion_grupo;
							
							if(id_compra_tipo_arr[0] == "ct")
							{
								id_compra_tipo = id_compra_tipo_arr[1];
								id_transaccion_motivo = id_compra_tipo_arr[2];
								$cod_transaccion_grupo = id_compra_tipo_arr[3];
							}
							if(id_compra_tipo_arr[0] == "tm")
							{
								id_transaccion_motivo = id_compra_tipo_arr[1];
								$cod_transaccion_grupo = id_compra_tipo_arr[2];
							}
							$("#cod_transaccion_grupo").val($cod_transaccion_grupo)
								
							var data = {
								operacion: 'query_comprobante_pago_tipo_transaccion_motivo',
								id_transaccion_motivo: id_transaccion_motivo
							};
							
							if(id_transaccion_motivo > 0)
							{
								
								$.getJSON('<?php echo $enlace_procesar_comprobante_pago;?>', data, function(data) {
									var selectOnlyOption = data.length <= 1;
									response($.map(data, function(item, index) {
										return {
											label: item.comprobante_pago_tipo,
											value: item.id_comprobante_pago_tipo
										};
									}));
								});	
								
							}
							else{
								$("#id_comprobante_pago_tipo").empty().append('<option selected="selected" value=0>Seleccione...</option>');
							}
								
						}
				}]
			});
			
			$("#id_compra_tipo_str").on("change",function()
			{
				var id_compra_tipo_str = $(this).val();
				var id_compra_tipo_arr = id_compra_tipo_str.split(":");
				
				if(id_compra_tipo_arr[0] == "ct")
					cod_transaccion_grupo = id_compra_tipo_arr[3];
				if(id_compra_tipo_arr[0] == "tm")
					cod_transaccion_grupo = id_compra_tipo_arr[2];
					
				$(".input_element").attr("disabled","disabled");
				$("#nro_serie").val("000");
				$("#nro_comprobante").val("");
				$("." + cod_transaccion_grupo).removeAttr("disabled");
				
				if($(".div_table_detalle").hasClass(cod_transaccion_grupo))
				{
					
					$div_table_detalle = $(".div_table_detalle").clone();
					$div_table_detalle.addClass("new_table");
					$div_table_detalle.removeClass("original");
					
					$("#div_detalle").append($div_table_detalle);
					
					$table = $("#div_detalle").find(".new_table");
					
					$table.show(700);
					
				}
									
			});
			
			
			
			
			$("#proveedor").autocomplete({
				source: function (request, response) {					
					$url_query_proveedor = "<?php echo $enlace_proveedor;?>";
					$id_tipo_documento = $("#id_proveedor_tipo_documento").val();
					$url_query_proveedor += "&id_tipo_documento=" + $id_tipo_documento;
					$url_query_proveedor += "&operacion=query2&nombres=";					
					$.ajax({
						url: $url_query_proveedor + request.term,
						dataType: "json",
						type: "GET",
						success: function (data) {
							response($.map( data, function(item) {
								return{
									value: item.id,
									label: item.nro_documento + " - " + item.razon_social,
									razon_social: item.razon_social,
									nro_documento: item.nro_documento													
								}
							}))
						}
					});
				},
				select: function(event, ui) {
					event.preventDefault();					
					$("#id_proveedor").val(ui.item.value);
					$("#proveedor").val(ui.item.nro_documento + " - " + ui.item.razon_social);
					$("#proveedor_aux").val(ui.item.nro_documento + "-" + ui.item.razon_social);
					//$("#nombre_cliente").val(ui.item.nombres+" "+ui.item.apellidos);
				},/*,
					    focus: function(event, ui) {
					        event.preventDefault();
					        $("#descripcion").val(ui.item.label);
					    },*/
				change: function(event, ui)
				{
					if($("#proveedor").val() != $("#proveedor_aux").val())
						$("#id_proveedor").val(0);
				},
				minLength: 2
			});
			
			
			$("#div_detalle").on("keydown", ".detalle_producto", function()			
			{
				$url = "<?php echo $enlace_query_producto;?>&operacion=query_producto&flag_venta=1&descripcion_corta=";
				$detalle_producto = $(this);
				
				$detalle_id_producto = $(this).parent().find(".detalle_id_producto");
				$detalle_producto_aux = $(this).parent().find(".detalle_producto_aux");
				$detalle_flag_pack = $(this).parent().find(".detalle_flag_pack");
				
						
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
											flag_pack: item.flag_pack
										}
									})
								)
							}
						});
					},
					select: function(event, ui)
					{
						event.preventDefault();
						$detalle_id_producto.val(ui.item.value);
						$detalle_producto.val(ui.item.label);
						$detalle_producto_aux.val(ui.item.label);
						$detalle_flag_pack.val(ui.item.flag_pack).trigger("change");
						
					},
					/*focus: function(event, ui) {
						event.preventDefault();
						$descripcion_corta.val(ui.item.label);
					},*/
					change: function(event, ui)
					{
						if($detalle_producto.val() != $detalle_producto_aux.val())
					   		$detalle_id_producto.val(0);
					},
					minLength: 3
				});
			});
			
			$("#div_detalle").on("change", ".detalle_flag_pack", function()
			{
				$url = "<?php echo $enlace_query_producto;?>";
				$cod_transaccion_grupo = $("#cod_transaccion_grupo").val();
				$tr = $("#div_detalle").find("."+ $cod_transaccion_grupo + ".original").find(".table_detalle tbody tr:first").clone(); //Fila Clonada a Insertar
				
				$fila = $(this).closest("tr.detalle_fila"); 
				
				//$celda_cantidadxunidad = $(this).parent().parent().find(".detalle_cantidadxunidad");
				$detalle_mult_fila = "";
				$id_fila = $fila.find(".detalle_id_fila").val();
				
				if($(this).val() == 1) //Flag Pack
				{
					$id_pack = $fila.find(".detalle_id_producto").val();
					$fila.find(".detalle_cantidadxunidad").removeAttr("disabled");
					
					var data = {
						operacion: "query_producto_pack",
						id_producto_pack: $id_pack
					};
					
					$.post($url, data, function(response) {
						
						$pack_item_cantidad = response.length;
						
						if($pack_item_cantidad > 1)
						{
							$fila.find(".detalle_columna_id").attr("rowspan", $pack_item_cantidad);
							$fila.find(".detalle_columna_producto").attr("rowspan", $pack_item_cantidad);
							$fila.find(".detalle_btn_operacion").attr("rowspan", $pack_item_cantidad);
							
						}
						
						for(i = 0; i < $pack_item_cantidad; i++ )
						{
							$detalle_mult_fila = guid();
							pack_item = response[i];
							
							if(i == 0)
							{
								$fila.find(".detalle_pack_item_id_producto").val(pack_item.id_producto);
								$fila.find(".detalle_pack_item").val(pack_item.producto_descripcion_corta + " [CANTIDAD: " + pack_item.cantidad + "]");
							}
							else
							{
								
								$tr_aux = $tr.clone();
								$tr_aux.removeClass();
								$tr_aux.addClass($detalle_mult_fila);
								$tr_aux.addClass($id_fila);
								$tr_aux.find(".detalle_columna_id").remove();
								$tr_aux.find(".detalle_columna_producto").remove();
								$tr_aux.find(".detalle_pack_item_id_producto").val(pack_item.id_producto);
								$tr_aux.find(".detalle_pack_item").val(pack_item.producto_descripcion_corta + " [CANTIDAD: " + pack_item.cantidad + "]");
								$tr_aux.find(".detalle_cantidadxunidad").attr("disabled", "disabled");
								$tr_aux.find(".detalle_btn_operacion").remove();
								$fila.after($tr_aux);
							}
						}
						
						$fila.find(".detalle_mult_ult_fila").val($detalle_mult_fila);
												
					}, 'json');
				}
				if($(this).val() == 0)
				{
					$fila.find(".detalle_cantidadxunidad").attr("disabled", "disabled");
					$fila.find(".detalle_cantidadxunidad").val("0");
				}
				
			});
			
			$("#div_detalle").on("change", ".detalle_calculable", function()
			{
				$detalle_cantidad = $(this).parent().parent().find(".detalle_cantidad");
				$detalle_cantidadxunidad = $(this).parent().parent().find(".detalle_cantidadxunidad");
				$detalle_precio_unitario_mn = $(this).parent().parent().find(".detalle_precio_unitario_mn");
				$detalle_total_mn = $(this).parent().parent().find(".detalle_total_mn");				
				
				if($(this).hasClass("detalle_cantidad"))
					$detalle_cantidadxunidad.val(0);
					
				if($(this).hasClass("detalle_cantidadxunidad"))
					$detalle_cantidad.val(0);
				
				$cantidad = parseInt($detalle_cantidad.val()) + parseInt($detalle_cantidadxunidad.val());
				
				$total_mn = $cantidad * parseFloat($detalle_precio_unitario_mn.val());
				
				$detalle_total_mn.val($total_mn.toFixed(2)).trigger("change");
				
			});
			
			$("#div_detalle").on("click",".btn_operacion", function(){
				$cod_transaccion_grupo = $("#cod_transaccion_grupo").val();
				$tr = $("#div_detalle").find("."+ $cod_transaccion_grupo + ".original").find(".table_detalle tbody tr:first").clone(); //Fila Clonada a Insertar
				$btn_operacion = $tr.find(".detalle_btn_operacion").find(".btn_operacion").clone();
				
				$div_table_detalle = $(this).closest(".div_table_detalle");
				$fila = $(this).closest("tr.detalle_fila");
				$tabla = $(this).closest(".table_detalle");
				
				$id_fila = guid();
				$idx = guid();
				
				if($fila.find(".detalle_flag_pack").val() == 1)
				{
					$fila_ult_class = $fila.find(".detalle_mult_ult_fila").val();
					$fila_ult = $tabla.find("." + $fila_ult_class);
				}
				else
					$fila_ult = $fila;
					
				$nro_fila = $tabla.find("tbody tr.detalle_fila").length;
				$monto_total_total_mn = $div_table_detalle.find(".monto_total_total_mn");
				$total_total_mn = 0;
				
				if($(this).hasClass("detalle_agregar"))
				{
					$btn_eliminar = $fila.find(".detalle_eliminar");
					$btn_eliminar.remove();
					
					$(this).removeClass("detalle_agregar").addClass("detalle_eliminar").empty().append("-");
										
					$btn_operacion.removeClass("detalle_agregar").addClass("detalle_eliminar").empty().append("-");
					$tr.removeClass();
					$tr.addClass("detalle_fila");
					$tr.addClass($id_fila);
					$tr.addClass($idx);
					
					$tr.find(".detalle_btn_operacion").append($btn_operacion);
					$tr.find(".nro_fila").empty();
					$tr.find(".nro_fila").append($nro_fila + 1);
					$tr.find(".detalle_nro_fila").val($nro_fila + 1);
					$tr.find(".detalle_mult_1er_fila").val($idx);
					$tr.find(".detalle_mult_ult_fila").val($idx);
					$tr.find(".detalle_id_fila").val($id_fila);
					
					$fila_ult.after($tr);
				}
				else
				{
					if($(this).hasClass("detalle_eliminar"))
					{
						$tabla.find("tr." + $fila.find(".detalle_id_fila").val()).remove();
						$i = 1;
						$total_total_mn = 0;
						$tabla.find("tbody tr.detalle_fila").each(function(){
							
							$(this).find(".nro_fila").empty();
							$(this).find(".nro_fila").append($i);
							$(this).find(".detalle_nro_fila").val($i);
							$i++;
														
							$detalle_total_mn = $(this).find(".detalle_total_mn");
							$total_mn =  parseFloat($detalle_total_mn.val());
							$total_total_mn += $total_mn;					
							
						});
						
						$monto_total_total_mn.val($total_total_mn.toFixed(2));						
						$fila_1 = $tabla.find("tr.detalle_fila").last(); //Ultima fila
						$nro_fila = $tabla.find("tbody tr.detalle_fila").length;
						if($nro_fila > 1)
						{
							if($fila_1.find(".detalle_agregar").length == 0)
								$fila_1.find(".detalle_btn_operacion").find(".detalle_eliminar").before($btn_operacion);
						}
						else
						{
							if($fila_1.find(".detalle_btn_operacion").find(".detalle_agregar").length > 0)
								$fila_1.find(".detalle_btn_operacion").find(".detalle_eliminar").remove();
							else
								$fila_1.find(".detalle_btn_operacion").find(".detalle_eliminar").removeClass("detalle_eliminar").addClass("detalle_agregar").empty().append("+");
						}
						
					}
				}
			});
			
			$("#div_detalle").on("change", ".detalle_total_mn", function()
			{
				$div_table_detalle = $(this).closest(".div_table_detalle");
				$tabla = $div_table_detalle.find(".table_detalle");
				
				$monto_total_total_mn = $div_table_detalle.find(".monto_total_total_mn");
				$total_total_mn = 0;
				$tabla.find("tbody tr").each(function()
				{
					$detalle_total_mn = $(this).find(".detalle_total_mn");
					$total_mn =  parseFloat($detalle_total_mn.val());
					$total_total_mn += $total_mn;					
				});
				
				$monto_total_total_mn.val($total_total_mn.toFixed(2));
			});
			
		});
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
		<div id="div_main">
		<form id="producto" name="producto" method="post" action="<?php echo $enlace_procesar_producto;?>">			
			<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>"/>
			<input type="hidden" id="id_centro" name="id_centro" value="<?php echo $id_centro;?>"/>
			<input type="hidden" id="permiso" value=""/>
			<input type="hidden" id="nro_items" name="nro_items" value=""/>
			<input type="hidden" id="id_compra_tipo" name="id_compra_tipo" />
			<input type="hidden" id="id_transaccion_motivo" name="id_transaccion_motivo" />
			<input type="hidden" id="cod_transaccion_grupo" name="cod_transaccion_grupo" />
			<input type="hidden" id="operacion" name="operacion" />
			<div id="titulo" align="center	">REGISTRO DE COMPRAS Y PAGOS</div>
			<div id="div_compra" class="div_compra">
				<fieldset id="fs_compra">
					<legend align="left">Datos de Compra o Pagos:</legend>
					<ul>
						<li>
							<label for="fecha_str">Fecha de Compra:</label>
							<input type="text" id="fecha_str" align="center" readonly="readonly" class=""/>
							<input id="fecha" name="fecha" align="center" value="" type="hidden"/>
						</li>
						
						<li>
							
							<label for="id_compra_tipo">Tipo de Compra/Pago:</label>							
							<select name="id_compra_tipo_str"  id="id_compra_tipo_str" class="drop_down_registro"/>
								<option value="0">Seleccione...</option>
								<?php
								//echo json_encode($lista_tm_usuario);
								$lista = $comBLO->ListarTiposTodos();
								$opciones = "";
								
								if(!is_null($lista_tm_usuario) && !is_null($lista))
									foreach($lista_tm_usuario as $tm)
									{
										$tm_ok = FALSE;	
									
										foreach($lista as $ct)
										{
											if($tm->id_transaccion_motivo == $ct->id_transaccion_motivo)
											{
												$opciones = $opciones."<option value=\"ct:$ct->id:$ct->id_transaccion_motivo:$tm->cod_transaccion_grupo\">[COMPRA] $ct->descripcion</option>";
												$tm_ok = TRUE;
											}																																		
										}
										if(!$tm_ok && $tm->transaccion_factor < 0)
											$opciones = $opciones."<option value=\"tm:$tm->id_transaccion_motivo:$tm->cod_transaccion_grupo\">$tm->transaccion_motivo</option>";
									}									
								
								echo $opciones;
								?>
								
							</select>						
						</li>
						<li>
							<label for="id_comprobante_pago_tipo">Tipo Comprobante Pago:</label>
							<select name="id_comprobante_pago_tipo"  id="id_comprobante_pago_tipo" class="drop_down_registro CON PPR GOP CEQ PSR IEX"/>
								<option value="0">Seleccione...</option>								
							</select>
						</li>
						<li>
							<label for="nro_serie">Nro. Comprobante:</label>
							<input type="text" id="nro_serie" maxlength="5" value="000" class="COM PPR GOP CEQ PSR IEX DCO input_element"/>
							<span>-</span>
							<input type="text" id="nro_comprobante" maxlength="6" class="COM PPR GOP CEQ PSR IEX DCO input_element" />
						</li>
						<li>
							<label class="lbl_etiqueta" id="lbl_beneficiario">Beneficiario</label>
							<label for="id_proveedor_tipo_documento">Documento:</label>
							<select id="id_proveedor_tipo_documento" class="COM PPR GOP CEQ PSR IEX DCO input_element">
								<option value="0">Seleccione..</option>
								<option value="1">DNI</option>
								<option value="2" selected="selected">RUC</option>
							</select>
							<span class="lbl_etiqueta">Raz. Social:</span>
							<input type="hidden" id="id_proveedor" name="id_proveedor"/>
							<input type="hidden" id="proveedor_aux"/>
							<input type"text" id="proveedor" class="COM PPR GOP CEQ PSR IEX DCO input_element"/>
						</li>						
						
					</ul>
				</fieldset>
			</div>
			<?php 
			$id_fila = getGUID();
			$idx =  getGUID();
			?>
			<div id="div_detalle" class="div_detalle">
				<div class="div_table_detalle COM CEQ original">
					<table class="table_detalle" border="0" frame="hsides">
						<thead>
							<tr>
								<th width=25px>#</th>
								<th width=600px colspan="2">Producto</th>
								<th width=80px>Cantidad</th>
								<th width=80px>Cant. Unid</th>
								<th width=80px>P.Unit Total</th>
								<th width=80px>Total</th>
								<th width=60px>...</th>	
							</tr>
							
						</thead>
						<tbody>
							<tr class="detalle_fila <?php echo "$idx $id_fila";?>">
								<td align="center" class="detalle_columna_id">
									<span class="nro_fila" align="center" >1</span>
									<input type="hidden" class="detalle_nro_fila detalle_texto" value="1"/>										
								</td>
								<td align="center" class="detalle_columna_producto">
									<input type="hidden" class="detalle_id_producto detalle_texto"/>
									<input type="hidden" class="detalle_producto_aux detalle_texto"/>
									<input type="hidden" class="detalle_flag_pack"/>
									<input type="text" align="center" class="detalle_producto detalle_texto"/>
								</td>
								<td>
									<input type="hidden" class="detalle_pack_item_id_producto detalle_texto"/>
									<input type="text" align="center" readonly="readonly" class="detalle_pack_item detalle_texto"/>																		
								</td>
								<td align="center"><input type="number" align="center" value="0" class="detalle_cantidad detalle_texto detalle_calculable"/></td>
								<td align="center"><input type="number" align="center" value="0" class="detalle_cantidadxunidad detalle_texto detalle_calculable" disabled="disabled"/></td>
								<td align="center"><input type="number" align="center" value="0.00" step="0.01" class="detalle_precio_unitario_mn detalle_texto detalle_calculable"/></td>
								<td align="center"><input type="number" align="center" value="0.00" step="0.01" class="detalle_total_mn detalle_texto"/></td>
								<td align="center" class="detalle_btn_operacion">
									<button type="button" class="detalle_agregar btn_operacion"/>+</button>
									<input type="hidden" class="detalle_mult_1er_fila" value="<?php echo $idx;?>"/>
									<input type="hidden" class="detalle_mult_ult_fila" value="<?php echo $idx;?>"/>
									<input type="hidden" class="detalle_id_fila" value="<?php echo $id_fila;?>"/>
								</td>
							</tr>
						</tbody>
					</table>
					<fieldset id="fs_totales" align="top">
					<!--legend align="left">TOTALES</legend-->
						<ul>
							<li>
								<label for="monto_neto_mn">Monto Neto:</label>
								<input type="number" align="center" step="0.01" value="0.00" class="monto_total monto_neto_total_mn"/>
							</li>
							<li>
								<label for="otros_impuestos_mn">I.S.C.:</label>
								<input type="number" align="center" step="0.01" value="0.00" class="monto_total otros_impuestos_total_mn"/>
							</li>
							<li>
								<label for="monto_impuesto_mn">I.G.V.:</label>
								<input type="number" align="center" step="0.01" value="0.00" class="monto_total monto_impuesto_total_mn"/>
							</li>
							<li>
								<label for="monto_total_mn">Monto Total:</label>
								<input type="number" align="center" step="0.01" value="0.00" class="monto_total monto_total_total_mn"/>
							</li>
							<li>
								<label for="percepciones_mn">Percepciones:</label>
								<input type="number" align="center" step="0.01" value="0.00" class="monto_total percepciones_total_mn"/>
							</li>
							<li>
								<label for="monto_total_pagar_mn">Total Pagar:</label>
								<input type="number" align="center" step="0.01" value="0.00" class="monto_total monto_pagar_total_mn"/>
							</li>
						</ul>
					</fieldset>
					<fieldset id="fs_general" align="top">
						<ul>
							<li>
								<button type="button" id="btn_guardar">Guardar Operación</button>
							</li>
							<li>
								<button type="button" id="btn_cerrar">Cancelar</button>
							</li>
						</ul>
					</fieldset>
				</div>
				
			</div>
			<br/>
		</form>
		</div>
	
	</body>
</html>


