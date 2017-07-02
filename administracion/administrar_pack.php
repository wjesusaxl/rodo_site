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
include ("../clases/producto.php");
include ("../clases/anuncio.php");

$enlace_procesar_producto = "../procesar_producto.php?id_centro=$id_centro&opcion_original_key=$opcion_key&usr_key=$usr_key";
//$enlace_procesar = "../procesar_producto.php?id_centro=$id_centro";

$proBLO = new ProductoBLO();
$opBLO = new OpcionBLO();

$opcion_administrar_pack = "4X36UO5V";
$opcion_modificar_producto = "LZQ3C801";

$permiso_crear_productos = $opBLO->ValidarOpcionXIdUsuario($opcion_crear_productos, $id_usuario, $id_centro);
$permiso_modificar_productos = $opBLO->ValidarOpcionXIdUsuario($opcion_modificar_producto, $id_usuario, $id_centro);

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
			
			/*
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
			.texto_2 { width: 100px; text-align: center; font-size: 11px; }
			.texto_3 { width: 150px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_10 { width: 400px; text-align: center; font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #585858; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			.etiqueta { font-size: 11px; font-weight: bold; color: #585858; float: left; }
			
			#div_opcion_general { float: right; margin-right: 20px; width: 1050px; margin-bottom: 20px;  }
			
			
			#id_opcion_general { margin-left: 10px; }
			#tabla_ingreso { border-collapse: collapse; }
			#tabla_ingreso td { border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; padding-bottom: 3px; }
			
			#tabla_opcion_cantidad td { border: none; }
			#tabla_opcion_cantidad th { font-size: 12px; color: #0099CC; }
			
			#div_tabla_resultados { margin-top: 20px; border-collapse: collapse; display: none;  }
			#tabla_resultados th { font-size: 12px; color: #0099CC; border-bottom: dotted 1px #0099CC; border-top: dotted 1px #0099CC; }
			#tabla_resultados td { font-size: 11px; color: #585858;}
			#tabla_resultados tr:nth-child(even) { background-color:#DAF1F7; }
			#tabla_resultados tr:nth-child(odd) { background-color:#FFFFFF; }
			#tabla_resultados tbody tr:not(:first-child):hover { background-color: #F8FEA9; }
			
			#tabla_resultados tbody tr:hover { background-color: #F8FEA9; }

			#tabla_resultados { border-collapse: collapse; }	*/
			
			#table_resultados{
				display: none;
				padding-right: 10px;
				padding-left: 10px;
				/*width: 400px;*/
				
			}
			
			#table_resultados tr{
				height: 20px;
			}
			
			#table_resultados tbody tr:hover, #table_resultados.display tbody tr:hover>.sorting_1, #table_resultados tbody tr.selected:hover,
			#table_resultados.display tbody tr:hover>.sorting_2, #table_resultados.display tbody tr:hover>.sorting_3, #table_resultados.display tbody tr:hover>.sorting_4
			{
				background-color: #0099CC;
				color: #FFFFFF;
			}
			
			#table_resultados td, #table_pack_detalle_resultados td, #table_resultados_productos_nuevos td{
				padding:0px 2px;
			}
			
			#table_resultados_wrapper
			{
				padding-right: 10px;
				padding-left: 10px;
				display: none;
			}
			
			#table_resultados_productos_nuevos_wrapper{
				display: none;
			}
			
			#table_resultados tbody tr.selected, #table_resultados tbody tr.selected td.sorting_1, #table_resultados tbody tr.selected td.sorting_2, #table_resultados tbody tr.selected td.sorting_3,
			#table_resultados tbody tr.selected td.sorting_4{
				font-weight: bold;
				background-color: #ffbf00;
			}
			
			.div_busqueda fieldset { 
				border: none;
				margin: 5px;
				padding: 10px;
				margin: 0 auto;
				overflow: hidden;
			}
			
			
				
			.div_busqueda {
				color: #585858;
				
			}
			.drop_down_busqueda{ 
				text-transform: uppercase;
				width: 140px;
				font-size: 12px;
				padding-left: 3px;				
			}
			
			#div_pack_detalle select.drop_down_busqueda{
				font-size: 12px;
			} 
			
			#id_marca.dropdown_busqueda{
				width: 100px;
			}
			
			.div_busqueda label{
				float: left;
				font-size: 12px;
				font-weight: bold;
				margin-right: 5px;
				margin-top: 3px;
			}
			
			.div_busqueda legend {
				font-weight: bold;
				font-size: 12px;
			}
			
			.div_busqueda ul{ 
				list-style: none; 
				margin: 0px;
				padding:0px;
			}
			
			.div_busqueda ul li {
				float: left;				
				padding-left: 5px;
				padding-right: 5px;
				margin-right: 1px;
				border-top:dotted 1px #0099CC;
				border-bottom:dotted 1px #0099CC;
				padding-top:3px;
				padding-bottom:3px;
				height: 19px;
			}
			
			.div_busqueda ul li.buscar {
				float:none;
				font-size: 12px;
			}
			
			#div_busqueda_producto
			{
				margin-top: 15px;
			}
			
			#btn_buscar_packs_x_categoria{
				width: 85px;				
				text-align: center;
			}
			
			#btn_mostrar_producto{
				width: 120px;
				font-size: 12px;
			}
			
			#div_btn_guardar{
				font-size: 12px;
				width: 50px;
			}
			
			#div_pack_detalle{
				width: 510px;
				height: 100px;
				display: none;
			}
			
			.ui-dialog-title {
				font-family: Helvetica;
				font-size: 12px;			
			}
			
			div.ui-dialog div.ui-dialog-titlebar {
				padding: 2px 2px;
				position: relative;
			}
			
			#table_pack_detalle_resultados, #table_resultados_productos_nuevos{
				font-size: 12px;
				font-family: Helvetica;
			}
			
			#div_table_pack_detalle input.item_cantidad{
				width: 60px;
				text-align: center;
			}
			
			#div_table_pack_detalle span.lbl_item_status{
				font-weight: bold;
				padding-right: 5px;
			}
			
			#div_table_pack_detalle span.lbl_item_status_modificado{				
				color: red;
			}
			
			.btn_agregar_producto, .btn_eliminar_producto{
				font-weight: bold;
				font-size: 14px;
			}
			
			#div_btn_guardar{
				width: 100%;
				padding-top: 10px;
			}

			.lbl_pack_en_edicion{
				font-weight: normal;
			}
		</style>
		
		
		<!--script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script-->
		
		<script language="JavaScript" src="../js/jquery-1.12.3.js"></script>
		
		<script type="text/javascript" src="../js/datatables.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cascadingdropdown.js"></script>
		<script language="JavaScript" src="../js/jquery-ui-1.11.4.min.js"></script>
		<script type="text/javascript" src="../js/dataTables.buttons.js"></script>
		
		
		
		<!--script language="JavaScript" src="../js/jquery.cookie.js"></script-->
		
		
		<!--script language="JavaScript" src="../js/jquery.min.js"></script>
		
		<script language="JavaScript" src="../js/jquery.cascadingdropdown.js"></script-->
		
		<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css?a=1"/>
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
 
		

		
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
		
		function CambiarProductoCategoria2(id_producto_categoria_3, id_default)
		{
			
		}
		
		function CambiarProductoCategoria1(id_producto_categoria_2, id_default)
		{
			
		}
		
		function AgregarFilaResultado()
		{
			
		}
		
		function CambiarOpcionGeneral()
		{
			$id_opcion_general = $("#id_opcion_general").val();
			switch($id_opcion_general)
			{
				case "1" : 
					$("#btn_operacion").val("Buscar");
					break;
				case "2" : 
					$("#operacion").val("crear");
					$("#btn_operacion").val("Crear");
					break;
				case "3" :
					$("#operacion").val("modificar");
					$("#btn_operacion").val("Guardar");
					break;
			}
		}
		
		function ValidarIngresoDatos()
		{
			
		}
		
		$(function()
		{
			$("#table_resultados").DataTable({
				//"paging":   false,
				"ordering": false,
				"info":     false,
				"searching": true,
				"autoWidth": false,
				"language": {
		            "lengthMenu": "Mostrar _MENU_ registros. Para Seleccionar, dar click en una fila.",
		            "zeroRecords": "No se encontraron registros.",
		            "info": "Mostrando pagina _PAGE_ de _PAGES_",
		            "infoEmpty": "No hay registros disponibles",
		            "infoFiltered": "(filtrados de un total de _MAX_ registros)",
		            "search": "Buscar:",
		            "paginate": {
						"previous": "Previa",
						"next": "Siguiente"
					}
		       },		       
				"ajax":	{
					"url": '<?php echo $enlace_procesar_producto;?>',
					"type": 'POST',														
					/*"data":	{
						"operacion": 'query_categoria',
						"id_producto_categoria_padre": $("#categoria2").val(),
						"tipo_codificacion_json": 'JS.JSON'
					}*/
					data: function(d) {
			            d.operacion = 'query_categoria_producto';
			            d.id_producto_categoria = $("#id_producto_categoria").val();
			            d.id_marca = $("#id_marca").val();
			            d.flag_pack = 1;
			            d.tipo_codificacion_json = 'JS.JSON';
			        },							
				},
				"columnDefs": [
					{ "targets":[0], className: "dt-body-center", 
						render: function (data, type, row, meta) {
							var ddl = "<input type='hidden' name='pack_hid_id_" + meta.row + "' class='pack_hid_id' value='" + data + "'>";
							ddl += "<span>" + data + "</span>";
							return ddl;
						},"width": "50px","searchable": true,
						
					}, 
					{ "targets": [2,3,4,5,6,8,9,10,13,14,15,16,17,18,19,20,21,22,23,24], "visible": false, "searchable": false },
					{ "targets": [1], "visible": true, "searchable": true, "width":"150px",
						"render": function (data, type, row) {						
						return row[20];
						}					
					},
					{ "targets": [7], "visible": true, "searchable": true,
						"render": function (data, type, row) {
							$ddl = "<input type='hidden' class='pack_hid_descripcion' value='" + data + "'/>";
							$ddl += "<span>" + data + "</span>";
							return $ddl;
						}, "width": "40%", "orderable": true },//Descripcion Corta
					{ "targets": [11], "visible": true, "searchable": false, className: "dt-body-center"},//Nro Serie
					{
						"targets": [12], "visible":true, 
						"render": function (data, type, row) {
							$ddl = Number(row[12]).toFixed(2) + " " + row[15];
							//$ddl += "<select class='prueba'><option>1</option><option>2</option></select>"
							return $ddl;
						}, "width": "30%", "className":"dt-body-center" 
					}							
				],
				"columns": [{ 
					},
					{},
					{ 	/*render: function (data, type, row) {
						var ddl = "<select size='1' id='row-1-division' name='row-1-division' class='cat_flag_habilitado'>";
						ddl += "<option value='" + data+ "'>"+ data + "</option>";
						ddl += "<option value='0'>X</option>";
						ddl += "</select>";
						return ddl;
						}*/
					},
				],
				rowCallback: function (row, data) {
				 
				}						
			});
			
			$(".drop_down_busqueda").on("change", function(){
				$id_producto_categoria = 0;
				for($i = 0; $i <= 3; $i ++)
					if($("#id_producto_categoria" + $i).val() > 0)
						$id_producto_categoria = $("#id_producto_categoria" + $i).val();
				
				$("#id_producto_categoria").val($id_producto_categoria);
			});
			
			$('#table_resultados tbody').on( 'click', 'tr', function () {
				
				$id_producto_pack = $(this).closest("tr").find(".pack_hid_id").val();
				$desc_producto_pack = $(this).closest("tr").find(".pack_hid_descripcion").val();
				
		        if ( $(this).hasClass('selected') ) {
		            $(this).removeClass('selected');
		            $("#id_producto_pack").val(0);
		        }
		        else {
		            $("#table_resultados tr.selected").removeClass('selected');
		            $(this).addClass('selected');
		            
		            $("#id_producto_pack").val($id_producto_pack);		            
		        }
		        
		        if($("#id_producto_pack").val() > 0){
		        	$("#div_pack_detalle").dialog("open");
			        $("#lbl_pack_en_edicion").html($desc_producto_pack);
			        $tabla = $("#table_pack_detalle_resultados").DataTable();
					$tabla.ajax.reload( null, false );			        	
		        }

		    });
		    
		    $("#table_pack_detalle_resultados").on("click",".btn_eliminar_producto", function () {
		    	$tabla = $("#table_pack_detalle_resultados").DataTable();
		    	
		    	$tabla.row($(this).parents('tr')).remove().draw();
			});
		 
		 	/*$(".prueba").on("change", function () {
				alert("Hola");
		    });*/
		   
			
			$("#div_busqueda_pack").cascadingDropdown({
				selectBoxes: [
				{
					selector:'#id_producto_categoria1'
				},
				{
					selector:'#id_producto_categoria2',
						requires: ['#id_producto_categoria1'],
						source: function(request, response) {
							var $id_producto_categoria1 = $("#id_producto_categoria1").val();
							var data = {
								operacion: 'query_categoria',
								id_producto_categoria_padre: $id_producto_categoria1
							};
							
							if($id_producto_categoria1 > 0)
							{
								$("#id_producto_categoria2").empty().append('<option selected="selected" value=0>Seleccione...</option>');
								$("#id_producto_categoria3").empty().append('<option selected="selected" value=0>Seleccione...</option>');
								$.getJSON('<?php echo $enlace_procesar_producto;?>', data, function(data) {
									var selectOnlyOption = data.length <= 1;
									response($.map(data, function(item, index) {
										return {
											label: item.descripcion,
											value: item.id
										};
									}));
								});	
								
							}
							else{
								$("#id_producto_categoria2").empty().append('<option selected="selected" value=0>Seleccione...</option>');
								$("#id_producto_categoria3").empty().append('<option selected="selected" value=0>Seleccione...</option>');
							}
								
						}
				},
				{
					selector:'#id_producto_categoria3',
					requires: ['#id_producto_categoria1','#id_producto_categoria2'],
					requireAll: true,
					source: function(request, response) {
						
						$("#id_producto_categoria3").empty().append('<option selected="selected" value=0>Seleccione...</option>');
						var $id_producto_categoria2 = $("#id_producto_categoria2").val();
						var data = {
							operacion: 'query_categoria',
							id_producto_categoria_padre: $id_producto_categoria2
						};
						
						if($id_producto_categoria2 > 0)
						{
							$.getJSON('<?php echo $enlace_procesar_producto;?>', data, function(data) {
								var selectOnlyOption = data.length <= 1;
								response($.map(data, function(item, index) {
									return {
										label: item.descripcion,
										value: item.id
									};
								}));
							});	
							
						}
						else
							$("#id_producto_categoria3").empty().append('<option selected="selected" value=0>Seleccione...</option>');
					}
				}]
			});
			
			$("#btn_buscar_packs_x_categoria").click(function()
			{
				$("#table_resultados_wrapper").show();
				$("#table_resultados").show();
				
				$tabla = $("#table_resultados").DataTable();
				$tabla.ajax.reload( null, false );
			});
			
			$("#div_pack_detalle").dialog({
				autoOpen: false,
				resizable: false,
				height: "auto",
				width: "720px",
				modal: true
			});
			
			$("#table_pack_detalle_resultados").DataTable({
				"paging":   false,
				"ordering": false,
				"info":     false,
				"searching": false,
				"language": {
		            "lengthMenu": "Mostrar _MENU_ registros. Para Seleccionar, dar click en una fila.",
		            "zeroRecords": "No se encontraron registros.",
		            "info": "Mostrando pagina _PAGE_ de _PAGES_",
		            "infoEmpty": "No hay registros disponibles",
		            "infoFiltered": "(filtrados de un total de _MAX_ registros)",
		            "search": "Buscar:",
		            "paginate": {
						"previous": "Previa",
						"next": "Siguiente"
					}
		       },
				//"autoWidth": true,
				//"autoWidth": false,
				"ajax":	{
					"url": '<?php echo $enlace_procesar_producto;?>',
					"type": 'POST',					
					data: function(d) {
			            d.operacion = 'query_producto_pack';
			            d.id_producto_pack = $("#id_producto_pack").val();
			            d.tipo_codificacion_json = 'JS.JSON';
			        },
				},
				"columnDefs": [
					{ "targets":[0], className: "dt-body-center", 
						render: function (data, type, row, meta) {
							var ddl = "<input type='hidden' name='id_" + meta.row + "' class='id' value='" + row[0] + "'>";//Id Producto
							ddl += "<input type='hidden' name='id_producto_" + meta.row + "' class='id_producto' value='" + row[9] + "'>";//Id Producto
							var status = ((row[16] == "producto_pack_item") ? (0) : (1)); //Status
							ddl += "<input type='hidden' class='pack_item_status' name='pack_item_status_" + meta.row + "' value='" + status + "'>";
							ddl += "<span>" + row[9] + "</span>";
							return ddl;
						},"width": "100px","searchable": true
						
					}, 
					{ "targets": [1,2,3,4,5,6,7,8,9,11], "visible": false, "searchable": false },
					{ "targets": [10], "visible": true, "searchable": false },
					{ "targets": [12], "visible": true, "searchable": false },
					{ "targets": [13], "visible": true, "searchable": false },
					{ "targets": [14],  "visible":true, 
						"render": function (data, type, row, meta) {
							return "<input type='number' name='item_cantidad_" + meta.row + "' class='item_atributo item_cantidad' value='" + data + "' />";							
						}, "width": "30%", "className":"dt-body-center"
					}, 									
					{
						"targets": [15], "visible":true, 
						"render": function (data, type, row, meta) {
							ddl = "<select name='item_flag_habilitado_" + meta.row + "' class='item_atributo item_flag_habilitado'>";
							for(i = 0; i <= 1; i++)
								ddl += "<option value='" + i + "' " + ((data == i)?("selected='selected'"):("")) + ">" + ((i == 1)?("Si"):("No")) + "</option>";
							ddl += "</select>";
							return ddl;
						}, "width": "30%", "className":"dt-body-center" 
					},					
					{
						"targets": [16], "visible":true, 
						"render": function (data, type, row) {
							var clase_status = ((row[16] == "producto_pack_item") ? ("lbl_item_status") : ("lbl_item_status_modificado"));
							var label_status = ((row[16] == "producto_pack_item") ? ("Existente") : ("Por Actualizar"));
							return "<span class='lbl_item_status " + clase_status +"'>" + label_status + "</span>";							
						}, "width": "30%", "className":"dt-body-center" 
					},
					{
						"targets": [17], "visible":true, 
						"render": function (data, type, row) {
							$ddl = "";
							if(data == "new")							
								$ddl = "<button type='button' class='btn_eliminar_producto' title='Click para Eliminar Producto del Pack'>-</button>";
							
							return $ddl;
						}, "width": "30%", "className":"dt-body-center" 
					}
				]
			});
			
			//$("#table_pack_detalle_resultados tbody").on("change","tr td select", function () {
			$("#table_pack_detalle_resultados").on("change",".item_atributo", function () {				
				$pack_item_status = $(this).closest("tr").find(".pack_item_status").val();
				if($pack_item_status == 0)
					$(this).closest("tr").find(".pack_item_status").val(2);//2: Actualizar Registros existentes en la BD. 1: Crear registros en la BD.
				$lbl_actualizado = $(this).closest("tr").find(".lbl_item_status");
				$lbl_actualizado.addClass("lbl_item_status_modificado");
				$lbl_actualizado.css("color","red");
				$lbl_actualizado.html("Por Actualizar");
				
			});
			
			$("#table_resultados_productos_nuevos").DataTable({
				"paging":   false,
				"ordering": false,
				"info":     false,
				"searching": false,
				"autoWidth": false,
				"language": {
		            "lengthMenu": "Mostrar _MENU_ registros. Para Seleccionar, dar click en una fila.",
		            "zeroRecords": "No se encontraron registros.",
		            "info": "Mostrando pagina _PAGE_ de _PAGES_",
		            "infoEmpty": "No hay registros disponibles",
		            "infoFiltered": "(filtrados de un total de _MAX_ registros)",
		            "search": "Buscar:",
		            "paginate": {
						"previous": "Previa",
						"next": "Siguiente"
					}
		       },
				//"autoWidth": false,
				"ajax":	{
					"url": '<?php echo $enlace_procesar_producto;?>',
					"type": 'POST',					
					data: function(d) {
			            d.operacion = 'query_producto_no_en_pack';
			            d.id_marca = $("#id_marca_producto").val();
			            d.id_producto_pack = $("#id_producto_pack").val();			            
			            d.tipo_codificacion_json = 'JS.JSON';
			        },
				},
				"columnDefs": [
					{ "targets":[0], className: "dt-body-center", 
						render: function (data, type, row) {
							var ddl = "<input type='hidden' class='id_producto_nuevo' value='" + data + "'>";
							ddl += "<input type='hidden' class='marca_nuevo' value='" + row[20] + "'>";
							ddl += "<span>" + row[0] + "</span>";
							return ddl;
						},"width": "100px","searchable": true, "width":"10%"
						
					},
					{ "targets": [1], "visible": true, 
						render: function (data, type, row) {
							var ddl = "<input type='hidden' class='producto_categoria_nuevo' value='" + data + "'>";							
							ddl += "<span>" + data + "</span>";
							return ddl;
						}
					},
					{ "targets": [6], "visible": true, 
						render: function (data, type, row) {
							var ddl = "<input type='hidden' class='codigo_nuevo' value='" + data + "'>";							
							ddl += "<span>" + data + "</span>";
							return ddl;
						} 
					},
					{ "targets": [7], "visible": true, 
						render: function (data, type, row) {
							var ddl = "<input type='hidden' class='descripcion_corta_nuevo' value='" + data + "'>";							
							ddl += "<span>" + data + "</span>";
							return ddl;
						} 
					},
					{ "targets": [11], "visible": true, 
						render: function (data, type, row) {
							var ddl = "<input type='hidden' class='nro_serie_nuevo' value='" + data + "'>";							
							ddl += "<span>" + data + "</span>";
							return ddl;
						} },
					{ "targets": [2,3,4,5,8,9,10,12,14,15,16,17,18,19,20,21,22,23,24], "visible": false, "searchable": false },
					{
						"targets": [13], "visible":true, 
						"render": function (data, type, row) {
							$ddl = "<button type='button' class='btn_agregar_producto' title='Click para Agregar el Producto al Pack'>+</button>";
							return $ddl;
						}
					}
				]
			});
			
			$("#btn_mostrar_producto").click(function()
			{
				$("#table_resultados_productos_nuevos_wrapper").show();
				$("#table_resultados_productos_nuevos").show();
				$tabla = $("#table_resultados_productos_nuevos").DataTable();				
				$tabla.ajax.reload( null, false );
			});
			
			$("#table_resultados_productos_nuevos").on("click",".btn_agregar_producto", function () {
				
				$tabla = $("#table_pack_detalle_resultados").DataTable();
				
				$id_producto = $(this).closest("tr").find(".id_producto_nuevo").val();
				$codigo = $(this).closest("tr").find(".codigo_nuevo").val();
				$descripcion_corta = $(this).closest("tr").find(".descripcion_corta_nuevo").val();
				$marca = $(this).closest("tr").find(".marca_nuevo").val();
				$nro_serie = $(this).closest("tr").find(".nro_serie_nuevo").val();
				
				$encontrado = false;
				
				$('#table_pack_detalle_resultados tbody tr').each(function (){
					if($(this).find(".id_producto").val() == $id_producto)
						$encontrado = true;
				});
				
				if(!$encontrado)
				{
					$tabla.row.add([
			            0,
						"",
						"",
						"",
						"",
						"",
						$codigo,
						$(this).closest("tr").find(".descripcion_corta_nuevo").val(),
						"",
						$id_producto,
						$descripcion_corta,
						"",
						$marca,
						$nro_serie,
						1,					
						1,
						"",
						"new"
					]).draw(true);
				}
				else
					console.log("Existing Product Id " + $id_producto + " in table");
			});
			
			$("#btn_guardar").click(function()
			{
				$tabla = $("#table_pack_detalle_resultados").DataTable();
				$data = $tabla.$('input, select').serialize();
				console.log($data);
				
				$("#pack_contenido").val($data);
				$("#operacion").val("modificar_pack_producto");
				
				if(confirm("Desea guardar los cambios?"))				
					$("#producto").submit();
				
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
			<input type="hidden" name="id_usuario" id ="id_usuario" value="<?php echo $id_usuario;?>" />
			<input type="hidden" id="id_producto_categoria" value="0"/>
			<input type="hidden" id="id_producto_pack" name="id_producto_pack" value="0"/>
			<input type="hidden" id="pack_contenido" name="pack_contenido" value="" />
			<input type="hidden" id="operacion" name="operacion" />
			<div id="titulo" align="center	">ADMINISTRACIÓN DE PACK</div>
			<div id="div_busqueda_pack" class="div_busqueda">
				<fieldset id="busqueda_pack">
					<legend align="left">Filtro para Buscar Pack:</legend>
					<ul>
						<li>
							<label for="id_producto_categoria1">Cat. Producto #1:</label>
							<select name="id_producto_categoria1"  id="id_producto_categoria1" class="drop_down_busqueda"/>
								<option value="0">Seleccione...</option>
								<?php
								$lista_cat = $proBLO->ListarCategoriaXCategoriaPadre(0);
								
								foreach($lista_cat as $c)
									echo "<option value=\"$c->id\">".strtoupper($c->descripcion)."</option>";
								?>
							</select>						
						</li>
						<li>
							<label for="id_producto_categoria2">Cat. Producto #2:</label>
							<select name="id_producto_categoria2"  id="id_producto_categoria2" class="drop_down_busqueda"/>
								<option value="0">Seleccione...</option>
							</select>
						</li>
						<li>
							<label for="id_producto_categoria2">Cat. Producto #3:</label>
							<select name="id_producto_categoria3"  id="id_producto_categoria3" class="drop_down_busqueda"/>
								<option value="0">Seleccione...</option>
							</select>
						</li>
						<li>
							<label for="id_marca">Marca:</label>
							<select name="id_marca"  id="id_marca" class="drop_down_busqueda"/>
								<option value="0">Seleccione...</option>
								<?php
								$lista_m = $proBLO->ListarMarcaTodas();
								
								foreach($lista_m as $m)
									echo "<option value=\"$m->id\">".strtoupper($m->nombre)."</option>";
								?>
							</select>
						</li>
						<li>
							<button type="button" id="btn_buscar_packs_x_categoria" class="buscar">Buscar Pack</button>
						</li>
					</ul>
				</fieldset>
			</div>
			<br/>
			<div id="div_table_resultados">
				<table id="table_resultados" cellspacing="0" class="display stripe">
					<thead>
						<tr>
							<th>ID</th>
							<th>Marca</th>
							<th>id_producto_categoria</th>
							<th>id_producto_categoria2</th>
							<th>id_producto_categoria3</th>
							<th>id_producto_categoria4</th>
							<th>codigo</th>
							<th>Descripcion</th>
							<th>descripcion_larga</th>
							<th>pais_origen</th>
							<th>id_pais_origen</th>
							<th>Nro.Serie</th>
							<th>Presentacion</th>
							<th>unidad_medida</th>
							<th>id_unidad_medida</th>
							<th>codigo_unidad_medida</th>
							<th>opcion_cantidad</th>
							<th>usuario</th>
							<th>id_usuario</th>
							<th>id_marca</th>
							<th>Marca</th>
							<th>id_cantidad_default</th>
							<th>flag_venta</th>
							<th>flag_pack</th>
							<th>tipo_objeto</th>
						</tr>
					</thead>
					
							
						
					</thead>				
				</table>
				
			</div>
			
		</form>
	</div>
	<div id="div_pack_detalle" title="Detalle del Pack">
		<div id="titulo" align="center	">
			<span>LISTA DE PRODUCTOS ASIGNADOS AL PACK:</span>
			<span id="lbl_pack_en_edicion"></span>	
		</div>
		<div id="div_table_pack_detalle">
			<table id="table_pack_detalle_resultados" name="table_pack_detalle_resultados" cellspacing="0" class="display stripe">
				<thead>
					<tr>
						<th>Id</th> 
						<th>id_producto_pack</th> 
						<th>pack_descripcion_corta</th> 
						<th>pack_descripcion_larga</th>
						<th>pack_nro_serie</th>
						<th>pack_opcion_cantidad</th> 
						<th>pack_marca</th>
						<th>pack_cantidad_default</th> 
						<th>pack_flag_venta</th>
						<th>id_producto</th>
						<th>Producto</th> 
						<th>producto_descripcion_larga</th>
						<th>Marca</th>
						<th>Nro.Serie</th> 
						<th>Cantidad</th>
						<th>Habilitado</th>
						<th>Estado</th>
						<th></th>
						
					</tr>
				</thead>				
			</table>
		</div>
		
		<div id="div_busqueda_producto" class="div_busqueda">
			<fieldset id="busqueda_producto" class="fs_busqueda">
				<legend align="left">Primero debes Seleccionar Marca, y luego el producto que quieres agregar al Pack:</legend>
				<ul>
					<li>
						<label for="id_marca_producto">Marca:</label>
						<select name="id_marca_producto"  id="id_marca_producto" class="drop_down_busqueda"/>
							<option value="0">Seleccione...</option>
							<?php
							$lista_m = $proBLO->ListarMarcaTodas();
									
							foreach($lista_m as $m)
								echo "<option value=\"$m->id\">".strtoupper($m->nombre)."</option>";
							?>
						</select>						
					</li>
					<li>
						<button type="button" id="btn_mostrar_producto" class="buscar">Mostrar Productos</button>
					</li>
				</ul>
			</fieldset>	
		</div>
		
		<div id="div_busqueda_productos_nuevos" class="div_busqueda">
			<div id="div_table_resultados_productos_nuevos">
				<table id="table_resultados_productos_nuevos" cellspacing="0" class="display stripe">
					<thead>
						<tr>
							<th>Id</th>
							<th>Categoria</th>
							<th>id_producto_categoria</th>
							<th>id_producto_categoria2</th>
							<th>id_producto_categoria3</th>
							<th>id_producto_categoria4</th>
							<th>Codigo</th>
							<th>Descripcion</th>
							<th>descripcion_larga</th>
							<th>pais_origen</th>
							<th>id_pais_origen</th>
							<th>Nro.Serie</th>
							<th>Presentacion</th>
							<th></th>
							<th>id_unidad_medida</th>
							<th>codigo_unidad_medida</th>
							<th>opcion_cantidad</th>
							<th>usuario</th>
							<th>id_usuario</th>
							<th>id_marca</th>
							<th>Marca</th>
							<th>id_cantidad_default</th>
							<th>flag_venta</th>
							<th>flag_pack</th>
							<th>tipo_objeto</th>
						</tr>
					</thead>				
				</table>				
			</div>
			<div id="div_btn_guardar" align="center">
				<button type="button" id="btn_guardar" title="Guardar Cambios" <?php echo $permiso_modificar_productos->isOK ? "" : "disabled='disabled'"?> >Guardar</button>
			</div>
		</div>		
	</div>
	
	</body>
</html>


