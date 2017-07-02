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
div_cliente: Main CLIENTE dialog div 
cliente_id_cliente: Client Id
cliente_nombre_cliente: Cliente nombre
			

/***********************************************************/


/*********************SCRIPTS********************************

$(document).live("ajaxStop", function (e) 
{
	$("#div_cliente").dialog("option", "position", "center");
													
});

 /**********************************************************/
?>

<script type="text/javascript">
	function IsNumeric(expression)
	{
	    return (String(expression).search(/^\d+$/) != -1);
	}
	
	
	function dlg_cerrar(valor)
	{
		$("#div_cliente_nuevo").dialog('close');
	}
	
	
			
	$(function()
	{
		var resp;
		jQuery.validator.addMethod(
			"rucApellido",
			function(value,element){
				$id_tipo_documento = $("#cliente_id_tipo_documento").val();				
				if($id_tipo_documento == 1)				
					resp = (value == "") ? false : true;
				else
					resp = true;				
				return resp;
			},
			"Persona Natural requiere Nombres y Apellidos"
		);

		jQuery.validator.addMethod(
		    "telefono1",
            function(value, element){
		        $telefono1 = $("#cliente_telefono1").val();
		        if($telefono1 != "")
                    resp = true;
		        else
		            resp = false;
		        return resp;
            },
            "Se requiere al menos un telefono"
        )
		
		
		
		$('#cliente').validate({
			lang: 'ES',
			errorClass: "my-error-class",
	        rules: {
	            cliente_nombres: { required: true },
	            cliente_apellidos: { rucApellido: true },
	            cliente_telefono1: { telefono1: true }
	        }
    	});
		
		$('#div_cliente_nuevo').dialog(
		{	
		 	autoOpen: false,
            //dialogClass: 'cls_div_cliente',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            title: "Informacion de Cliente Nuevo"
		});

		
		$("#cliente_btn_crear").click(function()
		{
			if($("#cliente").valid())
			{
				$id_tipo_documento = $("#cliente_id_tipo_documento").val();
				$nro_documento = $("#cliente_nro_documento").val();
				$nombres = $("#cliente_nombres").val();
				$apellidos = $("#cliente_apellidos").val();
				$telefono1 = $("#cliente_telefono1").val();
				$telefono2 = $("#cliente_telefono2").val();
				$id_usuario = $("#id_usuario").val();
				$telefonos = $telefono1;
				if($telefono2 != "" && $telefono1 != $telefono2)
					$telefonos = $telefonos + "&*" + $telefono2;
					
				$.ajax({
					type: "POST",
					/*async: false,
					timeout: 10000,*/
					url:"<?php echo $cliente_buscar_query_cliente;?>",
					//url: "../procesar_comprobante_pago.php",
					data: { 
						id_tipo_documento: $id_tipo_documento,
						nro_documento: $nro_documento,
						nombres: $nombres,						 
						apellidos: $apellidos,
						telefonos: $telefonos,
						operacion: "crear_simple",
						//operacion2: "crear",
						id_usuario: $id_usuario
						},
					datatype: 'json',
					success: function(data) { 
    					response = jQuery.parseJSON(data);
                        if(response.isOK == true)
                        {
                            if($("#div_reserva_nueva").dialog("isOpen") === true)
                            {
                                console.log("Nuevo");
                                $("#rn_id_cliente").val(response.id);
                                $("#rn_nombre_cliente_aux").val($nombres + " " + $apellidos);
                            }
                            if($("#div_reserva_existente").dialog("isOpen") === true)
                            {
                                console.log("Existing");
                                $("#re_id_cliente").val(response.id);
                                $("#re_nombre_cliente_aux").val($nombres + " " + $apellidos);
                                $("#re_cliente_telefonos").val($telefonos.replace("&*","-"))
                            }
                            dlg_cerrar(0);
                        }
    						
    					else
    					{
    						if(window.confirm(response.mensaje + "Desea crearlo de todas maneras?"))
	    					{
	    						$.ajax({
									type: "POST",								
									url:"<?php echo $cliente_buscar_query_cliente;?>",								
									data: { 
										id_tipo_documento: $id_tipo_documento,
										nro_documento: $nro_documento,
										nombres: $nombres,						 
										apellidos: $apellidos,
										telefonos: $telefonos,
										operacion: "crear_simple",
										operacion2: "crear",
										id_usuario: $id_usuario
										},
									datatype: 'json',
									success: function(data) {
										response = jQuery.parseJSON(data);
										alert(response.mensaje);
										if(response.isOK == true)
										{
										    if($("#div_reserva_nueva").dialog("isOpen") === true)
                                            {
                                                console.log("Nuevo");
                                                $("#rn_id_cliente").val(response.id);
                                                $("#rn_nombre_cliente_aux").val($nombres + " " + $apellidos);
                                            }
                                            if($("#div_reserva_existente").dialog("isOpen") === true)
                                            {
                                                console.log("Existing");
                                                $("#re_id_cliente").val(response.id);
                                                $("#re_nombre_cliente_aux").val($nombres + " " + $apellidos);
                                                $("#re_cliente_telefonos").val($telefonos.replace("&*","-"))
                                            }
                                            dlg_cerrar(0);
										}
									}
								});
	    						
	    					}	
    					}
    					
							            
					},
					error: function(jqXHR, textStatus)
					{
										
					},
					complete: function()
					{
						
					}
				});
			}
			
		});
		
		
	});
			
	
			
				

</script>
<style type="text/css">	    
	#div_cliente_nuevo { float: left;
		font-family: Helvetica; margin: 10px 10px 10px 10px; }	    
	.cliente_texto1 { font-size: 11px; width: 70px; }
	.cliente_texto2 { font-size: 11px; width: 100px; }			
	.cliente_texto3 { font-size: 11px; width: 200px; }
	.cliente_fila_seleccionada:hover { background-color: #F2F39F; cursor: pointer; }
	.dato { text-transform: uppercase; }
	#cliente_div_nota { float: right; font-size: 10px; }
	#dlg_cliente { border-radius: 10px 10px 10px 10px; border: dotted #0099CC 1px; }
	.cls_div_cliente .ui-dialog-titlebar { display: none; }
	.cls_div_cliente .ui-widget-content { min-width: 420px; }
	
	.my-error-class {
    	color:#FF0000;  /* red */
    	font-weight: bold;
	}

    #fs_cliente_nuevo{ border: none; }

    #fs_cliente_nuevo ul{ margin:5px auto; max-width: 500px; font: 12px "Helvetica", "Lucida Grande"; -webkit-padding-start: 20px; }

    #fs_cliente_nuevo li { padding: 0; display: block; list-style: none; margin: 10px 0 0 0; }
    #fs_cliente_nuevo label{ margin:0 0 3px 0; padding:0px; display:block; font-weight: bold; }

    #fs_cliente_nuevo input[type=text],
    #fs_cliente_nuevo input[type=date],
    #fs_cliente_nuevo input[type=datetime],
    #fs_cliente_nuevo input[type=number],
    #fs_cliente_nuevo input[type=search],
    #fs_cliente_nuevo input[type=time],
    #fs_cliente_nuevo input[type=url],
    #fs_cliente_nuevo input[type=email],
    textarea,
    select{
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        border:1px solid #BEBEBE;
        padding: 7px;
        margin:0px;
        -webkit-transition: all 0.30s ease-in-out;
        -moz-transition: all 0.30s ease-in-out;
        -ms-transition: all 0.30s ease-in-out;
        -o-transition: all 0.30s ease-in-out;
        outline: none;
    }
    #fs_cliente_nuevo input[type=text]:focus,
    #fs_cliente_nuevo input[type=date]:focus,
    #fs_cliente_nuevo input[type=datetime]:focus,
    #fs_cliente_nuevo input[type=number]:focus,
    #fs_cliente_nuevo input[type=search]:focus,
    #fs_cliente_nuevo input[type=time]:focus,
    #fs_cliente_nuevo input[type=url]:focus,
    #fs_cliente_nuevo input[type=email]:focus,
    #fs_cliente_nuevo textarea:focus,
    #fs_cliente_nuevo select:focus{
        -moz-box-shadow: 0 0 8px #88D5E9;
        -webkit-box-shadow: 0 0 8px #88D5E9;
        box-shadow: 0 0 8px #88D5E9;
        border: 1px solid #88D5E9;
    }
    #fs_cliente_nuevo .field-divided{
        width: 49%;
    }

    #fs_cliente_nuevo .field-long{
        width: 100%;
    }
    #fs_cliente_nuevo .field-select{
        width: 100%;
    }
    #fs_cliente_nuevo .field-textarea{
        height: 100px;
    }
    #fs_cliente_nuevo input[type=submit], #fs_reserva input[type=button]{
        background: #4B99AD;
        padding: 8px 15px 8px 15px;
        border: none;
        color: #fff;
    }
    #fs_cliente_nuevo input[type=submit]:hover, #fs_reserva input[type=button]:hover{
        background: #4691A4; box-shadow:none; -moz-box-shadow:none; -webkit-box-shadow:none;
    }
    #fs_cliente_nuevo .required{
        color:red;
    }

    #fs_cliente_nuevo textarea{
        resize: none;
    }

    #rn_nombre_cliente { width: 325px; font-size: 12px; }
    .my-error-class {
        color:#FF0000;  /* red */
        font-weight: bold;
    }



</style>

<div id="div_cliente_nuevo">
	<form id="cliente" name="cliente" action="<?php echo $cliente_buscar_enlace_post; ?>" method="POST">
		<input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario;?>" />
        <fieldset id="fs_cliente_nuevo">
            <ul>
                <li>
                    <label for="cliente_id_tipo_documento">Topo y Nro de Documento Identidad</label>
                    <select name="cliente_id_tipo_documento" id="cliente_id_tipo_documento" class="cliente_texto1">
                        <?php
                        $tipodocBLO = new TipoDocumentoBLO();

                        $tipos = $tipodocBLO->Listar('');
                        if(count($tipos) > 0)
                            foreach ($tipos as $t)
                            {
                                $selected = $t->id == 1 ? "selected=selected" : "";
                                echo "<option value='$t->id'>$t->descripcion</option>";
                            }
                        ?>
                    </select>
                    <input name="cliente_nro_documento" id="cliente_nro_documento" class="cliente_texto1 dato" maxlength="20"/>
                </li>

                <li>
                    <label for="cliente_nombres">Nombres y Apellidos</label>
                    <input name="cliente_nombres" id="cliente_nombres" class="cliente_texto2 dato" maxlength="50"/>
                    <input name="cliente_apellidos" id="cliente_apellidos" class="cliente_texto2 dato" maxlength="50"/>
                </li>
                <li>
                    <label for="cliente_telefono1">Telefonos</label>
                    <input name="cliente_telefono1" id="cliente_telefono1" class="cliente_texto2" maxlength="15"/>
                    <input name="cliente_telefono2" id="cliente_telefono2" class="cliente_texto2" maxlength="15"/>
                </li>
                <li>
                    <input type="button" class="clase12" id="cliente_btn_crear" style="font-size: 11px;" value="Crear" />
                    <!--input type="button" class="clase12" style="font-size: 11px;" value="Cerrar" onclick="dlg_cerrar(0)"/-->
                </li>
            </ul>
        </fieldset>
	</form>
</div>


