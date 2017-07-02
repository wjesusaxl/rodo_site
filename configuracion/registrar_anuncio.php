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
include ("../clases/anuncio.php");

$anBLO = new AnuncioBLO();
$opcBLO = new OpcionBLO();
$enlace_procesar = "../procesar_anuncio.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";
//$enlace_query = "../procesar_comprobante_pago.php?id_centro=$id_centro&op_original_key=$opcion_key&usr_key=$usr_key";

$opcion_ver_anuncios_otros_usuarios = "QBJ45N60";  //De Local

$permiso_ver_anuncios_otros = $opcBLO->ValidarOpcionXIdUsuario($opcion_ver_anuncios_otros_usuarios, $id_usuario, $id_centro);

if($permiso_ver_anuncios_otros->isOK)
	$lista_anuncios = $anBLO->ListarTodos($id_centro);
else 
	$lista_anuncios = $anBLO->ListarMisAnuncios($id_centro, $id_usuario);

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
			.etiqueta_1 { font-family: Helvetica; font-size: 11px;  color: #585858; }
			select { font-family: Helvetica; font-size: 12px; }
			
			.dato { font-family: Helvetica; font-size: 11px; text-align: center; font-weight: bold; color: #585858; }
			.dato_1 { font-family: Helvetica; font-size: 11px; text-align: center; }
			.texto_1 { width: 50px; }
			.texto_1_5 { width: 65px;}
			.texto_2 { width: 110px; }
			.texto_3 { width: 150px; }
			.texto_4 { width: 200px; }
			.texto_5 { width: 300px; }
			.texto_6 { width: 450px; }
			.texto_7 { width: 600px; }
			
			.titulo_1 { font-size: 14px; font-family: Helvetica; font-weight: bold; color: #0099CC;}
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			#tabla_info { border-collapse: collapse;}
			#tabla_info tbody td{ border-bottom: dotted 1px #0099CC;  }
			.td_titulo { border-bottom: dotted 1px #0099CC; }
			
			#div_titulo_anuncios { float: left; width: 1050px;  }
			#tabla_lista_anuncios { }
			#div_lista_anuncios { width: 1050px; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; margin-bottom: 10px; margin-top: 20px; }
			
			#tabla_lista_anuncios { border-collapse: collapse; width: 1040px; font-family: Helvetica;  }
			#tabla_lista_anuncios thead th { font-size: 12px; font-weight: bold; color: #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_anuncios tbody td { border-bottom: dotted 1px #0099CC; font-size: 11px; color: #585858;}
			
			
			#tabla_lista_anuncios tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_lista_anuncios tbody tr:nth-child(even) { background-color:#FFFFFF; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px; }
			
			
			
		
		</style>
		
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>	
		<script language="JavaScript" src="../js/jquery.cookie.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		
		<script src="../calendario/jquery.ui.core.js"></script>
        <script src="../calendario/jquery.ui.widget.js"></script>
        <script src="../calendario/jquery.ui.datepicker.js"></script>
        <link rel="stylesheet" href="../calendario/demos.css">
        <link rel="stylesheet" href="../calendario/base/jquery.ui.all.css">
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		function padStr(i) 
		{
		    return (i < 10) ? "0" + i : "" + i;
		}
		
		function FechaFormato(fecha, formato)
		{
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
		
		function validate(evt) 
		{
			var theEvent = evt || window.event;
			var key = theEvent.keyCode || theEvent.which;
			key = String.fromCharCode( key );
			//var regex = /[0-9]|\./;
			var regex = /[0-9]/;
			if( !regex.test(key) ) 
			{
				theEvent.returnValue = false;
				if(theEvent.preventDefault) theEvent.preventDefault();
			}
		}
		function validate2(evt) 
		{
			var theEvent = evt || window.event;
			var key = theEvent.keyCode || theEvent.which;
			key = String.fromCharCode( key );
			var regex = /[0-9]|\./;
			//var regex = /[0-9]/;
			if( !regex.test(key) ) 
			{
				theEvent.returnValue = false;
				if(theEvent.preventDefault) theEvent.preventDefault();
			}
		}
		
		
		
		$(function()
		{
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
			
			$("#btn_guardar").click(function()
			{
				$mensaje = $("#mensaje").val();
				
				if($mensaje != "")
				{
					$("#operacion").val("crear");
					$("#anuncio").submit();
				}
				else
					alert("No ha Ingresado el Anuncio");
			});
			
			$(".btn_modificar_anuncio").live("click", function()
			{
				$fila = $(this).parent().parent();
				
				var $url = "<?php echo $enlace_procesar; ?>";
				
				$fecha_inicio = $fila.find(".fecha_inicio").val();
				$fecha_fin = $fila.find(".fecha_fin").val();
				$id_anuncio = $fila.find(".id_anuncio").val();
				$mensaje = $fila.find(".mensaje").val();
				$flag_anulado = $fila.find(".flag_anulado").val();
				
				$("#id_anuncio").val($id_anuncio);
				$("#fecha_inicio").val($fecha_inicio);
				$("#fecha_fin").val($fecha_fin);
				$("#mensaje").val($mensaje);
				$("#flag_anulado").val($flag_anulado);
				
				if($mensaje != "")
				{
					$("#operacion").val("modificar");
					$.ajax( {
						type: "POST",
						url: $("#anuncio").attr( 'action' ),
						data: $("#anuncio").serialize(),
						success: function( response ) {
				        	$("#mensaje").val("");
				        	$("#flag_anulado").val("0");
				        	alert("Anuncio Guardado!");
				      }
				    } );
				}
				else
					alert("No ha Ingresado el Anuncio");
			});
			
			<?php
			
			if(!is_null($lista_anuncios))
			{
				if(count($lista_anuncios) > 0)
				{
					$i = 1;
					foreach($lista_anuncios as $a)
					{
						
						?>
						$("#fecha_inicio_str_<?php echo $i;?>").datepicker({
							dateFormat: "dd-mm-yy",
							altField: "#fecha_inicio_<?php echo $i;?>",
							altFormat: 'yy-mm-dd'
						});
									
						$("#fecha_fin_str_<?php echo $i;?>").datepicker({
							dateFormat: "dd-mm-yy",
							altField: "#fecha_fin_<?php echo $i;?>",
							altFormat: 'yy-mm-dd'
						});						
						
						<?php
					
						$i++;
					}
				}
			}
			?>
			
			
		});
		
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
	<form id="anuncio" name="anuncio" method="post" action="<?php echo $enlace_procesar; ?>">
		<input id="operacion" name="operacion" type="hidden"/>
		<input id="id_anuncio" name="id_anuncio" type="hidden"/>
		<input id="id_usuario" name="id_usuario" type="hidden" value="<?php echo $usuario->id;?>" />
		<input id="id_centro" name="id_centro" type="hidden" value="<?php echo $id_centro;?>" />
		<input id="flag_anulado" name="flag_anulado" type="hidden" value="0" />
		<div id="div_info">
			<table id="tabla_info">
				<tr>
					<td colspan="11" align="center"><span class="titulo_1">REGISTRO DE ANUNCIOS PARA RODO</span></td>
				</tr>
				<tr height="20px"></tr>
				<tr>
					<td><span class="etiqueta">Fecha Inicio: </span></td>
					<td width="10px"></td>
					<td>
						<input type="text" class="texto_1_5 dato_1" id="fecha_inicio_str" name="fecha_inicio_str" maxlength="8" readonly="readonly"/>
						<input type="hidden" id="fecha_inicio" name="fecha_inicio"/>
					</td>
					<td width="30px"></td>
					<td><span class="etiqueta">Fecha Fin: </span></td>
					<td width="20px"></td>
					<td>
						<input type="text" class="texto_1_5 dato_1" id="fecha_fin_str" name="fecha_fin_str" maxlength="8" readonly="readonly"/>
						<input type="hidden" id="fecha_fin" name="fecha_fin"/>
					</td>
					<td width="30px"></td>
					<td><span class="etiqueta">Mensaje: </span></td>
					<td width="20px"></td>
					<td><input type="text" class="texto_7 dato" id="mensaje" name="mensaje" maxlength="100"/></td>					
				</tr>
				<tr>
					<td colspan="11" align="center">
						<input type="button" class="texto_2 dato" id="btn_guardar" value="Guardar">
					</td>
				</tr>
				
			</table>
		</div>
		
		<div id="div_lista_anuncios">
			<table id="tabla_lista_anuncios">
				<thead>
					<th width=30px>#</th>
					<th width=60px>Fecha Inicio</th>
					<th width=60px>Fecha Fin</th>
					<th width=610px>Mensaje</th>
					<th width=80px>Usuario</th>
					<th width=40px>Anulado</th>
					<th width=70>Operación</th>									
				</thead>							
			
			<tbody>
			<?php
			
			
			if(!is_null($lista_anuncios))
			{
				if(count($lista_anuncios) > 0)
				{
					$i = 1;
					foreach($lista_anuncios as $a)
					{
						$fecha_inicio = date("d-m-Y", strtotime( date('Y-m-d H:i:s', strtotime($a->fecha_hora_inicio)) ));
						$fecha_fin = date("d-m-Y", strtotime( date('Y-m-d H:i:s', strtotime($a->fecha_hora_fin)) ));
						?>
					
						<tr>
							<td align="center">
								<b><?php echo $i;?></b>
								<input type="hidden" class="id_anuncio" id="id_<?php echo $i;?>" id="name_<?php echo $i;?>" value="<?php echo $a->id;?>"/>	
							</td>
							<td>
								<input type="text" class="texto_1_5 dato_1" name="fecha_inicio_str_<?php echo $i;?>" id="fecha_inicio_str_<?php echo $i;?>" value="<?php echo $fecha_inicio;?>" readonly=readonly />
								<input type="hidden" class="fecha_inicio" name="fecha_inicio_<?php echo $i;?>" id="fecha_inicio_<?php echo $i;?>" value="<?php echo $a->fecha_hora_inicio;?>" />
							</td>
							<td>
								<input type="text" class="texto_1_5 dato_1" name="fecha_fin_str_<?php echo $i;?>" id="fecha_fin_str_<?php echo $i;?>" value="<?php echo $fecha_fin;?>"  readonly=readonly/>
								<input type="hidden" class="fecha_fin" name="fecha_fin_<?php echo $i;?>" id="fecha_fin_<?php echo $i;?>" value="<?php echo $a->fecha_hora_fin;?>" />
							</td>
							<td align="center">
								<input type="text" class="texto_7 dato_1 mensaje"name="mensaje_<?php echo $i;?>" id="mensaje_<?php echo $i;?>" value="<?php echo $a->mensaje;?>" />
							</td>
							<td align="center">
								<span class="etiqueta_1"><?php echo $a->usuario; ?></span>
							</td>
							<td align="center">
								<select id="flag_anulado_$i" name="flag_anulado_$i" class="dato flag_anulado">
									<?php
									if($a->flag_anulado == 0)
									{
										echo "<option value=\"0\" selected='selected'>No</option>\n";
										echo "<option value=\"1\">Si</option>\n";
									}
									else
									{
										echo "<option value=\"0\">No</option>\n";
										echo "<option value=\"1\" selected='selected'>Si</option>\n";	
									}										
									?>
								</select>
							</td>
							<td align="center">
								<input type="button" class="texto_1_5 dato btn_modificar_anuncio" value="Guardar" />
							</td>
						</tr>
						
					<?php
						$i++;	
					}
				}
			}
			
			?>
			</tbody>
		</table>
		</div>
	</form>
	</div>
	
	</body>
</html>


