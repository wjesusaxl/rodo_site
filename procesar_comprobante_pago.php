<?php

session_start();

date_default_timezone_set("America/Lima");


include ("clases/comprobante_pago.php");
include ("clases/cliente.php");
include ("clases/enc_dec.php");

$id_centro = RetornarPOSTGET("id_centro", 0);
$id_comprobante_pago_tipo = RetornarPOSTGET("id_comprobante_pago_tipo", 0);
$operacion = RetornarPOSTGET("operacion", "");
$nro_serie = RetornarPOSTGET("nro_serie", "");
$nro_inicio = RetornarPOSTGET("nro_inicio", 0);
$nro_fin = RetornarPOSTGET("nro_fin", 0);
$fecha_inicio = RetornarPOSTGET("fecha_inicio", "");
$fecha_fin = RetornarPOSTGET("fecha_fin", "");
$id_tipo_origen = RetornarPOSTGET("id_tipo_origen", 0);
$busqueda_fecha = RetornarPOSTGET("busqueda_fecha", "N");
$nro_items = RetornarPOSTGET("nro_items", 0);
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");

$id_comprobante_pago = RetornarPOSTGET("id_comprobante_pago", 0);
$id_transaccion_motivo = RetornarPOSTGET("id_transaccion_motivo", 0);
$fecha_registro = RetornarPOSTGET("fecha_registro", "");
$monto_total_mn = RetornarPOSTGET("monto_total_mn", 0);
$id_comp_pago_agente = RetornarPOSTGET("id_comp_pago_agente", 0);
$flag_anulado = RetornarPOSTGET("flag_anulado", 0);

if($operacion == "query_serie")
{
	$lista = NULL;
	
	if($id_comprobante_pago_tipo > 0)
	{		
		$compBLO = new ComprobantePagoBLO();		
		
		
		$lista = $compBLO->ListarSerieXIdComprobantePagoTipo($id_comprobante_pago_tipo , $id_centro);
		
		if(count($lista) == 0)
			$lista = NULL;
		 
	}
	
	echo json_encode($lista);
}

if($operacion == "query_comprobante_pago_tipo_transaccion_motivo" && $id_transaccion_motivo >0)
{
	
	$compBLO = new ComprobantePagoBLO();
	$lista = $compBLO->ListarComprobantePagoTipoXIdTransaccionMotivo($id_transaccion_motivo);
	echo json_encode($lista);
	
}

if($operacion == "query_comprobante")
{
	$lista = NULL;
	
	if($id_comprobante_pago_tipo > 0)
	{
		$compBLO = new ComprobantePagoBLO();
		
		if($busqueda_fecha == "Y")
			$lista = $compBLO->ListarXRangoNroComprobante($id_centro, $id_tipo_origen, $nro_serie, $id_comprobante_pago_tipo, $nro_inicio, $nro_fin, $fecha_inicio, $fecha_fin);
		else
			$lista = $compBLO->ListarXRangoNroComprobanteSinFecha($id_centro, $id_tipo_origen, $nro_serie, $id_comprobante_pago_tipo, $nro_inicio, $nro_fin);
		
		if(count($lista) == 0)
			$lista = NULL;
	}

	echo json_encode($lista);
}

if($operacion == "generar")
{
	$lista = NULL;
	
	if($id_comprobante_pago_tipo > 0)
	{
		$compBLO = new ComprobantePagoBLO();
		
		$lista = $compBLO->ListarXRangoNroComprobanteSinFecha($id_centro, $id_tipo_origen, $nro_serie, $id_comprobante_pago_tipo, $nro_inicio, $nro_fin);
		
		//$comprobante_pago_tipo = $compBLO->RetornarTipoXId($id_comprobante_pago_tipo);
		$comp_tipo = $compBLO->RetornarTipoXId($id_comprobante_pago_tipo);
		
		if(count($lista) == 0)
			$flag_OK = TRUE;
		else
			$flag_OK = FALSE;
		
		$lista = array();
		
		for($i = $nro_inicio; $i <= $nro_fin; $i++)
		{
			$comp = new ComprobantePago();
			$comp->auto_key = random_string();
			$comp->id_centro = $id_centro;
			$comp->id_tipo_origen = $id_tipo_origen;
			$comp->comprobante_pago_tipo = $comp_tipo->descripcion;
			$comp->fecha_hora_registro = NULL;
			//$comp->fecha_hora_registro = date('Y-m-d H:i:s');
			$comp->id_comprobante_pago_tipo = $id_comprobante_pago_tipo;
			$comp->id_comp_pago_agente_tipo = 2; //Tipo Cliente
			$comp->id_comp_pago_agente = 1; //Cliente Genérico
			$comp->comp_pago_agente = "CLIENTE GENÉRICO";
			$comp->id_tipo_documento = 1; //DNI
			$comp->nro_documento = 0; //Nro Documento
			$comp->nro_comprobante = $nro_serie."-".str_pad($i, 6, "0", STR_PAD_LEFT);
			$comp->monto_neto_mn = 0;
			$comp->monto_impuesto_mn = 0;
			$comp->monto_percepcion_mn = 0;
			$comp->monto_otros_impuestos_mn = 0;
			$comp->monto_total_mn = 0;
			$comp->flag_anulado = 0;
			$comp->flag_post = 0;
			
			if(!$flag_OK)
			{
				$comp->flag_post = "NG";
				$lista[] = $comp;				
			}
			else
				$compBLO->RegistrarSinValidar($comp);
			
		}
		
		if($flag_OK)
		{
			$lista = $compBLO->ListarXRangoNroComprobanteSinFecha($id_centro, $id_tipo_origen, $nro_serie, $id_comprobante_pago_tipo, $nro_inicio, $nro_fin);
			
			foreach($lista as $o)
				$o->flag_post = "G";
		}
		
		if(count($lista) == 0)
			$lista = NULL;
	}

	echo json_encode($lista);
}

if($operacion == "modificar_comprobante")
{
	if($id_comprobante_pago > 0)
	{
		$compBLO = new ComprobantePagoBLO();
		$cliBLO = new ClienteBLO();
		
		$obj = $compBLO->RetornarXId($id_comprobante_pago);
				
		if(!is_null($obj))
		{
			$flag_post = $obj->flag_post;
					
			if($flag_post == 0)
			{
				if($fecha_registro != "" )
					$obj->fecha_hora_registro = $fecha_registro;
				else
					$obj->fecha_hora_registro = NULL;						
						
				$obj->monto_total_mn = $monto_total_mn;
				$obj->monto_neto_mn = round($monto_total_mn / 1.18, 2);
				$obj->monto_impuesto_mn = round($monto_total_mn - $obj->monto_neto_mn, 2);
				$obj->id_comp_pago_agente = $id_comp_pago_agente;
						
				if($id_comp_pago_agente > 1)
				{
					$cli = $cliBLO->RetornarClienteXId($id_comp_pago_agente);
							
					if(!is_null($cli))
					{
						$obj->id_tipo_documento = $cli->id_tipo_documento;
						$obj->nro_documento = $cli->nro_documento;
					}
							
				}
						
				$obj->flag_anulado = $flag_anulado;
						
				$compBLO->Modificar($obj);
						
			}
			
		}
		
	}
}

if($operacion == "modificar")
{
	if($nro_items > 0)
	{
		$compBLO = new ComprobantePagoBLO();
		$cliBLO = new ClienteBLO();
		
		for($i = 1; $i <= $nro_items; $i++)
		{
			
			$id = RetornarPOSTGET("id_comprobante_pago_$i", 0);
			$fecha_registro = RetornarPOSTGET("fecha_registro_$i", "");
			$monto_total_mn = RetornarPOSTGET("monto_total_mn_$i", 0);
			$id_comp_pago_agente = RetornarPOSTGET("id_comp_pago_agente_$i", 0);
			$flag_anulado = RetornarPOSTGET("flag_anulado_$i", 0);
			
			if($id > 0)
			{
				$obj = $compBLO->RetornarXId($id);
				
				if(!is_null($obj))
				{
					$flag_post = $obj->flag_post;
					
					if($flag_post == 0)
					{
						if($fecha_registro != "" )
							$obj->fecha_hora_registro = $fecha_registro;
						else
							$obj->fecha_hora_registro = NULL;						
						
						$obj->monto_total_mn = $monto_total_mn;
						$obj->monto_neto_mn = round($monto_total_mn / 1.18, 2);
						$obj->monto_impuesto_mn = round($monto_total_mn - $obj->monto_neto_mn, 2);
						$obj->id_comp_pago_agente = $id_comp_pago_agente;
						
						if($id_comp_pago_agente > 1)
						{
							$cli = $cliBLO->RetornarClienteXId($id_comp_pago_agente);
							
							if(!is_null($cli))
							{
								$obj->id_tipo_documento = $cli->id_tipo_documento;
								$obj->nro_documento = $cli->nro_documento;
							}
							
						}
						
						$obj->flag_anulado = $flag_anulado;
						
						$compBLO->Modificar($obj);
						
					}
				}
			}
			
			 ?>
	        <script type="text/javascript">
	            alert('Datos Guardados!');             
	        </script>
	        <?php
	        Redireccionar($op_original_key, $usr_key, $id_centro);     
			
		}
		
	}
	
}


function RetornarPOSTGET($value, $default)
{
	if(isset($_GET[$value]))
		$q = $_GET[$value];
	else
		if(isset($_POST[$value]))
			$q = $_POST[$value];
		else
			$q = $default;
	
	return $q;
}

function Redireccionar($opcion_key, $usr_key, $id_centro)
{
    echo "Redireccionando..";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
}


function json_encode2($list)
{
	$cadena = "{\"data\":[]}";
	if(!is_null($list))
		if(count($list) > 0)
		{
			$cadena = "";
			
			switch($list[0]->tipo_objeto)
			{
				case "comprobante_pago_tipo_transaccion_motivo":
					$i = 0;
					$separator = "";
					$cadena = "{\"data\":[";
					foreach($list as $o)
					{
						if($i > 0)							
							$separator = ",";
						$objeto = $separator."["."\"".$o->id."\",";	
						$objeto = $objeto."\"".$o->id_comprobante_pago_tipo."\",";
						$objeto = $objeto."\"".$o->cod_comprobante_pago_tipo."\",";
						$objeto = $objeto."\"".$o->comprobante_pago_tipo."\",";
						$objeto = $objeto."\"".$o->id_transaccion_motivo."\",";
						$objeto = $objeto."\"".$o->cod_transaccion_motivo."\",";
						$objeto = $objeto."\"".$o->cod_transaccion_motivo."\",";
						$objeto = $objeto."\"".$o->transaccion_motivo."\",";
						$objeto = $objeto."\"".$o->id_transaccion_grupo."\",";
						$objeto = $objeto."\"".$o->cod_transaccion_grupo."\",";
						$objeto = $objeto."\"".$o->transaccion_grupo."\",";
						$objeto = $objeto."\"".$o->transaccion_grupo_factor."\",";						
						$objeto = $objeto."\"".$o->tipo_objeto."\"]";
						$cadena = $cadena.$objeto;
						$i ++;
					}	
					$cadena = $cadena."]}";
				break;
			}	
		}
	echo $cadena;
}

?>