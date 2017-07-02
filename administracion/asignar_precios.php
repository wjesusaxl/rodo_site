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

$enlace_procesar = "../procesar_producto.php?id_centro=$id_centro&opcion_original_key=$opcion_key&usr_key=$usr_key";
//$enlace_procesar = "../procesar_producto.php?id_centro=$id_centro";

$proBLO = new ProductoBLO();
$opBLO = new OpcionBLO();

$opcion_crear_precio = "3D0OBX22";
$opcion_modificar_precio= "IAB0E270";

//

$permiso_crear_precio = $opBLO->ValidarOpcionXIdUsuario($opcion_crear_precio, $id_usuario, $id_centro);
$permiso_modificar_precio = $opBLO->ValidarOpcionXIdUsuario($opcion_modificar_precio, $id_usuario, $id_centro);

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
				border-radius: 10px 10px 10px 10px; font-family: Helvetica; }
			#titulo { font-weight: bold; font-size: 14px; color: #585858; }
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 75px; text-align: center; font-size: 11px; }
			.texto_2 { width: 100px; text-align: center; font-size: 11px; }
			.texto_3 { width: 150px; text-align: center; font-size: 11px; }
			.texto_3_5 { width: 180px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4 { width: 200px; text-align: center; font-size: 11px; }
			.texto_4_5 { width: 230px; text-align: center; font-size: 11px; }
			.texto_5 { width: 270px; text-align: center; font-size: 11px; }
			.texto_10 { width: 400px; text-align: center; font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #585858; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			.etiqueta { font-size: 11px; font-weight: bold; color: #585858; float: left; }
			
			#div_opcion_general { float: right; margin-right: 20px; width: 1050px; margin-bottom: 20px;  }
			
			#titulo { margin-bottom: 20px; }
			#id_opcion_general { margin-left: 10px; }
			#tabla_ingreso { border-collapse: collapse; }
			#tabla_ingreso td { border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; padding-bottom: 3px; }
			
			#tabla_opcion_cantidad td { border: none; }
			#tabla_opcion_cantidad th { font-size: 12px; color: #0099CC; }
			
			#div_tabla_resultados { margin-top: 20px; border-collapse: collapse; display: none; }
			#tabla_resultados th { font-size: 12px; color: #0099CC; border-bottom: dotted 1px #0099CC; border-top: dotted 1px #0099CC; background-color: #FFFFFF;}
			#tabla_resultados td { font-size: 11px; color: #585858;}
			#tabla_resultados tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_resultados tr:nth-child(even) { background-color:#FFFFFF; }
			/*#tabla_resultados tbody tr:not(:first-child):hover { background-color: #F8FEA9; }*/
			#tabla_resultados tbody tr:hover { background-color: #F8FEA9; }
			#tabla_resultados { border-collapse: collapse; }
			
			.btn_edit:hover { cursor: pointer; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px;}
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script>
		<script language="JavaScript" src="../js/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="../styles/jquery-ui.css" type="text/css" media="all"/>
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
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
		
		function padStr(i) 
		{
		    return (i < 10) ? "0" + i : "" + i;
		}
		
		function roundNumber(number, digits) {
            var multiple = Math.pow(10, digits);
            var rndedNum = Math.round(number * multiple) / multiple;
            return rndedNum;
        }
		
		
		
		function FechaFormato(fecha, formato)
		{
			//alert(fecha);
			//fecha = new Date(Date.parse(fecha,"yyyy-mm-dd HH MM ss"));
			
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
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		
		function AgregarFilaResultado()
		{
			$("#tabla_resultados").find("tr:gt(0)").remove();
					
			$filtro = "";
					
			$id_producto_categoria = $("#id_producto_categoria").val();
			$id_producto = $("#id_producto").val();
			$descripcion_corta = $("#descripcion_corta").val();
			$id_marca = $("#id_marca").val();
			
			$id_producto_precio_tipo = $("#id_producto_precio_tipo").val();
			$codigo = $("#codigo").val();
			$fecha_inicio = $("#fecha_inicio").val();
			$fecha_fin = $("#fecha_fin").val();
			$precio_neto_mn = $("#precio_neto_mn").val();
			$impuesto_mn = $("#impuesto_mn").val();
			$precio_total_mn = $("#precio_total_mn").val();
			$flag_habilitado = $("#flag_habilitado").val();
			
			
			if($id_producto_categoria > 0)
				$filtro = $filtro + "&id_producto_categoria=" + $id_producto_categoria;
			
			if($id_producto > 0)
				$filtro = $filtro + "&id_producto=" + $id_producto;
				
			if($id_marca > 0)
				$filtro = $filtro + "&id_marca=" + $id_marca;
			
			if($descripcion_corta != "")
				$filtro = $filtro + "&descripcion_corta=" + $descripcion_corta;
			
			if($id_producto_precio_tipo != 0)
				$filtro = $filtro + "&id_producto_precio_tipo=" + $id_producto_precio_tipo;
			
			if($codigo != "")
				$filtro = $filtro + "&codigo=" + $codigo;
			
			if($("#fechas_habilitadas").is(':checked'))
			{
				if($fecha_inicio != "")
					$filtro = $filtro + "&fecha_inicio=" + $fecha_inicio;
					
				if($fecha_fin != "")
					$filtro = $filtro + "&fecha_fin=" + $fecha_fin;
			}
				
			if($precio_neto_mn != "" && $precio_neto_mn > 0)
				$filtro = $filtro + "&precio_neto_mn=" + $precio_neto_mn;
			
			if($impuesto_mn != "" && $impuesto_mn > 0)
				$filtro = $filtro + "&impuesto_mn=" + $impuesto_mn;
				
			if($precio_total_mn != "" && $precio_total_mn > 0)
				$filtro = $filtro + "&precio_total_mn=" + $precio_total_mn;
			
			if($flag_habilitado != "")
				$filtro = $filtro + "&flag_habilitado=" + $flag_habilitado;
				
			$filtro = $filtro + "&flag_venta=1";
				
			var url = "<?php echo $enlace_procesar;?>&operacion=query_precio" + $filtro;
					
			//alert(url);
									
			$.getJSON(url, function(data)
			{
				
				$("#div_tabla_resultados").css("display", "block");
				
				if(data != null)
				{
					$("#div_tabla_resultados").css("display", "block");
					
					$.each(data, function(key, val) 
					{
						if(val.flag_habilitado == 0)
							$flag_habilitado = "No";
						else
							$flag_habilitado = "Si";
							
						$tr = "<tr>";
						$tr = $tr + "<td align='center'>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_producto_precio\" value=\"" + val.id +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_producto\" value=\"" + val.id_producto +"\"/>";
						$tr = $tr + val.id + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"id_producto_categoria\" value=\"" + val.id_producto_categoria +"\"/>" + val.producto_categoria + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"descripcion_corta\" value=\"" + val.descripcion_corta +"\"/>" + val.descripcion_corta + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_marca\" value=\"" + val.id_marca +"\"/>" + val.marca + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"codigo\" value=\"" + val.codigo +"\"/>" + val.codigo + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_producto_precio_tipo\" value=\"" + val.id_producto_precio_tipo +"\"/>" + val.producto_precio_tipo + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha_inicio\" value=\"" + FechaFormato(val.fecha_inicio, "y-m-d h:m:s") +"\"/><b>" + FechaFormato(val.fecha_inicio, "d-m-y") + "</b></td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"fecha_fin\" value=\"" + FechaFormato(val.fecha_fin, "y-m-d h:m:s") +"\"/><b>" + FechaFormato(val.fecha_fin, "d-m-y") + "</b></td>";
						
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"precio_neto_mn\" value=\"" + parseFloat(val.precio_neto_mn).toFixed(2) +"\"/>" + parseFloat(val.precio_neto_mn).toFixed(2) + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"impuesto_mn\" value=\"" + parseFloat(val.impuesto_mn).toFixed(2) +"\"/>" + parseFloat(val.impuesto_mn).toFixed(2) + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"precio_total_mn\" value=\"" + parseFloat(val.precio_total_mn).toFixed(2) +"\"/><b>" + parseFloat(val.precio_total_mn).toFixed(2) + "</b></td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"monto_neto_mn\" value=\"" + val.flag_habilitado +"\"/>" + $flag_habilitado + "</td>";
						$tr = $tr + "<td align='center'><img src=\"../images/page_edit.png\" class=\"btn_edit\""; 
						$tr = $tr + "title=\"Editar Producto [" + val.descripcion_corta +"]\"/></td>"; 
						$tr = $tr + "</tr>";
								
						$("#tabla_resultados").append($tr);		
					});
				}
				else
				{
					$tr = "<tr><b><td colspan=13>No se ha(n) encontrado Precio(s) para la Búsqueda</b></td></tr>";
					
					$("#tabla_resultados").append($tr);
					
				}
			});
			
		}
		
		function ValidarIngresoDatos()
		{
			$id_producto = $("#id_producto").val();
			
			$id_producto_precio_tipo = $("#id_producto_precio_tipo").val();
			$codigo = $("#codigo").val();
			$fecha_inicio = $("#fecha_inicio").val();
			$fecha_fin = $("#fecha_fin").val();
			$precio_neto_mn = $("#precio_neto_mn").val();
			$impuesto_mn = $("#impuesto_mn").val();
			$precio_total_mn = $("#precio_total_mn").val();
			$flag_habilitado = $("#flag_habilitado").val();
			
			$id_opcion_general = $("#id_opcion_general").val();
			$msg = "Se ha(n) encontrado el/los siguiente(s) Error(es):\n\n";
			$msg_original = $msg;
			
			if($id_opcion_general == 3)
				if($id_producto_precio == 0)
					$msg = $msg + "+ No ha Seleccionado un Precio.\n";
			
			if($id_producto == 0)
				$msg = $msg + "+ No ha Seleccionado Producto.\n";
			
			if($id_producto_precio_tipo == 0)
				$msg = $msg + "+ No ha Seleccionado Categoría de Precio.\n";
			
			if($codigo == "")
				$msg = $msg + "+ No ha Ingresado un Código de Precio+.\n";
			
			if($precio_neto_mn == "")
				$msg = $msg + "+ No ha Ingresado Precio Neto.\n";
			
			if($impuesto_mn == "")
				$msg = $msg + "+ No ha Ingresado Impuesto.\n";
				
			if($precio_total_mn == "")
				$msg = $msg + "+ No ha Ingresado Precio Total.\n";
				
			if(parseFloat($precio_total_mn) != (parseFloat($precio_neto_mn) + parseFloat($impuesto_mn)))
				$msg = $msg + "+ Suma de Precios NO Coincide. \n";
			
			if($msg == $msg_original)
				return "";
			else
				return $msg;
				
			
				
		}
		
		$(function()
		{
			
			$("#btn_limpiar_valores").click(function()
			{
				$("#div_tabla_resultados").css("display", "none");
				
				$("#id_producto_precio").val(0);
				$("#id_producto").val(0);
				$("#descripcion_corta").val("");
				$("#id_producto_categoria").val(0);
				$("#id_marca").val(0);
				$("#id_producto_precio_tipo").val(0);
				$("#codigo").val("");
				$("#precio_neto_mn").val("0.00");
				$("#impuesto_mn").val("0.00");
				$("#precio_total_mn").val("0.00");
				$("#flag_habilitado").val(1);
				
				$fecha = new Date();
				
				$("#fecha_inicio_str").datepicker("setDate", $fecha);
				$("#fecha_fin_str").datepicker("setDate", $fecha);
				
				
			});
			
			$url = "<?php echo $enlace_procesar;?>&operacion=query&descripcion_corta=";
			
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
	                                id_marca: item.id_marca,
	                                id_producto_categoria: item.id_producto_categoria
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
			        $("#id_marca").val(ui.item.id_marca);
			        $("#id_producto_categoria").val(ui.item.id_producto_categoria);
			        $("#descripcion_corta_aux").val(ui.item.label);
			    },
			    focus: function(event, ui) {
			        event.preventDefault();
			        $("#descripcion_corta").val(ui.item.label);
			    },
			    change: function(event, ui)
			    {
			    	if($("#descripcion_corta").val() != $("#descripcion_corta_aux").val())
			    		$("#id_producto").val(0);
			    },
                minLength: 3 
            });
			
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
			
			$("#id_opcion_general").change(function()
			{
				CambiarOpcionGeneral();
			});
			
			$(".btn_edit").live("click", function()
			{
				$("#id_opcion_general").val(3);
				CambiarOpcionGeneral();
				
				$fila = $(this).parent().parent();
				
				$id_producto_precio = $fila.find(".id_producto_precio").val();
				$id_producto = $fila.find(".id_producto").val();
				$id_producto_categoria = $fila.find(".id_producto_categoria").val();
				$id_producto_precio_tipo = $fila.find(".id_producto_precio_tipo").val();
				$id_marca = $fila.find(".id_marca").val();
				$descripcion_corta = $fila.find(".descripcion_corta").val();
				$codigo = $fila.find(".codigo").val();
				$fecha_inicio = $fila.find(".fecha_inicio").val();
				$fecha_fin = $fila.find(".fecha_fin").val();
				$precio_neto_mn = $fila.find(".precio_neto_mn").val();
				$impuesto_mn = $fila.find(".impuesto_mn").val();
				$precio_total_mn = $fila.find(".precio_total_mn").val();
				$flag_habilitado = $fila.find(".flag_habilitado").val();
				
				$("#id_producto_precio").val($id_producto_precio);
				$("#id_producto").val($id_producto);
				$("#id_producto_categoria").val($id_producto_categoria);
				$("#id_producto_precio_tipo").val($id_producto_precio_tipo);
				$("#descripcion_corta").val($descripcion_corta);
				$("#id_marca").val($id_marca);
				$("#codigo").val($codigo);
				$("#fecha_inicio_str").datepicker("setDate", FechaFormato($fecha_inicio, "d-m-y"));
				$("#fecha_fin_str").datepicker("setDate", FechaFormato($fecha_fin, "d-m-y"));
				$("#precio_neto_mn").val($precio_neto_mn);
				$("#impuesto_mn").val($impuesto_mn);
				$("#precio_total_mn").val($precio_total_mn);
				$("#flag_habilitado").val($flag_habilitado);
				
			});
			
			$("#btn_operacion").click(function()
			{
				$id_opcion_general = $("#id_opcion_general").val();
				
				
				switch($id_opcion_general)
				{
					case "1":
						AgregarFilaResultado(); break;
					case "2":
						$("#operacion").val("crear_precio");
						$msg = ValidarIngresoDatos();
						if($msg == "")
						{
						<?php 
						if($permiso_crear_precio->isOK)
							echo "$(\"#producto_precio\").submit();";
						else 
							echo "alert(\"No cuentas con Permiso para Crear Precios\")";
						?>
						}
						else
							alert($msg);
						break;
					case "3":
						$("#operacion").val("modificar_precio");
						$msg = ValidarIngresoDatos();
						if($msg == "")
						{
						<?php 
						if($permiso_modificar_precio->isOK)
							echo "$(\"#producto_precio\").submit();";
						else 
							echo "alert(\"No cuentas con Permiso para Modificar Precios\")";
							?>
						}
						else
							alert($msg);
						break;
						
				}
				
				
			});
			
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
		    
		    $("#precio_total_mn").change(function()
		    {
		    	$precio_total_mn = $("#precio_total_mn").val();
		    	$precio_total_mn = roundNumber(parseFloat($precio_total_mn), 2);
					
				$precio_neto_mn = $precio_total_mn / 1.18;
				$precio_neto_mn = roundNumber(parseFloat($precio_neto_mn), 2);										
				$impuesto_mn = $precio_total_mn - $precio_neto_mn;
					
				$("#precio_total_mn").val($precio_total_mn.toFixed(2));
				$("#precio_neto_mn").val($precio_neto_mn.toFixed(2));
				$("#impuesto_mn").val($impuesto_mn.toFixed(2));
									
		    });
		});
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
		<form id="producto_precio" name="producto_precio" method="post" action="<?php echo $enlace_procesar; ?>">
			<input type="hidden" name="operacion" id ="operacion" />
			<input type="hidden" name="id_usuario" id ="id_usuario" value="<?php echo $id_usuario;?>" />
			<input type="hidden" name="id_producto" id ="id_producto" value="<?php echo $id_producto;?>" />
			<input type="hidden" name="descripcion_corta_aux" id ="descripcion_corta_aux" value="" />
			<input type="hidden" name="id_producto_precio" id ="id_producto_precio" value="<?php echo $id_producto_precio;?>" />
			
		<div id="titulo">ASIGNACIÓN DE PRECIOS DE PRODUCTOS</div>
		<div id="div_opcion_general" align="right">
			<span class="titulo_2">Opción General:</span>
			<select id="id_opcion_general" class="texto_3">
				<option value="1">Buscar Precio</option>
				<option value="2" <?php echo $permiso_crear_precio->isOK? "" : "disabled='disabled'";?>>Crear Precio</option>
				<option value="3" <?php echo $permiso_modificar_precio->isOK? "" : "disabled='disabled'";?>>Editar Precio</option>
			</select>
		</div>
		<div id="div_tabla_ingreso">
			<table id="tabla_ingreso">
				<tr>
					<td width=100px><div class="etiqueta">Categoría:</div></td>
					<td width=160px> 
						<select id="id_producto_categoria" name="id_producto_categoria" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							$lista_cat = $proBLO->ListarCategoriaTodas();
							foreach($lista_cat as $c)
								echo "<option value=\"$c->id\">".strtoupper($c->descripcion)."</option>";
							?>
						</select>
					</td>
					<td width="100px"></td>
					<td width="110px"><span class="etiqueta">Producto:</span></td>
					<td><input type="text" id="descripcion_corta" name="descripcion_corta" value="" class="texto_4"/></td>
					<td width="100px"></td>
					<td width=100px><span class="etiqueta">Marca:</span></td>
					<td>
						<select id="id_marca" name="id_marca" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							$lista = $proBLO->ListarMarcaTodas();
							foreach($lista as $m)
								echo "<option value=\"$m->id\">".strtoupper($m->nombre)."</option>";
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width=100px><span class="etiqueta">Precio Tipo:</span></td>
					<td>
						<select id="id_producto_precio_tipo" name="id_producto_precio_tipo" class="texto_2">
							<option value="0">Seleccione...</option>
							<?php
							$lista = $proBLO->ListarPrecioTipoTodos();
							foreach($lista as $t)
								echo "<option value=\"$t->id\">".strtoupper($t->descripcion)."</option>";
							?>
						</select>
					</td>
					<td></td>
					<td><span class="etiqueta">Fecha Inicio:</span></td>
					<td>
						<input type="text" id="fecha_inicio_str" name="fecha_inicio_str" value="" readonly="readonly" class="texto_1_5"/>
						<input type="hidden" id="fecha_inicio" name="fecha_inicio"/>
					</td>
					<td></td>
					<td><span class="etiqueta">Fecha Fin:</span></td>
					<td>
						<input type="text" id="fecha_fin_str" name="fecha_fin_str" value="" readonly="readonly" class="texto_1_5"/>
						<input type="hidden" id="fecha_fin" name="fecha_fin"/>
					</td>
				</tr>
				<tr>
					
				</tr>
				<tr>
					<td><span class="etiqueta">Precio Neto S/.:</span></td>
					<td><input type="number" id="precio_neto_mn" name="precio_neto_mn" value="0.00" class="texto_1" onkeypress="validate()" readonly="readonly"/></td>
					<td></td>
					<td><span class="etiqueta">Impuesto S/.:</span></td>
					<td><input type="number" id="impuesto_mn" name="impuesto_mn" value="0.00" class="texto_1" onkeypress="validate()" readonly="readonly"/></td>
					<td></td>
					<td><span class="etiqueta">Precio Total S/.:</span></td>
					<td><input type="number" id="precio_total_mn" name="precio_total_mn" value="0.00" class="texto_1" onkeypress="validate()"/></td>
				</tr>
				<tr>
					<td><span class="etiqueta">Código:</span></td>
					<td><input type="text" id="codigo" name="codigo" value="" class="texto_2"/></td>
					<td></td>
					<td><span class="etiqueta">Habilitado:</span></td>
					<td>
						<select id="flag_habilitado" name="flag_habilitado" class="texto_1">
							<option value="1">Si</option>
							<option value="0">No</option>
						</select>
					</td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="float: left;"><input type="checkbox" id="fechas_habilitadas" /></div>
						<div style="float: left; margin-top: 6px; margin-left: 3px; "><span class="etiqueta">Fechas Habilitadas para Búsqueda.</span></div>
					</td>
					<td colspan="6" align="right"><input type="button" class="texto_2" value="Limpiar Valores" id="btn_limpiar_valores" /></td>
				</tr>
				<tr>
					<td colspan="8" align="center">
						<div id="div_operacion">
							<input type="button" value="Buscar" class="texto_2" id="btn_operacion" />
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="div_tabla_resultados">
			<table id="tabla_resultados">
				<thead>
					<th width=30px>Id</th>
					<th width=120px>Categ.Producto</th>
					<th width=250px>Producto</th>
					<th width=80px>Marca</th>
					<th width=80px>Cod.Precio</th>
					<th width=50px>T.Precio</th>
					<th width=80px>F.Inicio</th>
					<th width=80px>F.Fin</th>
					<th width=60px>P.Neto S/.</th>
					<th width=60px>Impto S/.</th>
					<th width=60px>P.Total S/.</th>
					<th width=60px>Habilitado</th>
					<th></th>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			
		</div>
		</form>
	</div>
	
	</body>
</html>