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
include ("../clases/movimiento.php");
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_almacen.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";
$enlace_query_producto = "../procesar_producto.php";
$enlace_query_stock = "../procesar_stock.php?id_centro=$id_centro";

$opcion_crear_turno = "";
$opcion_cerrar_turno = "";


$opcBLO = new OpcionBLO();
$almBLO = new AlmacenBLO();
$movBLO = new MovimientoBLO();

$permiso_crear_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_crear_turno, $usuario->id, $id_centro);
$permiso_cerrar_turno = $opcBLO->ValidarOpcionXIdUsuario($opcion_cerrar_turno, $usuario->id, $id_centro);


if($id_usuario > 0)
{
	$lista_almacenes_entrada = $almBLO->ListarAlmacenEntradaXIdUsuario($id_usuario, $id_centro);
	$lista_almacenes_salida = $almBLO->ListarAlmacenSalidaXIdUsuario($id_usuario, $id_centro);
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
			#div_main {  width: 1100px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; 
				overflow: hidden; border-radius: 10px 10px 10px 10px }
			.etiqueta { font-family: Helvetica; font-size: 12px; font-weight: bold; color: #585858; }
			.etiqueta_1 { font-family: Helvetica; font-size: 12px;  color: #585858; }
			select { font-family: Helvetica; font-size: 12px; }
			
			.dato { font-family: Helvetica; font-size: 11px; text-align: center; }
			.texto_1 { width: 50px; }
			.texto_1_5 { width: 65px;}
			.texto_2 { width: 100px; }
			.texto_3 { width: 150px; }
			.texto_4 { width: 200px; }
			.texto_5 { width: 300px; }
			.texto_6 { width: 450px; }
			.texto_7 { width: 550px; }
			
			.cantidad { width: 45px;font-size: 11px; text-align: center; }
			.eliminar_fila_producto { width: 100px; text-align: center; font-size: 11px; }
			#crear_movimiento { font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #585858; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			#tabla_info { border-collapse: collapse;}
			#tabla_info tbody td{ border-bottom: dotted 1px #0099CC;  }
			.td_titulo { border-bottom: dotted 1px #0099CC; }
			
			#div_titulo_producto { float: left; width: 1050px;  }
			#tabla_producto { }
			#div_tabla_producto { width: 1000px; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; margin-bottom: 10px; margin-top: 20px;
				display: none; }
			
			#div_tabla_lista_productos { margin-top: 20px; display: none; }
			#tabla_lista_productos { border-collapse: collapse; }
			#tabla_lista_productos thead { color: #0099CC; font-family: Helvetica; font-size: 12px; font-weight: bold; border-top: dotted 1px #0099CC;
				border-bottom: dotted 1px #0099CC; }
			#tabla_lista_productos td { font-family: Helvetica; font-size: 11px; color: #585858;}
			.ui-menu-item { font-family: Helvetica; font-size: 11px;}
			
			#tabla_lista_productos tr:nth-child(even) { background-color:#DAF1F7; border-radius: 5px 5px 5px 5px; }
			#tabla_lista_productos tr:nth-child(odd) { background-color:#FFFFFF; border-radius: 5px 5px 5px 5px; }
			
			#div_operacion { margin-top: 15px; display: none; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px;}
			
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script>
		<script language="JavaScript" src="../js/jquery-ui.min.js"></script>
		
		
		<link rel="stylesheet" href="../styles/jquery-ui.css" type="text/css" media="all"/>
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
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
							$("#id_producto").val(val.id);
							$("#marca").val(val.marca);
							$("#descripcion_corta").val(val.descripcion_corta);
							$("#producto_categoria").val(val.producto_categoria);
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
				$descripcion = $("#descripcion_corta").val();
				$marca = $("#marca").val();
				$nro_serie = $("#nro_serie").val();
				$producto_categoria = $("#producto_categoria").val();
				$factor = $("#factor_movimiento").val();
				
				$nro_filas = $('#tabla_lista_productos tbody tr').length;
				
				$nro_filas = $nro_filas + 1;
				
				$id_almacen_origen = $("#id_almacen_origen");
				$id_almacen_destino = $("#id_almacen_destino");
				
				if($id_almacen_origen.val() > 0 || $id_almacen_destino.val() > 0)
				{
					if($nro_filas == 1)
						$("#div_tabla_lista_productos").css("display", "block");					
					
					$url2 = "<?php echo $enlace_query_stock; ?>&operacion=query_stock_movimiento&id_almacen_origen=" + $id_almacen_origen.val();
					$url2 = $url2 + "&id_almacen_destino=" + $id_almacen_destino.val() + "&id_producto=" + $id_producto;
					
					$.getJSON($url2, function(data)
					{ 
						if(data != null)
						{
							$.each(data, function(key, val)
							{
								$stock_almacen_origen = "";
								$stock_almacen_destino = "";
								
								if($factor <= 0)
									$stock_almacen_origen = parseFloat(val.cantidad).toFixed(2);									
								
								if($factor >= 0)
									$stock_almacen_destino = parseFloat(val.cantidad2).toFixed(2);
								
								$tr = "<tr class=\"fila_producto\">";
								$tr = $tr + "<td align=\"center\" class=\"fila_id\">" + $nro_filas + "</td>";
								$tr = $tr + "<td align=\"center\">" + $producto_categoria + "</td>";
								$tr = $tr + "<td align=\"center\">" + $nro_serie + "</td>";
								$tr = $tr + "<td class=\"fila_descripcion\">" + $descripcion;
								
								$tr = $tr + "<input type=\"hidden\" class=\"id_producto\" name=\"id_producto_" + $nro_filas + "\" value=\"" + $id_producto +  "\"/>"; 
								$tr = $tr + "</td>";
								$tr = $tr + "<td align=\"center\">" + $marca + "</td>";
								$tr = $tr + "<td align=\"center\"><input type=\"number\" class=\"cantidad\" name=\"cantidad_" + $nro_filas + "\" value=\"0\"  onkeypress='validate(event)'/></td>";
								$tr = $tr + "<td align=\"center\">" + $stock_almacen_origen + "</td>";
								$tr = $tr + "<td align=\"center\">" + $stock_almacen_destino + "</td>";
								$tr = $tr + "<td><select class=\"eliminar_fila_producto\">";
								$tr = $tr + "<option value=\"0\">Seleccione...</option>";
								$tr = $tr + "<option value=\"1\">Eliminar</option>";
								$tr = $tr + "</select></td>";
								$tr = $tr + "</tr>";
								
								$("#tabla_lista_productos").append($tr);
									
								$("#id_producto").val(0);
								$("#descripcion_corta").val("");
								
								if($("#div_operacion").css("display") == "none")
									$("#div_operacion").css("display", "block");
							});
						}
					});	
					
					
				}
				else
					alert("No ha seleccionado Almacén de Origen o Destino!");
				
				
				    
				   				        
			}
			else
				alert("No ha seleccionado Producto")        	
        }
		
		$(function()
		{
			$("#agregar_nro_serie").click(function()
			{
				AgregarNroSerie();
			});
			
			$("#agregar_descripcion").click(function()
			{
				AgregarFilaProducto();
				
			        //$("#selected-customer").val(ui.item.label);
			});
			
			$("#id_movimiento_motivo").live("change", function()
			{
				$id_movimiento_motivo = $(this).val();
				
				$("#div_tabla_producto").css("display", "none");
				$("#div_tabla_lista_productos tbody tr").remove();
				$("#div_tabla_lista_productos").css("display", "none");
				
				url = "../procesar_movimiento.php?operacion=query_movimiento_motivo&id_movimiento_motivo=" + $id_movimiento_motivo;
				
				$("#id_almacen_origen").val(0);
				$("#id_almacen_destino").val(0);
				
				$("#id_almacen_origen").attr("disabled","disabled");
				$("#id_almacen_destino").attr("disabled","disabled");
				
				if($id_movimiento_motivo > 0)
				{
					$("#div_tabla_producto").css("display", "block");										
					
					$.getJSON(url, function(data)
					{ 
						if(data != null)
						{
							$.each(data, function(key, val)
							{
								factor = val.factor;
								
								$("#factor_movimiento").val(factor);
								
								if(factor == 0)
								{
									$("#id_almacen_origen").removeAttr("disabled");
									$("#id_almacen_destino").removeAttr("disabled");
								}
								if(factor > 0)
									$("#id_almacen_destino").removeAttr("disabled");
								if(factor < 0)
									$("#id_almacen_origen").removeAttr("disabled");
							});
							
						}
					});
				}
				
				
			});
			
			$url = "<?php echo $enlace_query_producto;?>?operacion=query&descripcion_corta=";
			
			$("#descripcion_corta").autocomplete({
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
	                                producto_categoria: item.producto_categoria,
	                                nro_serie: item.nro_serie,
	                                descripcion_corta: item.descripcion_corta
	                            	}
                        		})
                           
                           	)
						}
                    });

                },
                select: function(event, ui) {
			        event.preventDefault();
			        $("#descripcion_corta").val(ui.item.label);
			        $("#id_producto").val(ui.item.value);
			        $("#nro_serie").val(ui.item.nro_serie);
			        $("#marca").val(ui.item.marca);
			        $("#unidad_medida").val(ui.item.unidad_medida);
			        $("#descripcion_corta_aux").val(ui.item.label);
			        $("#producto_categoria").val(ui.item.producto_categoria);
			    },
			    focus: function(event, ui) {
			        event.preventDefault();
			        $("#descripcion").val(ui.item.label);
			    },
			    change: function(event, ui)
			    {
			    	if($("#descripcion_corta").val() != $("#descripcion_corta_aux").val())
			    		$("#id_producto").val(0);
			    },
                minLength: 3 
            });
            
            $(".eliminar_fila_producto").live("change", function(ev) 
			{		
				if($(this).val() == 1)
			  	{
			  		if(confirm("¿Seguro que desea Eliminar el Producto de la lista?"))
			  		{
			  			$fila = $(this).parent().parent();
			  			$fila.remove();
			  			
			  			$cont = 1;
			  			
			  			$('#tabla_lista_productos tr.fila_producto').each(function()
			  			{
			  				$fila_id = $(this).find(".fila_id");
			  				$id_producto = $(this).find(".id_producto");
			  				$cantidad = $(this).find(".cantidad");
			  				
			  				$fila_id.empty();
			  				$fila_id.append($cont);
			  				
			  				$id_producto.attr("name", "id_producto_" + $cont);
			  				$id_producto.attr("id", "id_producto_" + $cont);
			  				
			  				$cantidad.attr("name", "cantidad_" + $cont);
			  				$cantidad.attr("id", "cantidad_" + $cont);
			  				
			  				$cont++;			  				
			  			});
			  			
			  			$nro_filas = $('#tabla_lista_productos tr').length;
			  			
			  			if($nro_filas == 1)
							$("#div_operacion").css("display", "none");
							
					
			  		}
			  		else
			  		{
			  			$(this).val(0);
			  			//return false;
			  		}
			  		
			  	}
			  	
			});
			
			$("#crear_movimiento").click(function()
			{
				$res = true;
				
				$id_movimiento_motivo = $("#id_movimiento_motivo").val();
				$id_almacen_origen = $("#id_almacen_origen").val();
				$id_almacen_destino = $("#id_almacen_destino").val();
				
				$nro_items = $('#tabla_lista_productos tr.fila_producto').length;
				
				$("#nro_items").val($nro_items);
				
				$msg = "Error(es) Encontrado(s):\n\n"
				
				if($id_movimiento_motivo == 0)
				{
					$msg = $msg + "+ No ha seleccionado Motivo de Movimiento.\n";
					$res = false;
				}
					
				if($id_almacen_origen == 0 && $id_almacen_destino == 0)
				{
					$msg = $msg + "+ No ha seleccionado Ningún Almacen.\n";
					$res = false;
				}
				
				if($nro_items == 0)
				{
					$msg = $msg + "+ No ha ingresado ningún Item.\n";
					$res = false;
				}
				
				if($res)
				{
					$("#operacion").val("crear_movimiento");
					$("#movimiento").submit();
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
	<form id="movimiento" name="movimiento" method="post" action="<?php echo $enlace_procesar; ?>">
		<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>"/>
		<input type="hidden" id="id_centro" name="id_centro" value="<?php echo $id_centro;?>"/>
		<input type="hidden" id="nro_items" name="nro_items" value=""/>
		<input type="hidden" id="operacion" name="operacion" />
		<input type="hidden" id="factor_movimiento" />
		<input id="nro_serie" name="nro_serie" type="hidden" />
		<input id="id_producto" name="id_producto" type="hidden" value="0"/>
		<input id="marca" name="marca" type="hidden" />
		<input id="producto_categoria" name="producto_categoria" type="hidden" />
		<input type="hidden" name="descripcion_corta_aux" id="descripcion_corta_aux" value="" />
		<div id="div_info">
			
			<table id="tabla_info">
				<tr><td colspan="9" align="center"><span class="titulo_1">REGISTRO DE MOVIMIENTOS DE ALMACEN</span></td></tr>
				<tr height="20px"></tr>
				<tr>
					<td align="center"><span class="etiqueta">Tipo de Movimiento:</span></td>
					<td>
						<select id="id_movimiento_motivo" name="id_movimiento_motivo" class="texto_3 dato">
							<option value="0">Seleccione...</option>
							<?php
							$lista_motivos = $movBLO->ListarMotivoHabilitadoXIdUsuario($id_usuario, $id_centro);
							if(!is_null($lista_motivos))
								foreach($lista_motivos as $m)
									echo "<option value=\"$m->id_movimiento_motivo\">".strtoupper($m->movimiento_motivo)."</option>";
							?>
						</select>
						
					</td>
					<td width="50px"></td>
					<td><span class="etiqueta">Almacen Origen:</span></td>
					<td>
						<select id="id_almacen_origen" name="id_almacen_origen" class="texto_4 dato" disabled="disabled">
							<option value="0">Seleccione...</option>
							<?php
							
							if(!is_null($lista_almacenes_entrada))
								foreach($lista_almacenes_entrada as $au)
									echo "<option value=\"$au->id_almacen\">$au->almacen</option>";
							?>
						</select>
					</td>
					<td width="50px"></td>
					<td><span class="etiqueta">Almacen Destino:</span></td>
					<td>
						<select id="id_almacen_destino" name="id_almacen_destino" class="texto_4 dato" disabled="disabled">
							<option value="0">Seleccione...</option>
							<?php
							if(!is_null($lista_almacenes_salida))
								foreach($lista_almacenes_salida as $au)
									echo "<option value=\"$au->id_almacen\">$au->almacen</option>";
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><span class="etiqueta">Fecha:</span></td>
					<td><input class="texto_2 dato" id="fecha" name="fecha" value="<?php echo date("d-m-Y");?>" readonly='readonly'/></td>
					<td></td>
					<td><span class="etiqueta">Comentarios:</span></td>
					<td colspan="4" ><input class="texto_7 dato" id="comentarios" name="comentarios" align="center" /></td>
				</tr>
				
				
				
			</table>
			
		</div>
		<div id="div_tabla_producto">
			<table id="tabla_producto">
				<tr><td colspan="6"><span class="etiqueta_1">Ingresar Items para poder realizar el Movimiento de Almacen:<span></span></td></tr>
				<tr>
					<td><span class="etiqueta">Nro. Serie:</span></td>
					<td class="td_titulo"><input class="texto_3 dato" id="nro_serie_t" name="nro_serie_t" align="center" /></td>
					<td ><input type="button" class="texto_1_5 dato" value="Agregar" id="agregar_nro_serie" /></td>
					<td width="30px"></td>
					<td><span class="etiqueta">Producto:</span></td>
					<td class="td_titulo"><input class="texto_6 dato" id="descripcion_corta" name="descripcion_corta" align="center" /></td>
					<td><input type="button" class="texto_1_5  dato" value="Agregar" id="agregar_descripcion" /></td>
					
				</tr>
			</table>
		</div>
		<div id="div_tabla_lista_productos">
			<table id="tabla_lista_productos">
				<thead>
					<th width=20px>#</th>
					<th width=150px>Categoría</th>
					<th width=100px>Nro Serie</th>
					<th width=220px>Producto</th>
					<th width=130px>Marca</th>
					<th width=80px>Cantidad</th>
					<th width=100px>Cant.Alm.Origen</th>
					<th width=100px>Cant.Alm.Destino</th>
					<th wdith=110px>Operación</th>
				</thead>
			</table>
			<div id="div_operacion">
					
				<input id="crear_movimiento" value="Crear Movimiento" class="texto_2" alt="Crear Compra" type="button" />
			</div>
		</div>	
	</form>
	</div>
	
	</body>
</html>