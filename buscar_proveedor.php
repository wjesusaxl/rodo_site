<?php

session_start();

include ('../clases/general.php');
include ('../clases/proveedor.php');
include ('../clases/usuario.php');
include ('../clases/tipo_documento.php');

//include ('security.php');

$enlace = $_SERVER["PHP_SELF"]."?key=$usr_key";
$ruta_imagenes = "../images";
$enlace_query_proveedor = "../procesar_proveedor.php";

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
		
		<script type="text/javascript">
		
			function IsNumeric(expression)
			{
			    return (String(expression).search(/^\d+$/) != -1);
			}
			
			$(function()
			{
				$("#nro_documento").keyup(function()
				{
					id_tipo_documento = $("#id_tipo_documento").val();
					nro_documento = $("#nro_documento").val();
					razon_social = $("#razon_social").val();
					nombre_comercial = $("#nombre_comercial").val();
					if(id_tipo_documento > 0)
						id_tipo_documento = "&id_tipo_documento=" + id_tipo_documento;
					else
						id_tipo_documento = "";
						
					if(nro_documento != "")
						nro_documento = "&nro_documento=" + nro_documento;
					else
						nro_documento = "";
						
					if(nombre_comercial != "")
						nombre_comercial = "&nombre_comercial=" + nombre_comercial;
					else
						nombre_comercial = "";
						
					if(razon_social != "")
						razon_social = "&razon_social=" + razon_social;
					else
						razon_social = "";
						
					filtro = id_tipo_documento + nro_documento + nombre_comercial + razon_social; 
					
					$("#div_tabla_resultado").css("display","none");
					
					if(filtro != "")
					{
						var url = "<?php echo $enlace_query_proveedor;?>?operacion=query" + id_tipo_documento + nro_documento + nombre_comercial + razon_social;
					
						$.getJSON(url, function(data)
					    {
					    	if(data != null)
					    	{
					    		$("#div_tabla_resultado").css("display","block");
					    		$('#tabla_resultado > tbody').empty();
					    		$.each(data, function(key, val) 
								{									
									$tr = "<tr class=\"fila_seleccionada\">";
									$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_proveedor\" value=\"" + val.id + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"id_tipo_documento\" value=\"" + val.id_tipo_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"nro_documento\" value=\"" + val.nro_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"razon_social\" value=\"" + val.razon_social;
									$tr  = $tr + "\"/>" + val.tipo_documento + "</td>";
									$tr = $tr + "<td align='center'>" + val.nro_documento + "</td>";
									$tr = $tr + "<td>" + val.razon_social + "</td>";
									$tr = $tr + "<td>" + val.nombre_comercial + "</td>";
									$tr = $tr + "<td>" + val.telefonos + "</td>";
									$tr = $tr + "</tr>";
									$("#tabla_resultado").append($tr);
								});
																
					    	}					    	
					    });					
					}
				});
				
				$("#razon_social").keyup(function()
				{
					id_tipo_documento = $("#id_tipo_documento").val();
					nro_documento = $("#nro_documento").val();
					razon_social = $("#razon_social").val();
					nombre_comercial = $("#nombre_comercial").val();
					if(id_tipo_documento > 0)
						id_tipo_documento = "&id_tipo_documento=" + id_tipo_documento;
					else
						id_tipo_documento = "";
						
					if(nro_documento != "")
						nro_documento = "&nro_documento=" + nro_documento;
					else
						nro_documento = "";
						
					if(nombre_comercial != "")
						nombre_comercial = "&nombre_comercial=" + nombre_comercial;
					else
						nombre_comercial = "";
						
					if(razon_social != "")
						razon_social = "&razon_social=" + razon_social;
					else
						razon_social = "";
						
					filtro = id_tipo_documento + nro_documento + nombre_comercial + razon_social; 
					
					$("#div_tabla_resultado").css("display","none");
					
					if(filtro != "")
					{
						var url = "<?php echo $enlace_query_proveedor;?>?operacion=query" + id_tipo_documento + nro_documento + nombre_comercial + razon_social;
					
						$.getJSON(url, function(data)
					    {
					    	if(data != null)
					    	{
					    		$("#div_tabla_resultado").css("display","block");
					    		$('#tabla_resultado > tbody').empty();
					    		$.each(data, function(key, val) 
								{									
									$tr = "<tr class=\"fila_seleccionada\">";
									$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_proveedor\" value=\"" + val.id + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"id_tipo_documento\" value=\"" + val.id_tipo_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"nro_documento\" value=\"" + val.nro_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"razon_social\" value=\"" + val.razon_social;
									$tr  = $tr + "\"/>" + val.tipo_documento + "</td>";
									$tr = $tr + "<td align='center'>" + val.nro_documento + "</td>";
									$tr = $tr + "<td>" + val.razon_social + "</td>";
									$tr = $tr + "<td>" + val.nombre_comercial + "</td>";
									$tr = $tr + "<td>" + val.telefonos + "</td>";
									$tr = $tr + "</tr>";
									$("#tabla_resultado").append($tr);
								});
																
					    	}					    	
					    });					
					}
					
				});	
				
				$("#id_tipo_documento").change(function()
				{
					id_tipo_documento = $("#id_tipo_documento").val();
					nro_documento = $("#nro_documento").val();
					razon_social = $("#razon_social").val();
					nombre_comercial = $("#nombre_comercial").val();
					if(id_tipo_documento > 0)
						id_tipo_documento = "&id_tipo_documento=" + id_tipo_documento;
					else
						id_tipo_documento = "";
						
					if(nro_documento != "")
						nro_documento = "&nro_documento=" + nro_documento;
					else
						nro_documento = "";
						
					if(nombre_comercial != "")
						nombre_comercial = "&nombre_comercial=" + nombre_comercial;
					else
						nombre_comercial = "";
						
					if(razon_social != "")
						razon_social = "&razon_social=" + razon_social;
					else
						razon_social = "";
						
					filtro = id_tipo_documento + nro_documento + nombre_comercial + razon_social; 
					
					$("#div_tabla_resultado").css("display","none");
					
					if(filtro != "")
					{
						var url = "<?php echo $enlace_query_proveedor;?>?operacion=query" + id_tipo_documento + nro_documento + nombre_comercial + razon_social;
					
						$.getJSON(url, function(data)
					    {
					    	if(data != null)
					    	{
					    		$("#div_tabla_resultado").css("display","block");
					    		$('#tabla_resultado > tbody').empty();
					    		$.each(data, function(key, val) 
								{									
									$tr = "<tr class=\"fila_seleccionada\">";
									$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_proveedor\" value=\"" + val.id + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"id_tipo_documento\" value=\"" + val.id_tipo_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"nro_documento\" value=\"" + val.nro_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"razon_social\" value=\"" + val.razon_social;
									$tr  = $tr + "\"/>" + val.tipo_documento + "</td>";
									$tr = $tr + "<td align='center'>" + val.nro_documento + "</td>";
									$tr = $tr + "<td>" + val.razon_social + "</td>";
									$tr = $tr + "<td>" + val.nombre_comercial + "</td>";
									$tr = $tr + "<td>" + val.telefonos + "</td>";
									$tr = $tr + "</tr>";
									$("#tabla_resultado").append($tr);
								});
																
					    	}					    	
					    });					
					}
					
				});
				
				$("#nombre_comercial").keyup(function()
				{
					
					id_tipo_documento = $("#id_tipo_documento").val();
					nro_documento = $("#nro_documento").val();
					razon_social = $("#razon_social").val();
					nombre_comercial = $("#nombre_comercial").val();
					if(id_tipo_documento > 0)
						id_tipo_documento = "&id_tipo_documento=" + id_tipo_documento;
					else
						id_tipo_documento = "";
						
					if(nro_documento != "")
						nro_documento = "&nro_documento=" + nro_documento;
					else
						nro_documento = "";
						
					if(nombre_comercial != "")
						nombre_comercial = "&nombre_comercial=" + nombre_comercial;
					else
						nombre_comercial = "";
						
					if(razon_social != "")
						razon_social = "&razon_social=" + razon_social;
					else
						razon_social = "";
						
					filtro = id_tipo_documento + nro_documento + nombre_comercial + razon_social; 
					
					$("#div_tabla_resultado").css("display","none");
					
					if(filtro != "")
					{
						var url = "<?php echo $enlace_query_proveedor;?>?operacion=query" + id_tipo_documento + nro_documento + nombre_comercial + razon_social;
					
						$.getJSON(url, function(data)
					    {
					    	if(data != null)
					    	{
					    		$("#div_tabla_resultado").css("display","block");
					    		$('#tabla_resultado > tbody').empty();
					    		$.each(data, function(key, val) 
								{									
									$tr = "<tr class=\"fila_seleccionada\">";
									$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_proveedor\" value=\"" + val.id + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"id_tipo_documento\" value=\"" + val.id_tipo_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"nro_documento\" value=\"" + val.nro_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"razon_social\" value=\"" + val.razon_social;
									$tr  = $tr + "\"/>" + val.tipo_documento + "</td>";
									$tr = $tr + "<td align='center'>" + val.nro_documento + "</td>";
									$tr = $tr + "<td>" + val.razon_social + "</td>";
									$tr = $tr + "<td>" + val.nombre_comercial + "</td>";
									$tr = $tr + "<td>" + val.telefonos + "</td>";
									$tr = $tr + "</tr>";
									$("#tabla_resultado").append($tr);
								});
																
					    	}					    	
					    });					
					}
					
				});
				
				$("#btn_buscar").click(function()
				{
					id_tipo_documento = $("#id_tipo_documento").val();
					nro_documento = $("#nro_documento").val();
					nombre_comercial = $("#nombre_comercial").val();
					razon_social = $("#razon_social").val();
					if(id_tipo_documento > 0)
						id_tipo_documento = "&id_tipo_documento=" + id_tipo_documento;
					else
						id_tipo_documento = "";
						
					if(nro_documento != "")
						nro_documento = "&nro_documento=" + nro_documento;
					else
						nro_documento = "";
						
					if(nombre_comercial != "")
						nombre_comercial = "&nombre_comercial=" + nombre_comercial;
					else
						nombre_comercial = "";
						
					if(razon_social != "")
						razon_social = "&razon_social=" + nombre_comercial;
					else
						razon_social = "";
						
					filtro = id_tipo_documento + nro_documento + nombre_comercial + razon_social; 
					
					$("#div_tabla_resultado").css("display","none");
					
					if(filtro != "")
					{
						var url = "<?php echo $enlace_query_proveedor;?>?operacion=query" + id_tipo_documento + nro_documento + nombre_comercial + razon_social;
					
						$.getJSON(url, function(data)
					    {
					    	if(data != null)
					    	{
					    		$("#div_tabla_resultado").css("display","block");
					    		$('#tabla_resultado > tbody').empty();
					    		$.each(data, function(key, val) 
								{									
									$tr = "<tr class=\"fila_seleccionada\">";
									$tr = $tr + "<td align='center'><input type=\"hidden\" class=\"id_proveedor\" value=\"" + val.id + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"id_tipo_documento\" value=\"" + val.id_tipo_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"nro_documento\" value=\"" + val.nro_documento + "\"/>";
									$tr = $tr + "<input type=\"hidden\" class=\"razon_social\" value=\"" + val.razon_social;
									$tr  = $tr + "\"/>" + val.tipo_documento + "</td>";
									$tr = $tr + "<td align='center'>" + val.nro_documento + "</td>";
									$tr = $tr + "<td>" + val.razon_social + "</td>";
									$tr = $tr + "<td>" + val.nombre_comercial + "</td>";
									$tr = $tr + "<td>" + val.telefonos + "</td>";
									$tr = $tr + "</tr>";
									$("#tabla_resultado").append($tr);
																		
								});								
					    	}					    	
					    });					
					}
					else
						alert("Debes seleccionar o Ingresar al menos un valor!");				
				});
				
				$(".fila_seleccionada").live("click", function()
				{
					$tr = $(this);
					//$.cookie("id_cliente", null);
					$id_proveedor = $tr.find(".id_proveedor").val();
					$razon_social = $tr.find(".razon_social").val();
					$id_tipo_documento = $tr.find(".id_tipo_documento").val();
					$nro_documento = $tr.find(".nro_documento").val();
					
					$("#id_proveedor").val($id_proveedor);
					$("#razon_social").val($razon_social);
					$("#id_tipo_documento").val($id_tipo_documento);
					$("#nro_documento").val($nro_documento);
					//$.cookie("id_clientex", $id_cliente);
					//$("#operacion").val("cerrar");
					self.close();
					
				});
			});
			
			function terminate()
			{
				/*if(document.getElementById("operacion").value = "cerrar")
					self.close();*/	
				var o = new Object();
				o.id_proveedor = document.getElementById("id_proveedor").value;
				o.razon_social = document.getElementById("razon_social").value;
				o.id_tipo_documento = document.getElementById("id_tipo_documento").value;
				o.nro_documento = document.getElementById("nro_documento").value;
				window.returnValue = o;

			}
			
				

		</script>
		<style type="text/css">
			/*table.resultado { border-left: dotted 1px #3399FF; border-right: dotted 1px #3399FF; }
			table.resultado td { border-bottom: dotted 1px #3399FF; border-spacing: 0px; }*/
			#div_main { float: left; border: dotted 1px #3399FF; width:752px; background-color: #E6F2FF; color: #585858; 
				font-family: Helvetica; margin-left: 10px; margin-bottom: 20px; }
			#div_tabla_resultado { display: none; }
			
			#tabla_resultado { width:750px; font-family:Helvetica; font-size:11px; border-collapse: collapse;}
			#tabla_resultado th { font-weight: bold; background-color: #3399FF; color: #FFFFFF; }
			#tabla_resultado td { border: dotted #3399FF 1px; padding-left: 5px; }
			.texto1 { font-size: 11px; width: 70px; }
			.texto2 { font-size: 11px; width: 100px; }			
			.texto3 { font-size: 11px; width: 200px; }
			.fila_seleccionada:hover { background-color: #F2F39F; cursor: pointer; }
			#div_nota { float: right; font-size: 10px; }
		</style>

	</head>
	<body onbeforeunload="terminate()">		
		<div style="float: left;" id="div_main">
			<form name="cliente" action="<?php echo $enlace; ?>" method="POST">
				<input type="hidden" id="operacion" name="operacion" />
				<input type="hidden" name="id_proveedor" id="id_proveedor" />
				<input type="hidden" name="razon_social" id="razon_social" />
				<div id="div_formulario">
					<table style="">
						<tr>
							<td colspan="2">
								<div style="float: left; width: 400px;">
									<img src="<?php echo $ruta_imagenes;?>/logo-delocal.png" style="width:63px; height:72px;"/></br>
									<span style="font-size:11px; font-weight: bold">Ingrese al menos un valor para la búsqueda de proveedores:</span>	
								</div>						
							</td>					
						</tr>
						<tr height="5px;">
							
						</tr>
						<tr>
							<td width="150px;">
								<span style="font-weight: bold; font-size:11px;">Tipo de Documento:</span>
							</td>
							<td >
								<select name="id_tipo_documento" id="id_tipo_documento" class="texto3">
									<option style="color: #585858;" value=0>Seleccione...</option>
									<?php
																	
										$tipodocBLO = new TipoDocumentoBLO();
										
										$tipos = $tipodocBLO->ListarTodos();
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
						</tr>
						<tr>
							<td>
								<span style="font-weight: bold; font-size:11px;">Nro de Documento:</span>
							</td>
							<td>
								<input name="nro_documento" id="nro_documento" value="<?php echo $nro_documento; ?>" class="texto3" maxlength="20"/>
							</td>
						</tr>
						<tr>
							<td><span style="font-weight: bold; font-size:11px;">Raz. Social:</span></td>
							<td><input name="razon_social" id="razon_social" value="<?php echo $razon_social; ?>" class="texto3" maxlength="50"/></td>
						</tr>
						<tr>
							<td><span style="font-weight: bold; font-size:11px;">Nombre Comercial:</span></td>
							<td><input name="nombre_comercial" id="nombre_comercial" value="<?php echo $nombre_comercial; ?>" class="texto3" maxlength="50"/></td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="button" id="btn_buscar" style="font-size: 11px;" value="Buscar" />
								<input type="button" class="clase12" style="font-size: 11px;" value="Cerrar" onclick="self.close()"/>
							</td>
						</tr>
						
					</table>
				</div>
				<div id="div_tabla_resultado">
					<div id="div_nota">Dar click en la fila para seleccionar al Proveedor y volver a la página anterior.</div>
					<table id="tabla_resultado" style="">
						<thead>
							<th align="center">Tipo Doc.</td>
							<th align="center">Nro Doc.</td>
							<th align="center">Razón Social</th>
							<th align="center">Nombre Comercial</td>											
							<th align="center">Teléfono(s)</td>
								
								
								
									
						</thead>
					</table>
				</div>
			</div>
		</form>
	</body>
</html>
