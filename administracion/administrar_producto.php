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

$opcion_crear_productos = "4X36UO5V";
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
				border-radius: 10px 10px 10px 10px; font-family: Helvetica; }
			#titulo { font-weight: bold; font-size: 14px; color: #0099CC; margin-bottom: 20px; }
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
			/*#tabla_resultados tbody tr:not(:first-child):hover { background-color: #F8FEA9; }*/
			#tabla_resultados tbody tr:hover { background-color: #F8FEA9; }

			#tabla_resultados { border-collapse: collapse; }	
					
			.btn_edit:hover { cursor: pointer; }
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
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
			var url = "<?php echo $enlace_procesar;?>&operacion=query_categoria&id_producto_categoria_padre=" + id_producto_categoria_3;			
				
			$.getJSON(url, function(data)
			{
				if(data != null)
				{
					$("#id_producto_categoria_2").empty();
					$("#id_producto_categoria_2").append("<option value=\"0\">Seleccione...</option>");
							
					$("#id_producto_categoria_1").empty();
					$("#id_producto_categoria_1").append("<option value=\"0\">No Disponible</option>");
						
				    $.each(data, function(key, val) 
					{
						$("#id_producto_categoria_2").append("<option value=\"" + val.id +"\">" + val.descripcion.toUpperCase() + "</option>");
					});
					
					
					$("#id_producto_categoria_2").val(id_default);
				}
						
			});
		}
		
		function CambiarProductoCategoria1(id_producto_categoria_2, id_default)
		{
			var url = "<?php echo $enlace_procesar;?>&operacion=query_categoria&id_producto_categoria_padre=" + id_producto_categoria_2;
				
			$.getJSON(url, function(data)
			{
				if(data != null)
				{
					$("#id_producto_categoria_1").empty();
					$("#id_producto_categoria_1").append("<option value=\"0\">Seleccione...</option>");
					
					$.each(data, function(key, val) 
					{
						$("#id_producto_categoria_1").append("<option value=\"" + val.id +"\">" + val.descripcion.toUpperCase() + "</option>");
					});
					
					$("#id_producto_categoria_1").val(id_default);
				}
				
			});
		}
		
		function AgregarFilaResultado()
		{
			$("#tabla_resultados").find("tr:gt(0)").remove();
					
			$filtro = "";
					
			$id_producto_categoria_1 = $("#id_producto_categoria_1").val();
			$id_producto_categoria_2 = $("#id_producto_categoria_2").val();
			$id_producto_categoria_3 = $("#id_producto_categoria_3").val();
			$id_marca = $("#id_marca").val();
			$codigo = $("#codigo").val();
			$nro_serie = $("#nro_serie").val();
			$descripcion_corta = $("#descripcion_corta").val();
			$descripcion_larga = $("#descripcion_larga").val();
			$id_pais = $("#id_pais").val();
			$dimension = $("#dimension").val();
			$id_unidad_medida = $("#id_unidad_medida").val();
			$flag_venta = $("#flag_venta").val();
			$flag_pack = $("#flag_pack").val();
			
			if($id_producto_categoria_1 > 0)
				$filtro = $filtro + "&id_producto_categoria_1=" + $id_producto_categoria_1;
			
			if($id_producto_categoria_2 > 0)
				$filtro = $filtro + "&id_producto_categoria_2=" + $id_producto_categoria_2;
			
			if($id_producto_categoria_3 > 0)
				$filtro = $filtro + "&id_producto_categoria_3=" + $id_producto_categoria_3;
			
			if($id_marca > 0)
				$filtro = $filtro + "&id_marca=" + $id_marca;
						
			if($codigo != "")
				$filtro = $filtro + "&codigo=" + $codigo;
						
			if($nro_serie != "")
				$filtro = $filtro + "&nro_serie=" + $nro_serie;
						
			if($descripcion_corta != "")
				$filtro = $filtro + "&descripcion_corta=" + $descripcion_corta;
					
			if($descripcion_larga != "")
				$filtro = $filtro + "&descripcion_larga=" + $descripcion_larga;
				
			if($id_pais > 0)
				$filtro = $filtro + "&id_pais=" + $id_pais;
					
			if($dimension != "")
				$filtro = $filtro + "&dimension=" + $dimension;
					
			if($id_unidad_medida > 0)
				$filtro = $filtro + "&id_unidad_medida=" + $id_unidad_medida;
					
			if($flag_venta != "")
				$filtro = $filtro + "&flag_venta=" + $flag_venta;
				
			if($flag_pack != "")
				$filtro = $filtro + "&flag_pack=" + $flag_pack;
				
			var url = "<?php echo $enlace_procesar;?>&operacion=query" + $filtro;
			
			//alert(url);
					
			$("#div_tabla_resultados").css("display", "block");
									
			$.getJSON(url, function(data)
			{
				if(data != null)
				{
					$.each(data, function(key, val) 
					{
						if(val.flag_venta == 0)
							$flag_venta = "No";
						else
							$flag_venta = "Si";
						
						if(val.flag_pack == 0)
							$flag_pack = "No";
						else
							$flag_pack = "Si";
							
						$tr = "<tr>";
						$tr = $tr + "<td align='center'>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_producto\" value=\"" + val.id +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_producto_categoria_1\" value=\"" + val.id_producto_categoria +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_producto_categoria_2\" value=\"" + val.id_producto_categoria2 +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_producto_categoria_3\" value=\"" + val.id_producto_categoria3 +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"opcion_cantidad\" value=\"" + val.opcion_cantidad +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_cantidad_default\" value=\"" + val.id_cantidad_default +"\"/>";
						$tr = $tr + val.id + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"codigo\" value=\"" + val.codigo +"\"/>" + val.codigo + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"nro_serie\" value=\"" + val.nro_serie +"\"/>" + val.nro_serie + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"descripcion_corta\" value=\"" + val.descripcion_corta +"\"/>" + val.descripcion_corta + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"descripcion_larga\" value=\"" + val.descripcion_larga +"\"/>" + val.descripcion_larga + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"id_marca\" value=\"" + val.id_marca +"\"/>" + val.marca + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_pais\" value=\"" + val.id_pais_origen +"\"/>" + val.pais_origen + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"dimension\" value=\"" + parseFloat(val.dimension).toFixed(2) +"\"/>" + parseFloat(val.dimension).toFixed(2) + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_unidad_medida\" value=\"" + val.id_unidad_medida +"\"/>" + val.unidad_medida + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"flag_venta\" value=\"" + val.flag_venta +"\"/>" + $flag_venta + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"flag_pack\" value=\"" + val.flag_pack +"\"/>" + $flag_pack + "</td>";
						$tr = $tr + "<td align='center'><img src=\"../images/page_edit.png\" class=\"btn_edit\""; 
						$tr = $tr + "title=\"Editar Producto [" + val.descripcion_corta +"]\"/></td>"; 
						$tr = $tr + "</tr>";
								
						$("#tabla_resultados").append($tr);
					});
				}
				else
				{
					$tr = "<tr>";
					$tr = $tr + "<td colspan=11><b>No se Han encontrado Productos para esta Búsqueda.</b></td>";
					$tr = $tr + "</tr>";
					$("#tabla_resultados").append($tr);
				}
			});
			
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
			$id_producto = $("#id_producto").val();
			$id_producto_categoria = $("#id_producto_categoria").val();
			$id_marca = $("#id_marca").val();
			$codigo = $("#codigo").val();
			$nro_serie = $("#nro_serie").val();
			$descripcion_corta = $("#descripcion_corta").val();
			$descripcion_larga = $("#descripcion_larga").val();
			$id_pais = $("#id_pais").val();
			$dimension = $("#dimension").val();
			$id_unidad_medida = $("#id_unidad_medida").val();
			$flag_venta = $("#flag_venta").val();
			$flag_pack = $("#flag_pack").val();
			$opcion_cantidad = $("#opcion_cantidad").val();
			$id_cantidad_default = $("#id_cantidad_default").val();
			$opcion_cantidad_str = "";
			
			for(i = 0; i <=5 ; i ++)
			{
				$opcion = $("#op_cantidad_valor_" + i).val() + ":" + $("#op_cantidad_etiqueta_" + i).val();
				if(i == 0)
					$opcion_cantidad_str = $opcion;
				else
					$opcion_cantidad_str = $opcion_cantidad_str + "," + $opcion;
			}
			$("#opcion_cantidad_str").val($opcion_cantidad_str);
			
			$id_opcion_general = $("#id_opcion_general").val();
			$msg = "Se ha(n) encontrado el/los siguiente(s) Error(es):\n\n";
			$msg_original = $msg;
			
			if($id_opcion_general == 3)
				if($id_producto == 0)
					$msg = $msg + "+ No ha Seleccionado un Producto.\n";
			
			if($id_producto_categoria == 0)
				$msg = $msg + "+ No ha Seleccionado Categoría de Producto.\n";
			
			if($id_marca == 0)
				$msg = $msg + "+ No ha Seleccionado Marca.\n";
			
			if($codigo == "")
				$msg = $msg + "+ No ha Ingresado Código.\n";
			
			if($descripcion_corta == "")
				$msg = $msg + "+ No ha Ingresado Descripción Corta.\n";
			
			if($descripcion_larga == "")
				$msg = $msg + "+ No ha Ingresado Descripción Larga.\n";
				
			if($id_pais == 0)
				$msg = $msg + "+ No ha Seleccionado Pais de Origen.\n";
				
			if($dimension == 0)
				$msg = $msg + "+ No ha Ingresado Dimensión.\n";
				
			if($id_unidad_medida == 0)
				$msg = $msg + "+ No ha Seleccionado Unidad de Medida.\n";
			
			if($flag_venta == "")
				$msg = $msg + "+ No ha Seleccionado Flag Venta.\n";
			
			if($flag_pack == 0)
				$msg = $msg + "+ No ha Seleccionado Flag Pack.\n";
			
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
				
				$("#id_producto").val(0);
				$("#id_producto_categoria").val(0);
				$("#id_producto_categoria_3").val(0);
				$("#id_producto_categoria_2").empty();
				$("#id_producto_categoria_2").append("<option value=\"0\">No Disponible</option>");
				$("#id_producto_categoria_1").empty();
				$("#id_producto_categoria_1").append("<option value=\"0\">No Disponible</option>");
				$("#id_marca").val(0);
				$("#codigo").val("");
				$("#nro_serie").val("");
				$("#descripcion_corta").val("");
				$("#descripcion_larga").val("")
				$("#id_pais").val(0);
				$("#dimension").val("");
				$("#id_unidad_medida").val(0);
				$("#flag_venta").val("");
				$("#flag_pack").val("");
			});
			
			$("#id_producto_categoria_3").change(function()
			{
				$id_producto_categoria_3 = $(this).val();
				$("#id_producto_categoria").val($id_producto_categoria_3); 
				
				CambiarProductoCategoria2($id_producto_categoria_3);
			});
			
			$("#id_producto_categoria_2").change(function()
			{
				$id_producto_categoria_2 = $(this).val();
				$("#id_producto_categoria").val($id_producto_categoria_2);
				
				CambiarProductoCategoria1($id_producto_categoria_2);
				
			});
			
			$("#id_producto_categoria_1").change(function()
			{
				$("#id_producto_categoria").val($(this).val()); 
			})
			
			
			
			$("#id_opcion_general").change(function()
			{
				CambiarOpcionGeneral();
			});
			
			$(".btn_edit").live("click", function()
			{
				$("#id_opcion_general").val(3);
				CambiarOpcionGeneral();
				
				$fila = $(this).parent().parent();
				
				$id_producto = $fila.find(".id_producto").val();
				$id_producto_categoria_1 = $fila.find(".id_producto_categoria_1").val();
				$id_producto_categoria_2 = $fila.find(".id_producto_categoria_2").val();
				$id_producto_categoria_3 = $fila.find(".id_producto_categoria_3").val();
				$id_marca = $fila.find(".id_marca").val();
				$codigo = $fila.find(".codigo").val();
				$nro_serie = $fila.find(".nro_serie").val();
				$descripcion_corta = $fila.find(".descripcion_corta").val();
				$descripcion_larga = $fila.find(".descripcion_larga").val();
				$id_pais = $fila.find(".id_pais").val();
				$dimension = $fila.find(".dimension").val();
				$id_unidad_medida = $fila.find(".id_unidad_medida").val();
				$flag_venta = $fila.find(".flag_venta").val();
				$flag_pack = $fila.find(".flag_pack").val();
				$opcion_cantidad = $fila.find(".opcion_cantidad").val();
				$id_cantidad_default = $fila.find(".id_cantidad_default").val();
				
				$("#id_producto_categoria").val($id_producto_categoria_1);
				
				if($id_producto_categoria_3 == 0 && $id_producto_categoria_2 > 0)
				{
					$id_producto_categoria_3 = $id_producto_categoria_2;
					$id_producto_categoria_2 = $id_producto_categoria_1;
				}
				
				if($id_producto_categoria_3 == 0 && $id_producto_categoria_2 == 0)
					$id_producto_categoria_3 = $id_producto_categoria_1;
				
				$("#id_producto").val($id_producto);
				$("#id_producto_categoria_3").val($id_producto_categoria_3);
				CambiarProductoCategoria2($id_producto_categoria_3, $id_producto_categoria_2);
				CambiarProductoCategoria1($id_producto_categoria_2, $id_producto_categoria_1);
				
				$("#id_marca").val($id_marca);
				$("#codigo").val($codigo);
				$("#nro_serie").val($nro_serie);
				$("#descripcion_corta").val($descripcion_corta);
				$("#descripcion_larga").val($descripcion_larga);
				$("#id_pais").val($id_pais);
				$("#dimension").val($dimension);
				$("#id_unidad_medida").val($id_unidad_medida);
				$("#flag_venta").val($flag_venta);
				$("#flag_pack").val($flag_pack);
				
				$opciones = $opcion_cantidad.split(",");
				for(i = 0; i <=5 ; i ++)
				{
					$opcion = $opciones[i].split(":");
					
					$("#op_cantidad_valor_" + i).val($opcion[0]);
					$("#op_cantidad_etiqueta_" + i).val($opcion[1]);
				}
				
				$("input[name=id_cantidad_default][value=" + $id_cantidad_default + "]").prop('checked', true);
				//$("#id_cantidad_default").val($id_cantidad_default);
				
				/*$msg = "Valores\n\n";
				$msg = $msg + "Id Producto: " + $id_producto + "\n";
				$msg = $msg + "Id Categoria 1 : " + $id_producto_categoria_1 + "\n";
				$msg = $msg + "Id Categoria 2: " + $id_producto_categoria_2 + "\n";
				$msg = $msg + "Id Categoria 3: " + $id_producto_categoria_3 + "\n";
				$msg = $msg + "Id Marca: " + $id_marca + "\n";
				$msg = $msg + "Codigo: " + $codigo + "\n";
				$msg = $msg + "Nro Serie: " + $nro_serie + "\n";
				$msg = $msg + "D. Corta: " + $descripcion_corta + "\n";
				$msg = $msg + "D. Larga: " + $descripcion_larga + "\n";
				$msg = $msg + "Pais: " + $id_pais + "\n";
				$msg = $msg + "Dimensión: " + $dimension + "\n";
				$msg = $msg + "Id. Unidad Medida: " + $id_unidad_medida + "\n";
				$msg = $msg + "Flag Venta: " + $flag_venta + "\n";
				alert($msg);*/
				
				
			});
			
			$("#btn_operacion").click(function()
			{
				$id_opcion_general = $("#id_opcion_general").val();
				
				
				switch($id_opcion_general)
				{
					case "1":
					
						AgregarFilaResultado(); break;
					case "2":
						$("#operacion").val("crear");
						
						$msg = ValidarIngresoDatos();
						if($msg == "")
						{
							if(confirm("¿Seguro que desea Crear el Producto?"))
							{
							<?php 
							if($permiso_crear_productos->isOK)
								echo "$(\"#producto\").submit();";
							else 
								echo "alert(\"No cuentas con Permiso para Crear Productos\")";
							?>
							}	
						}
						else
							alert($msg);
						break;
					case "3":
						$("#operacion").val("modificar");
						$msg = ValidarIngresoDatos();
						if($msg == "")
						{
							if(confirm("¿Seguro que desea Modificar el Producto?"))
							{
							<?php 
							if($permiso_modificar_productos->isOK)
								echo "$(\"#producto\").submit();";
							else 
								echo "alert(\"No cuentas con Permiso para Modificar Productos\")";
							?>
							}
						
						}
						else
							alert($msg);
						break;
						
				}
				
				
			});
		});
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
		<form id="producto" name="producto" method="post" action="<?php echo $enlace_procesar; ?>">
			<input type="hidden" name="id_producto_categoria" id="id_producto_categoria" value="0"/>
			<input type="hidden" name="id_producto" id="id_producto" value="0"/>
			<input type="hidden" name="operacion" id ="operacion" />
			<input type="hidden" name="id_usuario" id ="id_usuario" value="<?php echo $id_usuario;?>" />
			<input type="hidden" name="opcion_cantidad_str" id ="opcion_cantidad_str" />
			
			
		<div id="titulo">ADMINISTRACIÓN DE PRODUCTOS</div>
		<div id="div_opcion_general" align="right">
			<span class="titulo_2">Opción General:</span>
			<select id="id_opcion_general" class="texto_3">
				<option value="1">Buscar Producto</option>
				<option value="2" <?php echo $permiso_crear_productos->isOK? "" : "disabled='disabled'";?>>Crear Producto</option>
				<option value="3" <?php echo $permiso_modificar_productos->isOK? "" : "disabled='disabled'";?>>Editar Producto</option>
			</select>
		</div>
		<div id="div_tabla_ingreso">
			<table id="tabla_ingreso">
				<tr>
					<td width=110px><div class="etiqueta">Categoría #3:</div></td>
					<td width=160px> 
						<select id="id_producto_categoria_3" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							
							$lista_cat = $proBLO->ListarCategoriaXCategoriaPadre(0);
							
							foreach($lista_cat as $c)
								echo "<option value=\"$c->id\">".strtoupper($c->descripcion)."</option>";
							?>
						</select>
					</td>
					<td width="130px"></td>
					<td width=110px><span class="etiqueta">Categoría #2:</span></td>
					<td width=160px>
						<select id="id_producto_categoria_2" class="texto_3">
							<option value="0">No Disponible...</option>
						</select>
					</td>
					<td width="130px"></td>
					<td width=110px><span class="etiqueta">Categoría #1:</span></td>
					<td width="160px">
						<select id="id_producto_categoria_1" class="texto_3">
							<option value="0">No Disponible...</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><span class="etiqueta">Marca:</span></td>
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
					<td></td>
					<td><span class="etiqueta">Codigo (max 15):</span></td>
					<td><input type="text" maxlength="15" id="codigo" name="codigo" value="" class="texto_3"/></td>
					<td></td>
					<td><span class="etiqueta">Nro. Serie:</span></td>
					<td><input type="text" id="nro_serie" name="nro_serie" value="" class="texto_3"/></td>
				</tr>
				<tr>
					<td><span class="etiqueta">Descripción Corta:</span></td>
					<td colspan="2"><input type="text" id="descripcion_corta" name="descripcion_corta" value="" class="texto_5"/></td>
					<td><span class="etiqueta">Descripción Larga:</span></td>
					<td colspan="2"><input type="text" id="descripcion_larga" name="descripcion_larga" value="" class="texto_5"/></td>
					<td><span class="etiqueta">País Origen:</span></td>
					<td>
						<select id="id_pais" name="id_pais" class="texto_2">
							<option value="0">Seleccione...</option>
							<?php
							$lista = $proBLO->ListarPaisTodos();

							foreach($lista as $p)
								echo "<option value=\"$p->id\">".strtoupper($p->nombre)."</option>";
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><span class="etiqueta">Dimensión:</span></td>
					<td><input type="text" id="dimension" name="dimension" value="" class="texto_1" onkeypress="validate()"/></td>
					<td></td>
					<td><span class="etiqueta">Unidad Medida:</span></td>
					<td>
						<select id="id_unidad_medida" name="id_unidad_medida" class="texto_2">
							<option value="0">Seleccione...</option>
							<?php
							$lista = $proBLO->ListarUnidadMedidaTodas();

							foreach($lista as $um)
								echo "<option value=\"$um->id\">".strtoupper($um->descripcion)."</option>";
							?>
						</select>
					</td>
					<td></td>
					<td><span class="etiqueta">Disponible Venta:</span></td>
					<td>
						<select id="flag_venta" name="flag_venta" class="texto_1_5">
							<option value="" selected="selected">--</option>
							<option value="0">No</option>
							<option value="1">Si</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><span class="etiqueta">Pack:</span></td>
					<td>
						<select id="flag_pack" name="flag_pack" class="texto_1_5">
							<option value="" selected="selected">--</option>
							<option value="0">No</option>
							<option value="1">Si</option>
						</select>
					</td>
					<td colspan="6"></td>
				</tr>
				<tr>
					<td><span class="etiqueta">Opción Cantidad:</span></td>
					<td colspan="2">
						<table id="tabla_opcion_cantidad">
							<thead>
								<th align="center">Valor</th>
								<th align="center">Etiqueta</th>
								<th align="center">Default</th>
							</thead>
							<tr>
								<td align="center"><input type="text" class="texto_1" value="0" name="op_cantidad_valor_0" id="op_cantidad_valor_0"/></td>
								<td align="center"><input type="text" class="texto_1" value="0" name="op_cantidad_etiqueta_0" id="op_cantidad_etiqueta_0"/></td>
								<td align="center"><input type="radio" name="id_cantidad_default" id="id_cantidad_default" value="0" checked="checked"></td>
							</tr>
							<tr>
								<td align="center"><input type="text" class="texto_1" value="1" name="op_cantidad_valor_1" id="op_cantidad_valor_1"/></td>
								<td align="center"><input type="text" class="texto_1" value="1" name="op_cantidad_etiqueta_1" id="op_cantidad_etiqueta_1"/></td>
								<td align="center"><input type="radio" name="id_cantidad_default" id="id_cantidad_default" value="1"></td>
							</tr>
							<tr>
								<td align="center"><input type="text" class="texto_1" value="2" name="op_cantidad_valor_2" id="op_cantidad_valor_2"/></td>
								<td align="center"><input type="text" class="texto_1" value="2" name="op_cantidad_etiqueta_2" id="op_cantidad_etiqueta_2"/></td>
								<td align="center"><input type="radio" name="id_cantidad_default" id="id_cantidad_default" value="2"></td>
							</tr>
							<tr>
								<td align="center"><input type="text" class="texto_1" value="3" name="op_cantidad_valor_3" id="op_cantidad_valor_3"/></td>
								<td align="center"><input type="text" class="texto_1" value="3" name="op_cantidad_etiqueta_3" id="op_cantidad_etiqueta_3"/></td>
								<td align="center"><input type="radio" name="id_cantidad_default" id="id_cantidad_default" value="3"></td>
							</tr>
							<tr>
								<td align="center"><input type="text" class="texto_1" value="4" name="op_cantidad_valor_4" id="op_cantidad_valor_4"/></td>
								<td align="center"><input type="text" class="texto_1" value="4" name="op_cantidad_etiqueta_4" id="op_cantidad_etiqueta_4"/></td>
								<td align="center"><input type="radio" name="id_cantidad_default" id="id_cantidad_default" value="4"></td>
							</tr>
							<tr>
								<td align="center"><input type="text" class="texto_1" value="5" name="op_cantidad_valor_5" id="op_cantidad_valor_5"/></td>
								<td align="center"><input type="text" class="texto_1" value="5" name="op_cantidad_etiqueta_5" id="op_cantidad_etiqueta_5"/></td>
								<td align="center"><input type="radio" name="id_cantidad_default" id="id_cantidad_default" value="5"></td>
							</tr>
						</table>
					</td>
					<td colspan="4"></td>
					<td valign="bottom" align="right"><input type="button" class="texto_2" value="Limpiar Valores" id="btn_limpiar_valores" /></td>
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
					<th width=110px>Codigo</th>
					<th width=80px>Nro. Serie</th>
					<th width=180px>Descripción Corta</th>
					<th width=250px>Descripción Larga</th>
					<th width=80px>Marca</th>
					<th width=60px>Pais</th>
					<th width=50px>Dimens</th>
					<th width=70px>U. Medida</th>
					<th width=50px>Venta</th>
					<th width=50px>Pack</th>
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


