<?php

session_start();

date_default_timezone_set("America/Lima");

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";


include ("../clases/registro_venta.php");

include ("../clases/PHPExcel.php");

$rvBLO = new RegistroVentaBLO();

if(isset($_POST["fecha_inicio"]))
	$fecha_inicio = $_POST["fecha_inicio"];
else
	$fecha_inicio = "";

if(isset($_POST["fecha_fin"]))
	$fecha_fin = $_POST["fecha_fin"];
else
	$fecha_fin = "";

if(isset($_POST["operacion"]))
	$operacion = $_POST["operacion"];
else
	$operacion = "";

$fecha_inicio_mostrar = $fecha_inicio != "" ? date("d-m-Y", strtotime( date('Y-m-d H:i:s', strtotime($fecha_inicio)))) : "";
$fecha_fin_mostrar = $fecha_fin != "" ? date("d-m-Y", strtotime( date('Y-m-d H:i:s', strtotime($fecha_fin)))) : "";

if($fecha_inicio != "" && $fecha_fin != "")
{
	$lista = $rvBLO->ListarItemXFecha($fecha_inicio, $fecha_fin);
	
	if(!is_null($lista))
	{
		if($operacion == "generar_excel")
		{
			$objPHPExcel = new PHPExcel();
			$nombre_archivo = "RV".date("YmdHis", strtotime( date('Y-m-d H:i:s')));
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", "Nro Fila");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B1", "Periodo");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C1", "Fecha");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D1", "Tipo Comp");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E1", "Nro Comp");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F1", "Cliente");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G1", "Monto Neto");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H1", "I.G.V.");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I1", "Monto Total");
            
            $i = 2;
            
			foreach($lista as $rvi)
			{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$i", $i);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$i", $rvi->periodo);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$i", $rvi->fecha);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$i", $rvi->cod_comprobante_pago_tipo);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$i", $rvi->nro_comprobante);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F$i", $rvi->cliente);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$i", $rvi->monto_neto_mn);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H$i", $rvi->monto_impuesto_mn);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I$i", $rvi->monto_total_mn);
				
				$i++;
			}
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'."$nombre_archivo.xls".'"');
			header('Cache-Control: max-age=0');
			
			$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
			$objWriter->save('php://output');
			exit;
		}	
	}
	
}

include ('../clases/enc_dec.php');
include ('../clases/general.php');
include ('../clases/usuario.php');
include ('../clases/centro.php');
include ('../clases/opcion.php');
include ('../clases/security.php');
include ("../clases/comprobante_pago.php");
include ("../clases/anuncio.php");

$opcBLO = new OpcionBLO();
  
$opcion_key = "0XUOH611";

$enlace_procesar = "ver_registro_ventas.php?id_centro=$id_centro&opcion_key=$opcion_key&usr_key=$usr_key";

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
			.etiqueta_1 { font-family: Helvetica; font-size: 12px;  color: #585858; }
			select { font-family: Helvetica; font-size: 12px; }
			
			.dato { font-family: Helvetica; font-size: 11px; text-align: center; font-weight: bold; }
			.dato_1 { font-family: Helvetica; font-size: 11px; text-align: center; }
			.texto_1 { width: 50px; }
			.texto_1_5 { width: 65px;}
			.texto_2 { width: 110px; }
			.texto_3 { width: 150px; }
			.texto_4 { width: 200px; }
			.texto_5 { width: 300px; }
			.texto_6 { width: 450px; }
			.texto_7 { width: 550px; }
			
			.cantidad { width: 45px;font-size: 11px; text-align: center; }
			.eliminar_fila_producto { width: 100px; text-align: center; font-size: 11px; }
			#crear_movimiento { font-size: 11px; }
			
			.titulo_1 { font-size: 14px; font-weight: bold; color: #0099CC; font-family: Helvetica; }
			.titulo_2 { font-size: 12px; font-weight: bold; color: #585858; font-family: Helvetica; }
			
			#tabla_info { border-collapse: collapse;}
			#tabla_info tbody td{ border-bottom: dotted 1px #0099CC;  }
			.td_titulo { border-bottom: dotted 1px #0099CC; }
			
			#div_titulo_comprobantes { float: left; width: 1050px;  }
			#tabla_lista_registro_ventas { }
			#div_lista_registro_ventas { width: 1050px; border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; margin-bottom: 10px; margin-top: 20px; }
			
			#tabla_lista_registro_ventas { border-collapse: collapse; font-family: Helvetica;  }
			#tabla_lista_registro_ventas thead th { font-size: 12px; font-weight: bold; color: #0099CC; border-bottom: dotted 1px #0099CC; }
			#tabla_lista_registro_ventas tbody td { border-bottom: dotted 1px #0099CC; font-size: 11px; color: #585858;}
			
			
			#tabla_lista_registro_ventas tbody tr:nth-child(odd) { background-color:#DAF1F7; }
			#tabla_lista_registro_ventas tbody tr:nth-child(even) { background-color:#FFFFFF; }
			
			#div_operacion { margin-top: 15px; display: none; }
			
			.ui-menu-item { font-family: Helvetica; font-size: 11px; }
			
			#btn_operacion { display: none; }
			
			.nro_comprobante { color: #0099CC; font-size: 13px; }
			
			#div_prueba { width: 20px; height: 20px; background-color: #0099CC }
			#div_guardar_cambios { display: none; margin-top: 15px; }
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
		
		function roundNumber(number, digits) 
		{
            var multiple = Math.pow(10, digits);
            var rndedNum = Math.round(number * multiple) / multiple;
            return rndedNum;
        }
		
		$(function()
		{
			$fecha_inicio = <?php echo $fecha_inicio_mostrar != "" ? "\"".$fecha_inicio_mostrar."\"" : "new Date()"?>;
			$fecha_fin = <?php echo $fecha_fin_mostrar != "" ? "\"".$fecha_fin_mostrar."\"" : "new Date()"?>;			
			
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
			$("#fecha_inicio_str").datepicker("setDate", $fecha_inicio);
			
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
			$("#fecha_fin_str").datepicker("setDate", $fecha_fin);
			
			$("#btn_buscar").click(function()
			{
				$("#lista_registro_ventas").submit();
			});
			
			$("#btn_generar_excel").click(function()
			{
				$("#operacion").val("generar_excel");
				$("#lista_registro_ventas").submit();
			});
			
		});
		
		</script>
	</head>
	<body>		
	<?php 
		include("../header.php");		
	?>
	<div id="div_main" align="center">
	<form id="lista_registro_ventas" name="lista_registro_ventas" method="post" action="<?php echo $enlace_procesar; ?>">
		<input id="operacion" name="operacion" type="hidden"/>
		<input id="nro_items" name="nro_items" type="hidden"/>
		<div id="div_info">
			<table id="tabla_info">
				<tr>
					<td colspan="15" align="center"><span class="titulo_1">REPORTE PARA GENERACIÓN DE REGISTRO DE VENTAS</span></td>
				</tr>
				<tr height="20px"></tr>
				
				<tr>
					<td><span class="etiqueta">Fecha Inicio: </span></td>
					<td width="10px"></td>
					<td>
						<input type="text" class="texto_2 dato" id="fecha_inicio_str" name="fecha_inicio_str" maxlength="8" readonly="readonly"/>
						<input type="hidden" id="fecha_inicio" name="fecha_inicio"/>
					</td>
					<td width="40px"></td>
					<td><span class="etiqueta">Fecha Fin: </span></td>
					<td width="20px"></td>
					<td>
						<input type="text" class="texto_2 dato" id="fecha_fin_str" name="fecha_fin_str" maxlength="8" readonly="readonly"/>
						<input type="hidden" id="fecha_fin" name="fecha_fin"/>
					</td>
					<td width="40px"></td>
					<td><input type="button" id="btn_buscar" class="texto_1" value="Buscar" ></td>
					<!--td></td>
					<td  colspan="2"><input class="texto_1_5 dato" type="button" id="btn_operacion"></td>
					<td><input type="checkbox" id="flag_fecha" checked="checked"><span class="etiqueta">Buscar Fecha</span></td-->					
				</tr>
				
			</table>
		</div>
		<?php
		if($fecha_inicio != "" && $fecha_inicio != "")
		{?>
		<div id="div_lista_registro_ventas">
			<table id="tabla_lista_registro_ventas">
				<thead>
					<th width=40px>#</th>
					<th width=60px>Periodo</th>
					<th width=60px>Fecha</th>
					<th width=80px>TipoComp.</th>										
					<th width=100px>Nro.Comp.</th>
					<th width=300>Cliente</th>
					<th width=80px>Base</th>
					<th width=80px>IGV</th>
					<th width=80px>Total</th>					
				</thead>
				<tbody>
				<?php
								
				if(!is_null($lista))
				{
					?>
					<tr><td colspan="9" align="center"><input type="button" id="btn_generar_excel" class="texto_2" value="Generar Excel" ></td></tr>
					<?php				
					$i = 1;
					$monto_neto_mn = 0;
					$monto_impuesto_mn = 0;
					$monto_total_mn = 0;
					foreach($lista as $rvi)
					{?>
					<tr>
						<td align="center"><b><?php echo $i;?></b></td>
						<td align="center"><?php echo $rvi->periodo;?></td>
						<td align="center"><?php echo $rvi->fecha;?></td>
						<td align="center"><?php echo $rvi->cod_comprobante_pago_tipo;?></td>
						<td align="center"><b><?php echo $rvi->nro_comprobante;?></b></td>
						<td><?php echo $rvi->cliente;?></td>
						<td align="center"><?php echo $rvi->monto_neto_mn;?></td>
						<td align="center"><?php echo $rvi->monto_impuesto_mn;?></td>
						<td align="center"><?php echo $rvi->monto_total_mn;?></td>							
					</tr>
					<?php
					$i++;
					$monto_neto_mn += $rvi->monto_neto_mn;
					$monto_impuesto_mn += $rvi->monto_impuesto_mn;
					$monto_total_mn += $rvi->monto_total_mn;					
					}
					?>
					<tr>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"><b><?php echo $monto_neto_mn;?></b></td>
						<td align="center"><b><?php echo $monto_impuesto_mn;?></b></td>
						<td align="center"><b><?php echo $monto_total_mn;?></b></td>							
					</tr>
					<?
				}
				else
				{?>
				<tr><td colspan="8">No se ha encontrado información para la Búsqueda.</td></tr>
				<?php
				}
				?>
				</tbody>							
			</table>				
		</div>			
		<?php	
		}
		?>
		
	</form>
	</div>
	
	</body>
</html>


