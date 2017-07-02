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
include ("../clases/comprobante_pago.php");


$fecha_inicio = "";
$fecha_fin = "";
$operacion = "";

if(isset($_POST["fecha_inicio"]))
	$fecha_inicio = $_POST["fecha_inicio"];

if(isset($_POST["fecha_fin"]))
	$fecha_fin = $_POST["fecha_fin"];

if(isset($_POST["operacion"]))
	$operacion = $_POST["operacion"];

if($operacion == "mostrar")
{
	/*echo "Fecha Inicio: $fecha_inicio</br>";
	echo "Fecha Fin: $fecha_fin</br>";*/
	
	$compBLO = new ComprobantePagoBLO();
	$cenBLO = new CentroBLO();
	
	
	$lista_centros = $cenBLO->ListarTodos();
	
}

$enlace_post = $_SERVER['PHP_SELF']."?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";

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
			.texto_1 { width: 30px; text-align: center; font-size: 11px; }
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
			
			#div_opcion_general { float: right; margin-right: 20px; width: 1030px; margin-bottom: 20px;  }
			
			#titulo { margin-bottom: 20px; }
			#id_opcion_general { margin-left: 10px; }
			#tabla_ingreso { border-collapse: collapse; }
			#tabla_ingreso td { border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; padding-bottom: 3px; }
			
			#tabla_opcion_cantidad td { border: none; }
			#tabla_opcion_cantidad th { font-size: 12px; color: #0099CC; }
			
			#div_tabla_resultados { margin-top: 20px; border-collapse: collapse; padding: 10px 5px 10px 5px; border: dotted 1px #0099CC; width: 700px; border-radius: 10px 10px 10px 10px; }
			#tabla_resultados th { font-size: 13px; color: #0099CC; border-bottom: dotted 1px #0099CC; border-top: dotted 1px #0099CC; background-color: #FFFFFF;}
			#tabla_resultados td { font-size: 12px; color: #585858;}
			#tabla_resultados tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_resultados tr:nth-child(even) { background-color:#FFFFFF; }
			/*#tabla_resultados tbody tr:not(:first-child):hover { background-color: #F8FEA9; }*/
			#tabla_resultados tbody tr:hover { background-color: #F8FEA9; }
			#tabla_resultados { border-collapse: collapse; }
			
			#comentarios { font-size: 11px; resize: none; font-family: Helvetica;  }
			
			.btn_edit:hover { cursor: pointer; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px;}
			
			.total_total { font-size: 12px;}
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
        
        function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
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
			
			$("#btn_mostrar").click(function()
			{
				$("#operacion").val("mostrar");
				$("#resumen").submit();
			})
		});
		
	
		
		
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
		<form id="resumen" name="transaccion" method="post" action="<?php echo $enlace_post; ?>">
			<input type="hidden" name="operacion" id ="operacion" />
			<div id="div_tabla_resumen">
				<table id="tabla_resumen">
					<tr>
						<td><span class="etiqueta">Fecha Inicio:</span></td>
						<td>
							<input type="text" id="fecha_inicio_str" name="fecha_inicio_str" value="" readonly="readonly" class="texto_1_5"/>
							<input type="hidden" id="fecha_inicio" name="fecha_inicio"/>
						</td>
						<td width="50px"></td>
						<td><span class="etiqueta">Fecha Fin:</span></td>
						<td>
							<input type="text" id="fecha_fin_str" name="fecha_fin_str" value="" readonly="readonly" class="texto_1_5"/>
							<input type="hidden" id="fecha_fin" name="fecha_fin"/>
						</td>
						<td width="50px"></td>
						<td>
							<input type="button" id="btn_mostrar" class="texto_2" value="Mostrar" />
						</td>
					</tr>
				</table>
			</div>
			<div id="div_tabla_resultados">
				<table id="tabla_resultados">
					<thead>
						<th width=100px>CENTRO</th>
						<th width=200px>TIPO COMPROBANTE</th>
						<th width=100px>MONTO NETO</th>
						<th width=100px>I.G.V.</th>
						<th width=100px>PERCEPCIÓN</th>
						<th width=100px>MONTO TOTAL</th>
					</thead>
					<tbody>
					<?php
					
					
					if(!is_null($lista_centros))
					{
						$monto_neto_mn = 0;
						$monto_impuesto_mn = 0;
						$monto_percepcion_mn = 0;
						$monto_total_mn = 0;
						
						foreach($lista_centros as $cen)
						{
							$monto_centro_neto_mn = 0;
							$monto_centro_impuesto_mn = 0;
							$monto_centro_percepcion_mn = 0;
							$monto_centro_total_mn = 0;
							
							$lista_comp_resumen = $compBLO->ListarResumenCompEmitidosXRangoFechas($cen->id, $fecha_inicio, $fecha_fin);
	
							if(!is_null($lista_comp_resumen))
							{
								if(count($lista_comp_resumen) > 0)
								{
									foreach($lista_comp_resumen as $c)
									{
										$monto_centro_neto_mn += $c->monto_neto_mn;
										$monto_centro_impuesto_mn += $c->monto_impuesto_mn;
										$monto_centro_percepcion_mn += $c->monto_percepcion_mn;
										$monto_centro_total_mn += $c->monto_total_mn;
									?>
									<tr>
										<td align="center"><?php echo strtoupper($c->centro);?></td>
										<td align="center"><?php echo strtoupper($c->comprobante_pago_tipo);?></td>
										<td align="center"><?php echo "S/. ".number_format($c->monto_neto_mn, 2);?></td>
										<td align="center"><?php echo "S/. ".number_format($c->monto_impuesto_mn, 2);?></td>
										<td align="center"><?php echo "S/. ".number_format($c->monto_percepcion_mn, 2);?></td>
										<td align="center"><?php echo "S/. ".number_format($c->monto_total_mn, 2);?></td>							
									</tr>
									<?php
									}
									?>
									<tr>
										<td align="right" colspan="2"><b><?php echo "TOTAL"?></b></td>
										<td align="center"><b><?php echo "S/. ".number_format($monto_centro_neto_mn, 2);?></b></td>
										<td align="center"><b><?php echo "S/. ".number_format($monto_centro_impuesto_mn, 2);?></b></td>
										<td align="center"><b><?php echo "S/. ".number_format($monto_centro_percepcion_mn, 2);?></b></td>
										<td align="center"><b><?php echo "S/. ".number_format($monto_centro_total_mn, 2);?></b></td>
									</tr>
								<?	
								}
							
							$monto_neto_mn += $monto_centro_neto_mn;
							$monto_impuesto_mn += $monto_centro_impuesto_mn;
							$monto_percepcion_mn += $monto_centro_percepcion_mn;
							$monto_total_mn += $monto_centro_total_mn;
								
							}
							
						}
						?>
						<tr height=10px></tr>
						<tr >
							<td align="right" colspan="2" class="total_total"><b><?php echo "TOTAL"?></b></td>
							<td align="center"><b><?php echo "S/. ".number_format($monto_neto_mn, 2);?></b></td>
							<td align="center"><b><?php echo "S/. ".number_format($monto_impuesto_mn, 2);?></b></td>
							<td align="center"><b><?php echo "S/. ".number_format($monto_percepcion_mn, 2);?></b></td>
							<td align="center"><b><?php echo "S/. ".number_format($monto_total_mn, 2);?></b></td>
						</tr>
					<?	
					}
					?>
					</tbody>
				</table>
			</div>
		</form>
	</div>
	
	</body>
</html>