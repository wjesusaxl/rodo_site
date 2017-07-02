
<?php 
$cliente_buscar_enlace_post = "../procesar_cliente.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>RODO </title>
		<meta name="author" content="Jesus Rodriguez" />
		
      
		
		<script language="JavaScript" src="../js/jquery-1.12.3.js"></script>
		<script language="JavaScript" src="../js/jquery-ui-1.11.4.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.11.4.min.css" />
        <!--script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script-->
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
		<script src="../js/jquery.validate.min.js"></script>
		
		<style media="screen" type="text/css">
			body {
				background-color: #F1F1F1;
			}


			#div_main {
				width: 1250px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; margin: 10px auto; overflow: hidden; background-color: #FFFFFF; font-family: Helvetica; }

			#div_reservas_titulo {}
			#reservas_titulo { font-family: Helvetica; font-size: 18px; font-weight: bold; color: #0099CC; }
			#ir_a_fecha { font-family: Helvetica; font-size: 12px; float: right; font-weight: bold; margin-right: 10px; }
			#ir_a_fecha:hover { cursor: pointer; }

			#fecha_mostrar { font-family: Helvetica; font-size: 11px; width: 60px; }

			#div_reservas_semana { color: #585858; margin-bottom: 20px; width: 1230px; border-radius: 10px 10px 10px 10px; }
			#div_tabla_reservas {
				width: 1225px; overflow-x: auto; overflow-y: auto; display: inline; float: left; border: solid 1px #3399FF; margin-left: 7px; border-radius: 8px 8px 8px 8px; margin-bottom: 10px;
				margin-top: 3px; }
				
			#tabla_reservas { border-collapse: collapse;}
			
			#tabla_reservas tbody tr:nth-child(even) { background-color: #EEE; }
			#tabla_reservas tbody tr:nth-child(odd) {  }
				
			.div_fecha_titulo { float: left; width: 159px; background-color: #333333; color: #FFFFFF; border-radius: 8px 8px 8px 8px; padding-top: 3px; padding-bottom: 3px; }
			.div_fecha_titulo .div_label { height: 16px; }
			.span_fecha_titulo_nombre_dia { font-family: Helvetica; font-size: 14px; font-weight: bold; }
			.span_fecha_titulo_fecha { font-family: Helvetica; font-size: 11px; }
		

			.div_cliente { width: 158px; border-radius: 5px 5px 5px 5px; height: 95%; display: inline-block; font-family: Helvetica; font-size: 10px; width: 158px; margin-left: 1px; }
			.div_cliente:hover { background-color: #585858; color: #FFFFFF; font-weight: bold; }
			.div_cliente p { display: table-cell; vertical-align: middle; text-align: center; }

			#aux_comentarios { resize: none; }
			.ui-dialog { border: dotted 1px #0099CC; }
			.ui-dialog-titlebar { border: dotted 1px #0099CC; }
			.ui-dialog-title { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; }

			#nombre_cliente { text-transform: uppercase; font-size: 11px; width: 240px; text-align: center;  }
			.boton_operacion {  font-size: 11px; }
			#btn_agregar_cliente { width: 22px; height; 20px; font-weight: bold; font-size: 20px; font-family: Helvetica;}			
			.etiqueta { font-size: 11px; font-family: Helvetica; font-weight: bold;  }

			.ui-widget input { font-family: Helvetica; }
			.ui-widget select { font-family: Helvetica; }

			#div_leyenda_estados { border: dotted 1px #0099CC; margin-top: 10px; padding: 10px 10px 10px 10px; width: 980px; height: 18px; border-radius: 10px 10px 10px 10px; background-color: #FFFFFF; margin: 0 auto 10px; overflow: hidden; }
			.div_reserva_estado { float: left; margin-left: 10px; border: dotted 1px #0099CC; border-radius: 5px 5px 5px 5px; background-color: #FFFFFF; padding-left: 5px; font-size: 10px; }
			.leyenda_estado { width: 40px; height: 15px; float: left; margin-left: 10px; border-radius: 5px 5px 5px 5px; }
			.etiqueta_leyenda { float: left; }
			
			.ui-datepicker table {
			    width: 100%;
			    font-size: 11px;
			    border-collapse: collapse;
			    margin: 0 0 .4em;
			}
			
			.ui-widget {
			    font-family: Helvetica;
			    font-size: 12px;
			}
			
			.div_celda_libre { width: 100%; min-height: 20px; border-radius: 5px; box-sizing: border-box; padding-top: 3px; font-size: 4px; }
			.div_celda_libre_00 { color: #FFFFFF; }
			.div_celda_libre_30 { color: #EEE; }
			.div_celda_libre_00:hover { cursor: pointer; background-color: #333333; font-weight: bold; font-size: 12px; }
			.div_celda_libre_30:hover { cursor: pointer; background-color: #333333; font-weight: bold; font-size: 12px; }
			
			.etiqueta_celda_libre { }
			
			
			.td_celda_hora { }
			.td_celda_fecha_hora {  }
			.div_span_etiqueta { }
			.div_celda_hora { box-sizing:border-box; width: 60px; height: 42px; background-color: #333333; border-radius: 5px; vertical-align: middle; text-align: center;
				padding-left: 11px; padding-top: 5px; }
				
			.div_celda_reserva { box-sizing:border-box; border-radius: 5px; }
			.div_celda_reserva:hover { cursor: pointer; color: #FFFFFF; font-weight: bold; }
			.etiqueta_reserva { font-size: 10px; display: block; }
			
			.td_celda_hora {  }
			.div_etiqueta_hora_am_pm {  }
			.div_etiqueta_hora { float: left; }
			.div_etiqueta_am_pm { float: left; }
			.etiqueta_hora { color: #eee; font-size: 24px; font-weight: bold; font-family: Impact; }
			.etiqueta_am_pm { color: #eee; font-size: 8px; }
			
			.div_hora_min_00, .div_hora_min_30 { border-style: solid; border-width: 1px; border-radius: 2px; }
			.div_hora_min_00 { background-color: #333333; color: #FFFFFF; border-color: #333333; }
			.div_hora_min_30 { border-color: #333333; color: #333333; }			
			.etiqueta_hora_min { font-weight: bold; font-size: 13px; text-decoration: underline; }
			
			#reserva_fecha_hora_str { font-weight: bold; }
			''
		</style>
		
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key . "&id_centro=$id_centro"; ?>";
		}
		
		
		/*$(function()
		{
			$(".div_celda_reserva").each(function()
			{
				$td_height = $(this).closest("td").css("height");
				
			})
			
		})*/
		
		


		$(function()
		{
			
			$url_query_cliente = "<?php echo $cliente_buscar_enlace_post;?>" + "?operacion=query2&nombres=";
	
			$('#reserva_nombre_cliente').autocomplete({
                source: function (request, response) {
                    $.getJSON($url_query_cliente + request.term, function (data) {
                        response($.map(data, function (item) {
                            return {
                                value: item.id,
                                label: item.nombres+ ' '+item.apellidos+'['+item.tipo_documento.substring(0,3)+':'+item.nro_documento+']',
                                nombres: item.nombres,
                                apellidos: item.apellidos,
                            };
                        }));
                    });
                },
                select: function(event, ui) {
                    event.preventDefault();                 
                    $("#reserva_id_cliente").val(ui.item.value);
                    $("#reserva_nombre_cliente").val(ui.item.nombres+" "+ui.item.apellidos);
                },
                minLength: 3
            });
            
            $('#reserva_nombre_cliente').autocomplete("option","appendTo","#div_reserva_detalle");

            $("#div_reserva_detalle").dialog(
            {
                autoOpen: false,
                height: 450,
                width: 710,
                modal: true,
                resizable: false,
                title: "Detalle de la Reserva"
            });
            
            $("#btn_test").click(function(){
               $( "#div_reserva_detalle" ).dialog("open" );
            });
			
		});

		</script>
	</head>

	<body>
	<div id="div_main">
    
    <button type="button" id="btn_test">Test</button>
    
	<div id="div_reserva_detalle">
		<fieldset id="fs_reserva">
			<ul>
				<li>
					<label for="reserva_fecha_str">Fecha Hora:</label>
					<span id="reserva_fecha_hora_str"></span>
				</li>				
				<li>
					<label for="reserva_duracion">Duracion:</label>
					<input type="hidden" id="reserva_hora_fin"/>
					<select id="reserva_duracion">
						<option value="0_1">Seleccione...</option>
						<?php
                        for($i = $hora_inicial; $i <= $hora_final; $i++)
                        {
                            for($j = 0; $j < $fraction_in_hour; $j++)
                            {
                                $min = $j==0 ? "00" : "30"; 
                                $valor = ((string)$i).$min."_".TimeIntToString($i, $j * $lap, TRUE);
                                $etiqueta = TimeIntToString($i, $j * $lap, FALSE);
                                echo "<option value=\"$valor\">$etiqueta</option>";
                            }
                        }
                        echo "<option value=\"2359_23:59:59\">12:00 AM (+1 dia)";
                        ?>
					</select>
				</li>
				<li>
				     
				</li>
			</ul>
			
			
		</fieldset>
		
		<!--label for="reserva_nombre_cliente">Cliente</label-->
                    <input type="hidden" id="reserva_id_cliente"/>
                    <input type="text" id="reserva_nombre_cliente"/>
	</div>
	</div>
	

	</body>
</html>
	
