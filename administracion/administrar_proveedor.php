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
include ("../clases/proveedor.php");
include ("../clases/tipo_documento.php");
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_proveedor.php?id_centro=$id_centro&opcion_original_key=$opcion_key&usr_key=$usr_key";
//$enlace_procesar = "../procesar_producto.php?id_centro=$id_centro";

$proBLO = new ProveedorBLO();
$tdBLO = new TipoDocumentoBLO();
$opBLO = new OpcionBLO();

$opcion_crear_proveedor = "O7GW6U77";
$opcion_modificar_proveedor= "8JM2C65J";

$permiso_crear_proveedor = $opBLO->ValidarOpcionXIdUsuario($opcion_crear_proveedor, $id_usuario, $id_centro);
$permiso_modificar_proveedor = $opBLO->ValidarOpcionXIdUsuario($opcion_modificar_proveedor, $id_usuario, $id_centro);

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
			#div_main {  width: 1150px; border: dotted 1px #0099CC; background-color: #FFFFFF; padding-top: 10px; padding-bottom: 10px; margin: 0 auto; overflow: hidden; 
				border-radius: 10px 10px 10px 10px; font-family: Helvetica; }
			#titulo { font-weight: bold; font-size: 14px; color: #585858; }
			.texto_1 { width: 50px; text-align: center; font-size: 11px; }
			.texto_1_5 { width: 65px; text-align: center; font-size: 11px; }
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
			#tabla_resultados th { font-size: 12px; color: #0099CC; border-bottom: dotted 1px #0099CC; border-top: dotted 1px #0099CC; }
			#tabla_resultados td { font-size: 11px; color: #585858;}
			#tabla_resultados tr:nth-child(even) { background-color:#DAF1F7; }
			#tabla_resultados tr:nth-child(odd) { background-color:#FFFFFF; }
			/*#tabla_resultados tbody tr:not(:first-child):hover { background-color: #F8FEA9; }*/
			#tabla_resultados tbody tr:hover { background-color: #F8FEA9; }
			
			#tabla_resultados {border-collapse: collapse; }
			
			.btn_edit:hover { cursor: pointer; }
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		
		
		
		
		function AgregarFilaResultado()
		{
			$("#tabla_resultados").find("tr:gt(0)").remove();
					
			$filtro = "";
					
			$id_proveedor_categoria = $("#id_proveedor_categoria").val();
			$id_tipo_documento = $("#id_tipo_documento").val();
			$nro_documento = $("#nro_documento").val();
			$razon_social = $("#razon_social").val();
			$nombre_comercial = $("#nombre_comercial").val();
			$telefonos = $("#telefonos").val();
			$direccion = $("#direccion").val();
			$comentarios = $("#comentarios").val();
			
			if($id_proveedor_categoria > 0)
				$filtro = $filtro + "&id_proveedor_categoria=" + $id_proveedor_categoria;
			
			if($id_tipo_documento > 0)
				$filtro = $filtro + "&id_tipo_documento=" + $id_tipo_documento;
			
			if($nro_documento != "")
				$filtro = $filtro + "&nro_documento=" + $nro_documento;
			
			if($razon_social != "")
				$filtro = $filtro + "&razon_social=" + $razon_social;
						
			if($nombre_comercial != "")
				$filtro = $filtro + "&nombre_comercial=" + $nombre_comercial;
				
			if($telefonos != "")
				$filtro = $filtro + "&telefonos=" + $telefonos;
				
			if($direccion != "")
				$filtro = $filtro + "&direccion=" + $direccion;
				
			if($comentarios != "")
				$filtro = $filtro + "&comentarios=" + $comentarios;
			
				
			var url = "<?php echo $enlace_procesar;?>&operacion=query" + $filtro;
					
					//alert(url);
									
			$.getJSON(url, function(data)
			{
				if(data != null)
				{
					$("#div_tabla_resultados").css("display", "block");
					
					$.each(data, function(key, val) 
					{
						if(val.flag_venta == 0)
							$flag_venta = "No";
						else
							$flag_venta = "Si";
							
						$tr = "<tr>";
						$tr = $tr + "<td align='center'>";
						$tr = $tr + "<input type=\"hidden\" class=\"id_proveedor\" value=\"" + val.id +"\"/>";
						$tr = $tr + val.id + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_proveedor_categoria\" value=\"" + val.id_proveedor_categoria +"\"/>" + val.proveedor_categoria + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_tipo_documento\" value=\"" + val.id_tipo_documento +"\"/>" + val.tipo_documento + "</td>";
						$tr = $tr + "<td align='center'>";
						$tr = $tr + "<input type=\"hidden\" class=\"nro_documento\" value=\"" + val.nro_documento +"\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"razon_social\" value=\"" + val.razon_social +"\"/>"  + val.nro_documento + " " + val.razon_social;
						$tr = $tr + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"nombre_comercial\" value=\"" + val.nombre_comercial +"\"/>" + val.nombre_comercial + "</td>";
						$tr = $tr + "<td><input type=\"hidden\" class=\"direccion\" value=\"" + val.direccion +"\"/>" + val.direccion + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"telefonos\" value=\"" + val.telefonos +"\"/>" + val.telefonos + "</td>";
						$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"comentarios\" value=\"" + val.comentarios +"\"/>" + val.comentarios + "</td>";
						$tr = $tr + "<td align='center'><img src=\"../images/page_edit.png\" class=\"btn_edit\""; 
						$tr = $tr + "title=\"Editar Producto [" + val.nombre_comercial +"]\"/></td>"; 
						$tr = $tr + "</tr>";
								
						$("#tabla_resultados").append($tr);
					});
				}
			});
			
		}
		
		function ValidarIngresoDatos()
		{
			$id_proveedor = $("#id_proveedor").val();
			$id_proveedor_categoria = $("#id_proveedor_categoria").val();
			$id_tipo_documento = $("#id_tipo_documento").val();
			$nro_documento = $("#nro_documento").val();
			$razon_social = $("#razon_social").val();
			$nombre_comercial = $("#nombre_comercial").val();
			$direccion = $("#direccion").val();
			
			$id_opcion_general = $("#id_opcion_general").val();
			$msg = "Se ha(n) encontrado el/los siguiente(s) Error(es):\n\n";
			$msg_original = $msg;
			
			if($id_opcion_general == 3)
				if($id_proveedor == 0)
					$msg = $msg + "+ No ha Seleccionado un Proveedor.\n";
			
			if($id_proveedor_categoria == 0)
				$msg = $msg + "+ No ha Seleccionado Categoría de Proveedor.\n";
			
			if($nro_documento == "")
				$msg = $msg + "+ No ha Ingresado un Número de Documento.\n";
			
			if($razon_social == "")
				$msg = $msg + "+ No ha Ingresado Razón Social.\n";
			
			if($nombre_comercial == "")
				$msg = $msg + "+ No ha Ingresado Nombre Comercial.\n";
			
			if($direccion == "")
				$msg = $msg + "+ No ha Ingresado Dirección.\n";
			
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
				
				$("#id_proveedor").val(0);
				$("#id_proveedor_categoria").val(0);
				$("#id_tipo_documento").val(0);
				$("#nro_documento").val("");
				$("#razon_social").val("");
				$("#nombre_comercial").val("");
				$("#direccion").val("");
				$("#telefonos").val("");
				$("#comentarios").val("");
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
				
				$id_proveedor = $fila.find(".id_proveedor").val();
				$id_proveedor_categoria = $fila.find(".id_proveedor_categoria").val();
				$id_tipo_documento = $fila.find(".id_tipo_documento").val();
				$nro_documento = $fila.find(".nro_documento").val();
				$razon_social= $fila.find(".razon_social").val();
				$nombre_comercial = $fila.find(".nombre_comercial").val();
				$telefonos = $fila.find(".telefonos").val();
				$direccion = $fila.find(".direccion").val();
				$comentarios = $fila.find(".comentarios").val();
				
				
				$("#id_proveedor").val($id_proveedor);
				$("#id_proveedor_categoria").val($id_proveedor_categoria);
				$("#id_tipo_documento").val($id_tipo_documento);
				$("#nro_documento").val($nro_documento);
				$("#razon_social").val($razon_social);
				$("#nombre_comercial").val($nombre_comercial);
				$("#telefonos").val($telefonos);
				$("#direccion").val($direccion);
				$("#comentarios").val($comentarios);
				
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
						<?php 
						if($permiso_crear_proveedor->isOK)
							echo "$(\"#proveedor\").submit();";
						else 
							echo "alert(\"No cuentas con Permiso para Crear Proveedores\")";
						?>
						}
						else
							alert($msg);
						break;
					case "3":
						$("#operacion").val("modificar");
						$msg = ValidarIngresoDatos();
						if($msg == "")
						{
						<?php 
						if($permiso_modificar_proveedor->isOK)
							echo "$(\"#proveedor\").submit();";
						else 
							echo "alert(\"No cuentas con Permiso para Modificar Proveedores\")";
							?>
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
		<form id="proveedor" name="proveedor" method="post" action="<?php echo $enlace_procesar; ?>">
			<input type="hidden" name="id_proveedor" id="id_proveedor" value="0"/>
			<input type="hidden" name="operacion" id ="operacion" />
			<input type="hidden" name="id_usuario" id ="id_usuario" value="<?php echo $id_usuario;?>" />
			
		<div id="titulo">ADMINISTRACIÓN DE PROVEEDORES</div>
		<div id="div_opcion_general" align="right">
			<span class="titulo_2">Opción General:</span>
			<select id="id_opcion_general" class="texto_3">
				<option value="1">Buscar Proveedor</option>
				<option value="2" <?php echo $permiso_crear_proveedor->isOK? "" : "disabled='disabled'";?>>Crear Proveedor</option>
				<option value="3" <?php echo $permiso_modificar_proveedor->isOK? "" : "disabled='disabled'";?>>Editar Proveedor</option>
			</select>
		</div>
		<div id="div_tabla_ingreso">
			<table id="tabla_ingreso">
				<tr>
					<td width=80px><div class="etiqueta">Categoría:</div></td>
					<td width=160px> 
						<select id="id_proveedor_categoria" name="id_proveedor_categoria" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							
							$lista_cat = $proBLO->ListarCategoriaTodas();
							
							foreach($lista_cat as $c)
								echo "<option value=\"$c->id\">".strtoupper($c->descripcion)."</option>";
							?>
						</select>
					</td>
					<td width="30px"></td>
					<td width="110px"><span class="etiqueta">Tipo Documento:</span></td>
					<td>
						<select id="id_tipo_documento" name="id_tipo_documento" class="texto_3">
							<option value="0">Seleccione...</option>
							<?php
							$lista = $tdBLO->ListarTodos();

							foreach($lista as $td)
								echo "<option value=\"$td->id\">".strtoupper($td->descripcion)."</option>";
							?>
						</select>
					</td>
					<td width="30px"></td>
					<td width=100px><span class="etiqueta">Nro. Documento:</span></td>
					<td><input type="text" id="nro_documento" name="nro_documento" value="" class="texto_3"/></td>
				</tr>
				<tr>
					<td><span class="etiqueta">Razón Social:</span></td>
					<td><input type="text" id="razon_social" name="razon_social" value="" class="texto_4_5"/></td>
					<td></td>
					<td><span class="etiqueta">Nombre Comercial:</span></td>
					<td><input type="text" id="nombre_comercial" name="nombre_comercial" value="" class="texto_4_5"/></td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td><span class="etiqueta">Dirección:</span></td>
					<td><input type="text" id="direccion" name="direccion" value="" class="texto_4_5"/></td>
					<td></td>
					<td><span class="etiqueta">Teléfonos:</span></td>
					<td><input type="text" id="telefonos" name="telefonos" value="" class="texto_3"/></td>
					<td></td>
					<td><span class="etiqueta">Comentarios:</span></td>
					<td><input type="text" id="comentarios" name="comentarios" value="" class="texto_4_5"/></td>
				</tr>
				<tr>
					<td colspan="8" align="right"><input type="button" class="texto_2" value="Limpiar Valores" id="btn_limpiar_valores" /></td>
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
					<th width=120px>Categoría</th>
					<th width=120px>Documento</th>					
					<th width=230px>Razón Social</th>
					<th width=130px>Nombre Comercial</th>
					<th width=200px>Dirección</th>
					<th width=100px>Teléfonos</th>
					<th width=100px>Comentarios</th>
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