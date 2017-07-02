<?php
/**********************TESTING*******************************/
/*	include ('../clases/general.php');
	include ('../clases/cliente.php');*/
/***********************************************************/

/*********************PATHS********************************
$cliente_buscar_enlace_post = "/procesar_cliente.php";
$cliente_ruta_imagenes = "images"
$cliente_buscar_query_cliente = "/procesar_cliente.php";
$cliente_tipo_fuente_externa = "reservas_cancha_cliente_buscar";

/***********************************************************/

/*********************ELEMENTS********************************
cliente_div_main: Main CLIENTE dialog div 
cliente_id_cliente: Client Id
cliente_nombre_cliente: Cliente nombre
			

/***********************************************************/


/*********************SCRIPTS********************************

$(document).live("ajaxStop", function (e) 
{
	$("#cliente_div_main").dialog("option", "position", "center");
													
});

 /**********************************************************/
?>

<script type="text/javascript">
	function IsNumeric(expression)
	{
	    return (String(expression).search(/^\d+$/) != -1);
	}
	
	function populateClienteTable(){
		
		cliente_id_tipo_documento = $("#cliente_id_tipo_documento").val();
		cliente_nro_documento = $("#cliente_nro_documento").val();
		cliente_apellidos = $("#cliente_apellidos").val();
		
		if(cliente_id_tipo_documento > 0)
			cliente_id_tipo_documento = "&id_tipo_documento=" + cliente_id_tipo_documento;
		else
			cliente_id_tipo_documento = "";
			
		if(cliente_nro_documento != "")
			cliente_nro_documento = "&nro_documento=" + cliente_nro_documento;
		else
			cliente_nro_documento = "";
			
		if(cliente_apellidos != "")
			cliente_apellidos = "&apellidos=" + cliente_apellidos;
		else
			cliente_apellidos = "";
				
		filtro = cliente_id_tipo_documento + cliente_nro_documento + cliente_apellidos; 
				
		$("#cliente_div_tabla_resultado").css("display","none");
				
		if(filtro != "")
		{
			var url = "<?php echo $cliente_buscar_query_cliente;?>?operacion=query" + cliente_id_tipo_documento + cliente_nro_documento + cliente_apellidos;
				
			$.getJSON(url, function(data)
		    {
				if(data != null)
	    		{
		    		$("#cliente_div_tabla_resultado").css("display","block");
		    		$('#cliente_tabla_resultado > tbody').empty();
		    		$.each(data, function(key, val) 
					{									
						$tr = "<tr class=\"cliente_fila_seleccionada\">";
						$tr = $tr + "<td><input type=\"hidden\" class=\"cliente_id_cliente\" value=\"" + val.id + "\"/>";
						$tr = $tr + "<input type=\"hidden\" class=\"cliente_nombre_cliente\" value=\"" + val.nombres + " " + val.apellidos;
						$tr  = $tr + "\"/>" + val.tipo_documento + "</td>";
						$tr = $tr + "<td>" + val.nro_documento + "</td>";
						$tr = $tr + "<td>" + val.nombres + " " + val.apellidos + "</td>";
						$tr = $tr + "<td>" + val.telefonos_str + "</td>";
						$tr = $tr + "<td>" + val.email + "</td>";
						$tr = $tr + "<td>" + val.keyword + "</td>";
						$tr = $tr + "<td>" + val.usuario_creacion + "</td>";
						$tr = $tr + "</tr>";
						$("#cliente_tabla_resultado").append($tr);
					});
															
		    	}					    	
		    });
		}
		   
			
	}
	
	function dlg_cerrar(valor)
	{
		if(valor == 0)
		{
			$("#cliente_id_cliente").val("");
	    	$("#cliente_nombre_cliente").val("");
		}
	    $("#cliente_operacion").val("");
	    $("#cliente_id_tipo_documento").val(0);
	    $("#cliente_nro_documento").val("");
	    $("#cliente_apellidos").val("");
	    $("#cliente_div_tabla_resultado").css("display","none");	    
	    $('#cliente_tabla_resultado > tbody').empty();
	    
	    <?php
	    if($cliente_tipo_fuente_externa == "reservas_cancha_cliente_buscar" || $cliente_tipo_fuente_externa == "reservas_cancha_cliente_frecuente_buscar")
		{?>					
			$('#id_cliente').val($("#cliente_id_cliente").val());
			$('#nombre_cliente').val($("#cliente_nombre_cliente").val());
		<?php
		}
		if($cliente_tipo_fuente_externa == "ventas_bar_cliente_buscar")
		{?>
			if($("#cliente_id_cliente").val() != "")
			{			
				ActualizarCuentaCliente($("#cliente_id_cliente").val());
			}
		<?php
		}
		?>	
	    $("#cliente_div_main").dialog('close');
	}
	
	
			
	$(function()
	{
		$("#cliente_nro_documento").keyup(function()
		{
			populateClienteTable();
		});
				
		$("#cliente_apellidos").keyup(function()
		{
			populateClienteTable();
					
		});	
				
		$("#cliente_id_tipo_documento").change(function()
		{
						
		});
				
		$("#cliente_btn_buscar").click(function()
		{
			populateClienteTable();				
		});
				
		$(".cliente_fila_seleccionada").live("click", function()
		{
			$tr = $(this);
			$cliente_id_cliente = $tr.find(".cliente_id_cliente").val();
			$cliente_nombre_cliente = $tr.find(".cliente_nombre_cliente").val();
					
			$("#cliente_id_cliente").val($cliente_id_cliente);
			$("#cliente_nombre_cliente").val($cliente_nombre_cliente);
			//$.cookie("cliente_id_clientex", $cliente_id_cliente);
			//$("#cliente_operacion").val("cerrar");
			
			//$("#cliente_div_main").dialog('close');
	    	//$("#cliente_div_main").dialog('destroy');
			
			dlg_cerrar(1);
					
		});
		
		$('#cliente_div_main').dialog(
		{	
		 	autoOpen: false,
            dialogClass: 'cls_cliente_div_main',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto'/*,
            close: function (event, ui) 
    		{
    			$(this).dialog("destroy");
    		}*/
		});

		
		
		
		
	});
			
	
			
				

</script>
<style type="text/css">
		/*table.resultado { border-left: dotted 1px #3399FF; border-right: dotted 1px #3399FF; }
			table.resultado td { border-bottom: dotted 1px #3399FF; border-spacing: 0px; }*/
	#cliente_div_main { float: left; border: dotted 1px #3399FF; width: 952px; background-color: #E6F2FF; color: #585858; 
		font-family: Helvetica; margin: 10px 10px 10px 10px; }
	#cliente_div_tabla_resultado { display: none; }
			
	#cliente_tabla_resultado { width:950px; font-family:Helvetica; font-size:11px; border-collapse: collapse;}
	#cliente_tabla_resultado th { font-weight: bold; background-color: #3399FF; color: #FFFFFF; }
	#cliente_tabla_resultado td { border: dotted #3399FF 1px; padding-left: 5px; }
	.cliente_texto1 { font-size: 11px; width: 70px; }
	.cliente_texto2 { font-size: 11px; width: 100px; }			
	.cliente_texto3 { font-size: 11px; width: 200px; }
	.cliente_fila_seleccionada:hover { background-color: #F2F39F; cursor: pointer; }
	#cliente_div_nota { float: right; font-size: 10px; }
	#dlg_cliente { border-radius: 10px 10px 10px 10px; border: dotted #0099CC 1px; }
	/*.no-titlebar .ui-dialog-titlebar { display: none; }
	.cls_cliente_div_nota .ui-widget-content { min-width: 500px; }*/
	.cls_cliente_div_main .ui-dialog-titlebar { display: none; }
	.cls_cliente_div_main .ui-widget-content { min-width: 970px; }
</style>



<div style="float: left;" id="cliente_div_main">
	<form name="cliente" action="<?php echo $cliente_buscar_enlace_post; ?>" method="POST">
		<input type="hidden" id="cliente_operacion" name="cliente_operacion" />
		<input type="hidden" id="cliente_id_cliente" name="cliente_id_cliente"/>
		<input type="hidden" id="cliente_nombre_cliente" name="cliente_nombre_cliente"/>
		<div id="cliente_div_formulario">
			<table style="">
				<tr>
					<td colspan="2">
						<div style="float: left; width: 400px;">
							<img src="<?php echo $cliente_ruta_imagenes;?>/logo-delocal.png" style="width:63px; height:72px;"/></br>
							<span style="font-size:11px; font-weight: bold">Ingrese al menos un valor para la búsqueda de clientes:</span>	
							
						</div>						
					</td>					
				</tr>
				<tr height="5px;"></tr>
				<tr>
					<td width="150px;">
						<span style="font-weight: bold; font-size:11px;">Tipo de Documento:</span>
					</td>
					<td >
						<select name="cliente_id_tipo_documento" id="cliente_id_tipo_documento" class="cliente_texto3">
							<option style="color: #585858;" value=0>Seleccione...</option>
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
				</tr>
				<tr>
					<td>
						<span style="font-weight: bold; font-size:11px;">Nro de Documento:</span>
					</td>
					<td>
						<input name="cliente_nro_documento" id="cliente_nro_documento" value="<?php echo $cliente_nro_documento; ?>" class="cliente_texto3" maxlength="20"/>
					</td>
				</tr>
				<tr>
					<td><span style="font-weight: bold; font-size:11px;">Raz. Social o Apellidos:</span></td>
					<td><input name="cliente_apellidos" id="cliente_apellidos" value="<?php echo $cliente_apellidos; ?>" class="cliente_texto3" maxlength="50"/></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="button" id="cliente_btn_buscar" style="font-size: 11px;" value="Buscar" />
						<input type="button" class="clase12" style="font-size: 11px;" value="Cerrar" onclick="dlg_cerrar(0)"/>
					</td>
				</tr>
						
			</table>
		</div>
		<div id="cliente_div_tabla_resultado">
			<div id="div_nota">Dar click en la fila para seleccionar al cliente y volver a la página anterior.</div>
				
			
			<table id="cliente_tabla_resultado" style="">
				<thead>
					<th align="center">Tipo Doc.</th>
					<th align="center">Nro Doc.</th>
					<th align="center">Nombres y Apellidos</th>											
					<th align="center">Teléfono(s)</th>
					<th align="center">Email</th>
					<th align="center">Keyword</th>
					<th align="center">Creado por</th>
				</thead>
			</table>
		</div>
	
	</form>
</div>


