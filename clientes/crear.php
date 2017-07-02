<?php

session_start();

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

include ('../clases/usuario.php');
include ('../clases/general.php');
include ('../clases/cliente.php');
include ('../clases/enc_dec.php');
//include ('../clases/cuenta_venta.php');
include ('../clases/opcion.php');
include ('../clases/centro.php');
include ('../clases/security.php');
include ("../clases/anuncio.php");

$enlace = "crear_cliente.php?usr_key=$usr_key";
$enlace_query_cliente = "../procesar_cliente.php";
$enlace_procesar = "../procesar_cliente.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";

/*$opcion_buscar_cliente = "ZT3C300Q";
$opcion_crear_cliente = "K5GS376O";
$opcion_modificar_cliente = "27M7N6ES";*/


$permiso_buscar_cliente = NULL;
$permiso_crear_cliente = NULL;
$permiso_modificar_cliente = NULL;


$cenBLO = new CentroBLO();
$opcBLO = new OpcionBLO();

$opcion_buscar_cliente = "ZT3C300Q";  //De Local
$permiso_buscar_cliente = $opcBLO->ValidarOpcionXIdUsuario($opcion_buscar_cliente, $id_usuario, $id_centro);	

$opcion_crear_cliente = "K5GS376O";  //De Local
$permiso_crear_cliente = $opcBLO->ValidarOpcionXIdUsuario($opcion_crear_cliente, $id_usuario, $id_centro);	

$opcion_modificar_cliente = "27M7N6ES";
$permiso_modificar_cliente = $opcBLO->ValidarOpcionXIdUsuario($opcion_modificar_cliente, $id_usuario, $id_centro);

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		<!--link rel="stylesheet" href="style.css" type="text/css" /-->
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		function CargarValores(id, id_tipo_documento, nro_documento, nombres, apellidos, telefonos_str, email)		
		{	
			$("#opcion").val(3);
			$("#btn_operacion").val("Guardar");
			
			$("#id_tipo_documento").val(id_tipo_documento);
			
			if($("#id_tipo_documento").val() == 2)
			{
				$(".fila_doc_natural").css("display","none");
				$(".fila_doc_juridica").css("display","block");
			}
			else
			{
				if($("#id_tipo_documento").val() > 0)
				{
					$(".fila_doc_natural").css("display","block");
					$(".fila_doc_juridica").css("display","none");	
				}				
			}
			
			/*$("#id_tipo_documento").attr("disabled","disabled");
			$("#nro_documento").attr("readonly","readonly");
			$("#nombres").attr("readonly","readonly");
			$("#apellidos").attr("readonly","readonly");
			$("#razon_social").attr("readonly","readonly");
			$("#email").attr("readonly","readonly");*/
			
			$("#id_cliente").val(id);
			$("#nro_documento").val(nro_documento);
			$("#nombres").val(nombres);
			$("#apellidos").val(apellidos);
			$("#razon_social").val(apellidos);
			$("#email").val(email);
			
			$(".contacto").css("display","block");				
			
			telefonos = telefonos_str.split(" - ");
			if(telefonos.length > 0)
			{
				$("#tabla_telefonos").css("display","block");
				$("#tabla_telefonos").empty();
				for(i = 0; i < telefonos.length; i++)
				{					
					$fila = "<tr><td>" + telefonos[i] + "<input class=\"telefono\" value=\"" + telefonos[i] + "\" type=\"hidden\"\>";
					$fila = $fila + "<div class=\"div_eliminar_telefono\" align=middle title=\"Eliminar Teléfono [" + telefonos[i] +"]\">X</div>"
					$fila = $fila + "</td></tr>";
					$("#tabla_telefonos").append($fila);
				}
			}

		
		}
		
		$(function()
		{
			$(".fila_doc_natural").css("display","none");
			$(".fila_doc_juridica").css("display","none");
			
			$opcion = $("#opcion").val();
			$(".contacto").css("display","block");
			switch($opcion)
			{
				case "1": 
					$("#btn_operacion").val("Buscar");
					$(".contacto").css("display","none");					 
					break;
				case "2":
					$("#btn_operacion").val("Crear"); break;
				case "3":
					$("#btn_operacion").val("Guardar"); break;
			}
			
			$("#opcion").change(function()
			{
				$opcion = $("#opcion").val();
				$(".contacto").css("display","block");
				
				switch($opcion)
				{
					case "1": 
						$("#btn_operacion").val("Buscar"); 
						$(".contacto").css("display","none");
						break;
					case "2":
						$("#btn_operacion").val("Crear");
						$("#div_nota").css("display","none");
						$("#tabla_resultado").css("display","none");
						break;
					case "3":
						$("#btn_operacion").val("Guardar"); break;
				}
			});
			
			$("#id_tipo_documento").change(function()
			{
				$id_tipo_documento = $(this).val();
				if($id_tipo_documento == 1 || $id_tipo_documento == 3)
				{					
					$(".fila_doc_natural").css("display","block");
					$(".fila_doc_juridica").css("display","none");
				}
				if($id_tipo_documento == 2)
				{
					$(".fila_doc_natural").css("display","none");
					$(".fila_doc_juridica").css("display","block");
				}
			});
			
			$("#btn_agregar_telefono").click(function()
			{
				$telefono = $("#telefono").val();
				
				$("#tabla_telefonos").css("display","block");
				$("#telefono").val("");
				//$fila = "<tr><td>" + $telefono;
				$fila = "<tr><td>" + $telefono + "<input class=\"telefono\" value=\"" + $telefono + "\" type=\"hidden\"\>";
				$fila = $fila + "<div class=\"div_eliminar_telefono\" align=middle title=\"Eliminar Teléfono [" + $telefono +"]\">X</div>"
				$fila = $fila + "</td></tr>";
				$("#tabla_telefonos").append($fila);
			});
			
			$(".div_eliminar_telefono").live('click',function()
			{
				$fila = $(this).parent().parent();
				$fila.remove();
				
				$nro_filas = $("#tabla_telefonos tr").length;
				
				if($nro_filas == 0)
					$("#tabla_telefonos").css("display","none");
				
			});
			
			$("#btn_operacion").click(function()
			{
				id_tipo_documento = $("#id_tipo_documento").val();
				nro_documento = $("#nro_documento").val();
				nombres = $("#nombres").val();
				apellidos = $("#apellidos").val();
				razon_social = $("#razon_social").val();
				
				$opcion = $("#opcion").val();
				
				if(id_tipo_documento > 0)
					id_tipo_documento = "&id_tipo_documento=" + id_tipo_documento;
				else
					id_tipo_documento = "";
						
				if(nro_documento != "")
					nro_documento = "&nro_documento=" + nro_documento;
				else
					nro_documento = "";
						
				if(apellidos != "")
					apellidos = "&apellidos=" + apellidos;
				else
					apellidos = "";
				
				if(razon_social != "")
					razon_social = "&apellidos=" + razon_social;
				else
					razon_social = "";
					
				if(nombres != "")
					nombres = "&nombres=" + nombres;
				else
					nombres = "";
						
				filtro = id_tipo_documento + nro_documento + apellidos + nombres;
				
				$("#keyword").attr("disabled","disabled"); 
					
				$("#tabla_resultado").css("display","none");
				$("#div_nota").css("display","none");
				
				if($opcion == "1") //Buscar
				{
					if($("#p_buscar_c").val() == 1)
					{
						if(filtro != "")
						{
							var url = "<?php echo $enlace_query_cliente;?>?operacion=query" + filtro;
							
							$.getJSON(url, function(data)
						    {
						    	$("#tabla_resultado").css("display","block");
						    	$("#div_nota").css("display","block");
							    $('#tabla_resultado > tbody').empty();
						    	if(data != null)
						    	{	
						    						    		
							    	$.each(data, function(key, val) 
									{
										
										$onclick = " onclick=\"CargarValores(" + val.id + "," + val.id_tipo_documento + ",'" + val.nro_documento + "','";
										$onclick = $onclick + val.nombres + "','" + val.apellidos + "','" + val.telefonos_str + "','" + val.email + "')\"";
										
										$tr = "<tr class=\"fila_por_seleccionar\"" + $onclick + ">";
										$tr = $tr + "<td align=\"center\">" + val.id + "</td>";
										$tr = $tr + "<td align=\"center\">" + val.tipo_documento + ": " + val.nro_documento + "</td>";
										$tr = $tr + "<td>" + val.nombres + " " + val.apellidos + "</td>";
										$tr = $tr + "<td align=\"center\">" + val.telefonos_str + "</td>";
										$tr = $tr + "<td>" + val.email + "</td>";																	
										$tr = $tr + "</tr>";
										$("#tabla_resultado").append($tr);
																				
									});					    	
						    	}
						    	else
						    	{
						    		$tr = "<tr><td colspan=5>No hay valores para la búsqueda</td></tr>";
						    		$("#tabla_resultado").append($tr);
						    	}	
						    });
						}
					}
					else
						alert("No cuenta con Permiso para realizar búsquedas de Clientes!");
					
				}
				
				if($opcion == 2 || $opcion == 3)
				{
					resultado = true;
					msg = "Se ha(n) encontrado inconsistencia(s) en el/los valores:\n"
					
					if($opcion == 3)
					{
						if($("#id_cliente") == 0)
						{
							resultado = false;
							msg = msg + "\n+No ha seleccionado ningún cliente!";
						}
					}
					
					if(id_tipo_documento == 0)
					{
						resultado = false;
						msg = msg + "\n+Tipo de Documento";	
					}
					if(nro_documento == "")
					{
						resultado = false;
						msg = msg + "\n+Nro. de Documento";	
					}
					if(apellidos == "" && id_tipo_documento == 1)
					{
						resultado = false;
						msg = msg +"\n+Apellidos/Razón Social";	
					}
					if(razon_social == "" && id_tipo_documento == 2)
					{
						resultado = false;
						msg = msg +"\n+Apellidos/Razón Social";	
					}
					
					if(email == "")
					{
						resultado = false;
						msg = msg +"\n+Email";	
					}
					if($(".telefono").length == 0)
					{
						resultado = false;
						msg = msg +"\n+Teléfono(s)!";	
					}
					else
					{
						var telefonos = "";
						$(".telefono").each(function()
						{							
							if(telefonos != "")
								telefonos = telefonos + "&*" + $(this).val();
							else
								telefonos = $(this).val();
						});
						
						$("#telefonos").val(telefonos);
					}
					switch($opcion)
					{
						case "2":
							$("#operacion").val("crear"); 
							if($("#p_crear_c").val() == 0)
							{
								resultado = false;
								msg = "No cuenta con Permisos para Crear Clientes!";
							}
							break;
						case "3":
						if($("#p_modificar_c").val() == 0)
							{
								resultado = false;
								msg = "No cuenta con Permisos para Modificar Clientes!";
							}
							$("#operacion").val("modificar"); break;
					}
					//alert($("#operacion").val());
					//document.cliente.operacion.value = $("#operacion").val();
					
					if(resultado)	
						$("#cliente").submit();
					else
						alert(msg);
						
				}
				
				
			});
			
			$(".fila_por_seleccionar").live("click", function()
			{
				
			});
			
			$(".limpiar_valores").click(function()
			{
				$("#id_tipo_documento").val(0);
				$("#nombres").val("");
				$("#apellidos").val("");
				$("#nro_documento").val("");
				$("#razon_social").val("");
				$("#keyword").val("");
				$("#telefono").val("");
				$("#email").val("");
				$("#id_cliente").val(0);
				$("#tabla_telefonos").css("display","none");
				$("#tabla_resultado").css("display","none");
				$("#div_nota").css("display","none");
			});
			
		});

		</script>
		<style media="screen" type="text/css">			
			body { background-color: #F1F1F1; }
			#div_main { padding-top: 10px; font-family: Helvetica; margin-bottom: 20px;  }
			#main { border: dotted 1px #3399FF; background-color: #FFFFFF; color: #585858; width: 1000px; overflow: hidden; margin: 0 auto; border-radius: 10px 10px 10px 10px; }
			#div_titulo { font-weight: bold; font-size:14px; margin-top: 10px; }
			#div_opciones { margin-top: 20px; float: right; margin-right: 10px;  }
			#div_cliente { margin-top: 10px; margin-left: 10px; margin-right: 10px;  }
			.titulo_seccion { font-weight: bold; font-size: 12px; }
			.etiqueta { font-family: Helvetica; font-size:12px; }
			.texto1 { font-size: 11px; width: 60px; font-family: Helvetica; }
			.texto2 { font-size: 11px; width: 100px; font-family: Helvetica; }
			.texto3 { font-size: 11px; width: 150px; font-family: Helvetica; }
			.texto4 { font-size: 11px; width: 200px; font-family: Helvetica; }
			#tabla_telefonos { width: 170px; background-color: #FFFFFF; border: dotted 1px #0099CC; font-size: 12px; display: none; }
			#tabla_telefonos td { width: 170px; font-size: 11px; font-family: Helvetica; }
			
			.div_eliminar_telefono { border-radius: 10px 10px 10px 10px; border: solid 2px #CE3333; color: #CE3333; font-size: 9px; 
				width: 10px; float: right; font-weight: bold; margin-right: 5px; }
			.div_eliminar_telefono:hover { background-color: #CE3333; color: #FFFFFF; cursor: pointer; }
			.contacto { display: none; }
			
			#tabla_resultado { border-collapse: collapse; display: none; border: solid 1px #0099CC; margin-right: 10px; background-color: #FFFFFF; }
			#tabla_resultado thead { font-size: 12px; font-weight: bold; color: #0099CC; }
			#tabla_resultado tr { font-size: 11px; border-bottom: dotted 1px #0099CC; }
			#tabla_resultado td { font-family: Helvetica; padding-left: 5px; }
			
			.fila_por_seleccionar:hover { background-color: #F2F39F; cursor: pointer; }
			
			#div_nota { font-size: 10px; float: right; display: none; margin-right: 40px; margin-bottom: 10px; }
			.limpiar_valores { color: #0099CC; text-decoration:underline; font-size: 11px; }
			.limpiar_valores:hover { cursor: pointer; }
		</style>
	</head>
	<body>
	
	<?php include("../header.php"); ?>
	<div id="div_main" align="center">
		<form id="cliente" name="cliente" action="<?php echo $enlace_procesar; ?>" method="POST" >
			<input type="hidden" name="operacion" id="operacion"/>				
			<input type="hidden" name="telefonos" id="telefonos" value=""/>
			<input type="hidden" name="id_cliente" id="id_cliente" value=""/>
			<input type="hidden" name="id_usuario" value="<?php echo $usuario->id;?>"/>
			<input type="hidden" id="p_buscar_c" value="<?php echo $permiso_buscar_cliente->isOK ? 1 : 0; ?>"/>
			<input type="hidden" id="p_crear_c" value="<?php echo $permiso_crear_cliente->isOK ? 1 : 0; ?>"/>
			<input type="hidden" id="p_modificar_c" value="<?php echo $permiso_modificar_cliente->isOK ? 1 : 0; ?>"/>
				
			<div id="main">
				<div id="div_titulo">
					<span style="">CREACION Y MODIFICACION DE CLIENTES</span>
				</div>
				<div style="width:998px; float: left;">
					<div id="div_opciones">
						<span style="font-size: 12px; ">Opción General:</span>
							<select id="opcion" name="opcion" class="texto3">
								<option value="1" <?php echo $permiso_buscar_cliente->isOK ? "" : "disabled=disabled";?> selected = 'selected'>Buscar Cliente</option>
								<option value="2" <?php echo $permiso_crear_cliente->isOK ? "" : "disabled=disabled";?>>Crear Cliente</option> 
								<option value="3" <?php echo $permiso_modificar_cliente->isOK ? "" : "disabled=disabled";?>>Editar Cliente</option>															
							</select>
						</div>
					</div>
					<div id="div_cliente">
						<table id="tb_cliente">
							<tr><td colspan="6"><hr></td></tr>						
							<tr><td colspan="6" width="200px"><span class="titulo_seccion">Datos del Cliente:</span></td></tr>
							<tr>
								<td width=130px>
									<span class="etiqueta">Tipo de Documento:</span>
								</td>
								<td width=300px>								
									<select name="id_tipo_documento" id="id_tipo_documento" class="texto3"> 								
										<option>Seleccione...</option>
										<?php
											$tipodocBLO = new TipoDocumentoBLO();
											$tipos = $tipodocBLO->Listar('');
											if(count($tipos) > 0)
											{
												foreach ($tipos as $t)
												{
													if($t->id == $tipo_documento)
														$selected = "selected = 'selected'";
													else
														$selected = "";  									
													echo "<option value='$t->id' $selected>$t->descripcion</option>";
												}
											}	
										?>
									</select>
								</td>
								<td width=130px><span class="etiqueta">Nro de Documento:</span></td>
								<td width=200px><input name="nro_documento" id="nro_documento" class="texto2" maxlength="20" /></td>
								<td colspan=2></td>
							</tr>
							<tr>
								<td ><div class="fila_doc_natural"><span class="etiqueta">Nombres:</span></div></td>
								<td width=300px><div class="fila_doc_natural"><input name="nombres" id="nombres" class="texto3" maxlength="50" /></div></td>
								<td><div class="fila_doc_natural"><span class="etiqueta">Apellidos:</span></div></td>
								<td width=250px><div class="fila_doc_natural"><input name="apellidos" id="apellidos" class="texto3" maxlength="50" /></div></td>							
								<td><div class="fila_doc_natural"><span class="etiqueta">Keyword:</span></div></td>
								<td width=170px><div class="fila_doc_natural"><input name="keyword" id="keyword" class="texto2" maxlength="20" /></div></td>
							</tr>
							<tr>
								<td><div class="fila_doc_juridica"><span class="etiqueta">Razon Social:</span></div></td>
								<td width=250px ><div class="fila_doc_juridica"><input name="razon_social" id="razon_social" class="texto4" maxlength="100" /></div></td>
								<td><div class="fila_doc_juridica"><span class="etiqueta">Keyword:</span></div></td>
								<td width=170px><div class="fila_doc_juridica"><input name="keyword" id="keyword" class="texto2" maxlength="20" /></div></td>
								<td colspan="2"></td>
							</tr>
							
							<tr><td colspan="6"><hr></td></tr>
							<tr><td colspan="6" width="200px"><div class="contacto"><span class="titulo_seccion">Datos de Contacto:</span></div></td></tr>
							<tr>
								<td><div class="contacto"><span class="etiqueta">Email:</span></div></td>
								<td width=200px><div class="contacto"><input name="email" id="email" class="texto4" maxlength="40" /></div></td>
								<td><div class="contacto"><span class="etiqueta">Teléfono:</span></div></td>
								<td width=200px><div class="contacto"><input name="telefono" id="telefono" class="texto2" maxlength="15" />
									<input type="button" class="texto1" id="btn_agregar_telefono" value="Agregar"/></div>
								</td>
								<td colspan="2"></td>
							</tr>
							
							<tr>
								<td colspan="3"></td>
								<td colspan="3">
									<div class="contacto">
										<table id="tabla_telefonos">
										
										</table>
									</div>
								</td>
								
							</tr>
							<tr><td colspan="6"><div class="contacto"><hr></div></td></tr>
							<tr>
								<td colspan="6" align="left"><span class="limpiar_valores">Limpiar Valores</span></td>							
							</tr>
							<tr>
								<td colspan=6 align=center>
									<input id="btn_operacion" type="button" class="texto2" value="">
									<input id="btn_cancelar" type="button" class="texto2" value="Cancelar">
								</td>
							</tr>
							<tr height="10px"></tr>						
							<tr>
								<td colspan="6" align="middle">
									<div id="div_nota">Dar click en la fila para seleccionar al cliente.</div>
									<div style="float: left;">
									<table id="tabla_resultado">
										<thead>
											<th width=50px>Id</th>
											<th width=150px>Documento</th>
											<th width=300px>Nombres y Apellidos</th>
											<th width=230px>Teléfono(s)</th>
											<th width=200px>Correo</th>
										</thead>
									</table>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
					
			</form>
		</div>	
		
	</body>
</html>