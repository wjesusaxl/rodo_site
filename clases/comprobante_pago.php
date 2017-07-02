<?php

class ComprobantePago
{
	public $id;
	public $auto_key;
	public $id_centro;
	public $cod_centro;
	public $centro;
	public $id_tipo_origen;
	public $cod_tipo_origen;
	public $tipo_origen;
	public $fecha_hora_registro;
	public $id_comprobante_pago_tipo;
	public $cod_comprobante_pago_tipo;
	public $comprobante_pago_tipo;
	public $id_tipo_documento;
	public $cod_tipo_documento;
	public $tipo_documento;
	public $nro_documento;
	public $nro_comprobante;
	public $monto_neto_mn;
	public $monto_impuesto_mn;
	public $monto_percepcion_mn;
	public $monto_otros_impuestos_mn;
	public $monto_total_mn;
	public $flag_anulado;
	public $flag_post;
	public $id_comp_pago_agente_tipo;
	public $id_comp_pago_agente;
	public $comp_pago_agente;
	public $cod_agente_tipo;
	public $agente_tipo;
}

class ComprobantePagoSerie
{
	public $id;
	public $id_centro;
	public $id_comprobante_pago_tipo;
	public $nro_serie;
	public $flag_habilitado;
}
    
class ComprobantePagoTipo
{
	public $id;
	public $codigo;
	public $descripcion;
	public $descripcion_corta;		
		
}

class ComprobantePagoAgenteTipo
{
	public $id;
	public $codigo;
	public $descripcion;
	public $descripcion_corta;
}

class ComprobantePagoTipoTransaccionMotivo
{
	public $id;
	public $id_comprobante_pago_tipo;
	public $cod_comprobante_pago_tipo; 
	public $comprobante_pago_tipo;
	public $id_transaccion_motivo; 
	public $cod_transaccion_motivo; 
	public $transaccion_motivo;
	public $id_transaccion_grupo; 
	public $cod_transaccion_grupo;
	public $transaccion_grupo;
	public $transaccion_grupo_factor;
	public $tipo_objeto = "comprobante_pago_tipo_transaccion_motivo";
}


class ComprobantePagoDA
{
   	private $conn;
	public function __construct()
	{
		
		$file = "config.xml";
			
		$xml = simplexml_load_file($file);
			 
		#coneccion con el MDB en MySql
		//$link = mysql_connect("localhost","peruingc","banner2010", false, 65536)
		try
        {
            $connection_string = "mysql:host=".$xml->database_configuration[0]->host[0].";";
            $connection_string = $connection_string."dbname=".$xml->database_configuration[0]->database[0];
                
            $this->conn = new PDO($connection_string, 
                $xml->database_configuration[0]->username[0], 
                $xml->database_configuration[0]->password[0]
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
        }catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
	}
	
	public function Listar($filtro)
	{
		if($filtro != "")
			$filtro = "WHERE $filtro";
		else
			$filtro = "";
		
		$query = "SELECT cp.id, cp.id_tipo_origen, cpto.codigo cod_tipo_origen, cpto.descripcion tipo_origen, cp.fecha_hora_registro, 
			cp.id_comprobante_pago_tipo, cpt.codigo cod_comprobante_pago_tipo, cpt.descripcion_corta comprobante_pago_tipo, 
			cp.id_comp_pago_agente_tipo, cp.id_comp_pago_agente, cp.id_tipo_documento, td.codigo cod_tipo_documento, 
			td.descripcion tipo_documento, cp.nro_documento, cp.nro_comprobante, cp.monto_neto_mn, cp.monto_impuesto_mn, 
			cp.monto_otros_impuestos_mn, cp.monto_percepcion_mn, cp.monto_total_mn, cp.id_centro, ce.codigo cod_centro, ce.descripcion centro, cp.auto_key, 
			cp.flag_anulado, cp.flag_post, cpat.codigo cod_agente_tipo, cpat.descripcion agente_tipo, 
			CASE cp.id_comp_pago_agente_tipo WHEN 1 THEN pr.razon_social WHEN 2 THEN UPPER(CONCAT(cl.nombres, ' ', cl.apellidos)) END comp_pago_agente
			FROM comprobante_pago cp
			INNER JOIN centro ce ON cp.id_centro = ce.id
			INNER JOIN comp_pago_tipo_origen cpto ON cp.id_tipo_origen = cpto.id
			INNER JOIN comprobante_pago_tipo cpt ON cp.id_comprobante_pago_tipo = cpt.id
			INNER JOIN comprobante_pago_agente_tipo cpat ON cp.id_comp_pago_agente_tipo = cpat.id
			INNER JOIN tipo_documento td ON cp.id_tipo_documento = td.id
			LEFT JOIN cliente cl ON cp.id_comp_pago_agente = cl.id AND cp.id_comp_pago_agente_tipo = 2
			LEFT JOIN proveedor pr ON cp.id_comp_pago_agente = pr.id AND cp.id_comp_pago_agente_tipo = 1 $filtro";
			
		//echo "Query: $query</br>";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            $lista = NULL;
            
            if($result->rowCount()>0)
            {
				$lista = array();                	
				foreach($result as $row)                    
				//while($row = $result->fetch_assoc())
				{
					$obj = new ComprobantePago();
						
					$obj->id = $row["id"];
					$obj->auto_key = $row["auto_key"];
					$obj->id_centro = $row["id_centro"];
					$obj->cod_centro = $row["cod_centro"];
					$obj->centro = $row["centro"];
					$obj->id_tipo_origen = $row["id_tipo_origen"];
					$obj->cod_tipo_origen = $row["cod_tipo_origen"];
					$obj->tipo_origen = $row["tipo_origen"];
					$obj->fecha_hora_registro = $row["fecha_hora_registro"];
					$obj->id_comprobante_pago_tipo = $row["id_comprobante_pago_tipo"];
					$obj->cod_comprobante_pago_tipo = $row["cod_comprobante_pago_tipo"];
					$obj->comprobante_pago_tipo = $row["comprobante_pago_tipo"];
					$obj->id_comp_pago_agente_tipo = $row["id_comp_pago_agente_tipo"];
					$obj->comp_pago_agente = $row["comp_pago_agente"];
					$obj->cod_agente_tipo = $row["cod_agente_tipo"];
					$obj->agente_tipo = $row["agente_tipo"];
					$obj->id_comp_pago_agente = $row["id_comp_pago_agente"];
					$obj->id_tipo_documento = $row["id_tipo_documento"];
					$obj->cod_tipo_documento = $row["cod_tipo_documento"];
					$obj->tipo_documento = $row["tipo_documento"];
					$obj->nro_documento = $row["nro_documento"];
					$obj->nro_comprobante = $row["nro_comprobante"];
					$obj->monto_neto_mn = $row["monto_neto_mn"];
					$obj->monto_impuesto_mn = $row["monto_impuesto_mn"];
					$obj->monto_percepcion_mn = $row["monto_percepcion_mn"];
					$obj->monto_otros_impuestos_mn = $ro["monto_otros_impuestos_mn"];
					$obj->monto_total_mn = $row["monto_total_mn"];
					$obj->flag_anulado = $row["flag_anulado"];
					$obj->flag_post = $row["flag_post"];
							
					$lista[] = $obj;
							
				}               
			
		}
            
		return $lista;
	}
		
	public function ListarTipos($filtro)
	{
			
		if($filtro != "")
			$filtro = "WHERE $filtro";
			
		$query = "SELECT id, codigo, descripcion, descripcion_corta FROM comprobante_pago_tipo $filtro";
			
		try
        {
            $result = $this->conn->query($query);    
        }catch(PDOException $e)
        {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
        }
            
        $lista = NULL;
            
        if($result->rowCount()>0)
        {
            $lista = array();
			foreach($result as $row)                    
				//while($row = $result->fetch_assoc())
			{
				$obj = new ComprobantePagoTipo();
						
				$obj->id = $row['id'];
				$obj->codigo = $row['codigo'];
				$obj->descripcion = $row['descripcion'];
				$obj->descripcion_corta = $row['descripcion_corta'];
							
				$lista[] = $obj;
							
					
			}               
			
        }
		return $lista;
	}
	

	public function ListarResumen($filtro)
	{
		if($filtro != "")
			$filtro = "WHERE $filtro";
		
		$query = "SELECT cp.id_centro, c.descripcion centro, cpt.descripcion comprobante_pago_tipo, SUM(cp.monto_neto_mn) monto_neto_mn, SUM(cp.monto_impuesto_mn) monto_impuesto_mn, SUM(cp.monto_percepcion_mn) monto_percepcion_mn, 
			SUM(monto_otros_impuestos_mn) monto_otros_impuestos_mn, SUM(monto_total_mn) monto_total_mn
			FROM comprobante_pago cp
			INNER JOIN centro c ON cp.id_centro = c.id
			INNER JOIN comprobante_pago_tipo cpt ON cp.id_comprobante_pago_tipo = cpt.id $filtro
			GROUP BY cp.id_centro, c.descripcion, cpt.descripcion
			ORDER BY cp.id_centro";
			
		//echo "Query: $query</br>";
	
		try
        {
            $result = $this->conn->query($query);    
        }catch(PDOException $e)
        {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
        }
            
        $lista = NULL;
            
        if($result->rowCount()>0)
        {
			$lista = array();
				
			if(count($result) > 0)
			{
				foreach($result as $row)                    
				//while($row = $result->fetch_assoc())
				{
					$obj = new ComprobantePago();
					$obj->id_centro = $row["id_centro"];
					$obj->centro = $row["centro"];
					$obj->comprobante_pago_tipo = $row["comprobante_pago_tipo"];
					$obj->monto_neto_mn = $row["monto_neto_mn"];
					$obj->monto_impuesto_mn = $row["monto_impuesto_mn"];
					$obj->monto_otros_impuestos_mn = $ro["monto_otros_impuestos_mn"];
					$obj->monto_total_mn = $row["monto_total_mn"];
					$obj->monto_percepcion_mn = $row["monto_percepcion_mn"];
					
					$lista[] = $obj;

				}
            }
		return $lista;
	   }

    }
	
	public function ListarComprobantePagoTipoTransaccionMotivo($filtro)
	{
		if($filtro != "")
			$filtro = "WHERE $filtro";
		
		$query = "SELECT cpttm.id, cpttm.id_comprobante_pago_tipo, cpt.codigo cod_comprobante_pago_tipo, cpt.descripcion_corta comprobante_pago_tipo,
			tm.id id_transaccion_motivo, tm.codigo cod_transaccion_motivo, tm.descripcion transaccion_motivo, tm.id_transaccion_grupo, tg.codigo cod_transaccion_grupo,
			tg.descripcion transaccion_grupo, tg.factor transaccion_grupo_factor
			FROM comprobante_pago_tipo_transaccion_motivo cpttm
			INNER JOIN comprobante_pago_tipo cpt ON cpttm.id_comprobante_pago_tipo = cpt.id
			INNER JOIN transaccion_motivo tm ON cpttm.id_transaccion_motivo = tm.id
			INNER JOIN transaccion_grupo tg ON tm.id_transaccion_grupo = tg.id $filtro";
			
		//echo "Query: $query</br>";
	
		try
        {
            $result = $this->conn->query($query);    
        }catch(PDOException $e)
        {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
        }
            
        $lista = NULL;
            
        if($result->rowCount()>0)
        {
            $lista = array();
				
            foreach($result as $row)                    
				//while($row = $result->fetch_assoc())
            {
                $obj = new ComprobantePagoTipoTransaccionMotivo();
				$obj->id = $row["id"];
				$obj->id_comprobante_pago_tipo = $row["id_comprobante_pago_tipo"];;
				$obj->cod_comprobante_pago_tipo = $row["cod_comprobante_pago_tipo"]; 
				$obj->comprobante_pago_tipo = $row["comprobante_pago_tipo"];
				$obj->id_transaccion_motivo = $row["id_transaccion_motivo"];
				$obj->cod_transaccion_motivo = $row["cod_transaccion_motivo"]; 
				$obj->transaccion_motivo = $row["transaccion_motivo"];
				$obj->id_transaccion_grupo = $row["id_transaccion_grupo"];
				$obj->cod_transaccion_grupo = $row["cod_transaccion_grupo"];
				$obj->transaccion_grupo = $row["transaccion_grupo"];
				$obj->transaccion_grupo_factor = $row["transaccion_grupo_factor"];
					
				$lista[] = $obj;

            }
        }          
		
		return $lista;
	}
	
	public function Modificar($obj)
	{
				
		if(is_null($obj->fecha_hora_registro))
			$fecha_hora_registro = "NULL";
		else 
			$fecha_hora_registro = "'$obj->fecha_hora_registro'";
			
		if(is_null($obj->monto_otros_impuestos_mn))
			$monto_otros_impuestos_mn = 0;
		else 
			$monto_otros_impuestos_mn = "$obj->monto_otros_impuestos_mn";
		
		$query = "UPDATE comprobante_pago
			SET fecha_hora_registro = $fecha_hora_registro,
			id_comp_pago_agente = $obj->id_comp_pago_agente,
			id_tipo_documento = $obj->id_tipo_documento,
			nro_documento = '$obj->nro_documento',
			monto_neto_mn = $obj->monto_neto_mn,
			monto_impuesto_mn = $obj->monto_impuesto_mn,
			monto_percepcion_mn = $obj->monto_percepcion_mn,
			monto_otros_impuestos_mn = $monto_otros_impuestos_mn,
			monto_total_mn = $obj->monto_total_mn,			
			flag_anulado = $obj->flag_anulado,
			flag_post = $obj->flag_post
			WHERE id = $obj->id";
			
		//echo "Query: $query</br>";
			
		try
        {
            $result = $this->conn->prepare($query);
            $result->execute();    
                
            $last_inserted_id = $this->conn->lastInsertId();
            $affected_rows = $result->rowCount();
                
        }catch(PDOException $e)
        {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
        }
	}
	
	public function ListarSeries($filtro)
	{
			
		if($filtro != "")
			$filtro = "WHERE $filtro";
			
		$query = "SELECT id, id_centro, id_comprobante_pago_tipo, nro_serie, flag_habilitado FROM comprobante_pago_serie $filtro";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            $lista = NULL;
            
            if($result->rowCount()>0)
            {
        	   $lista = array();
				
				foreach($result as $row)                    
				//while($row = $result->fetch_assoc())
				{
					$obj = new ComprobantePagoSerie();
					
					$obj->id = $row['id'];
					$obj->id_centro = $row['id_centro'];
					$obj->id_comprobante_pago_tipo = $row['id_comprobante_pago_tipo'];
					$obj->nro_serie = $row['nro_serie'];
					$obj->flag_habilitado = $row["flag_habilitado"];					
						
					$lista[] = $obj;
						
				}               
			
		}
            
		return $lista;
	}
		
	public function Registrar($obj)
	{
		if(is_null($obj->fecha_hora_registro))
			$fecha_hora_registro = "NULL";
		else 
			$fecha_hora_registro = "'$obj->fecha_hora_registro'";
		
		
		$query = "INSERT INTO comprobante_pago (id_tipo_origen, fecha_hora_registro, id_comprobante_pago_tipo, id_comp_pago_agente_tipo, id_tipo_documento,
			nro_documento, nro_comprobante, monto_neto_mn, monto_impuesto_mn, monto_percepcion_mn, monto_otros_impuestos_mn, monto_total_mn, id_centro, auto_key, flag_anulado, 
			flag_post, id_comp_pago_agente)
			VALUES ($obj->id_tipo_origen, $fecha_hora_registro, $obj->id_comprobante_pago_tipo, $obj->id_comp_pago_agente_tipo,
				$obj->id_tipo_documento, '$obj->nro_documento', '$obj->nro_comprobante', $obj->monto_neto_mn, $obj->monto_impuesto_mn, 
				$obj->monto_percepcion_mn, $obj->monto_otros_impuestos_mn, $obj->monto_total_mn, $obj->id_centro, '$obj->auto_key', $obj->flag_anulado, 
				$obj->flag_post, $obj->id_comp_pago_agente);";
					
		//echo "Query: $query</br>";
			
		try
        {
            $result = $this->conn->prepare($query);
            $result->execute();    
                
            $last_inserted_id = $this->conn->lastInsertId();
            $affected_rows = $result->rowCount();
                
        }catch(PDOException $e)
        {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
        }
		
	}
}

class ComprobantePagoBLO
{
	private function Listar($filtro)
	{
		$objDA = new ComprobantePagoDA();
		return $objDA->Listar($filtro);				
	}
		
	private function ListarTipos($filtro)
	{
		$objDA = new ComprobantePagoDA();
		return $objDA->ListarTipos($filtro);
	}
	
	private function ListarComprobantePagoTipoTransaccionMotivo($filtro)
	{
		$objDA = new ComprobantePagoDA();
		return $objDA->ListarComprobantePagoTipoTransaccionMotivo($filtro);
	}
	
	public function RetornarTipoXId($id)
	{
		$lista = $this->ListarTipos("id = $id");
		if(!is_null($lista))
			if(count($lista) > 0)	
				return $lista[0];					
			else
				return NULL;
		else
			return NULL;
				
	}
		
	public function RetornarXKey($comp_key)
	{
		$lista = $this->Listar("cp.auto_key = '$comp_key'");
		if(!is_null($lista))
			if(count($lista) > 0)	
				return $lista[0];
					
			else
				return NULL;
		else
			return NULL;
				
	}
	
	public function RetornarXId($id)
	{
		$lista = $this->Listar("cp.id = $id");
		if(!is_null($lista))
			if(count($lista) > 0)	
				return $lista[0];
					
			else
				return NULL;
		else
			return NULL;
				
	}
		
	public function Modificar($obj)
	{
			$objDA = new ComprobantePagoDA();
			$objDA->Modificar($obj);
	}
	
	public function RegistrarSinValidar($obj)
	{
		$objDA = new ComprobantePagoDA();
		$objDA->Registrar($obj);
	}
		
	public function Registrar($obj)
	{
		$resultado = new OperacionResultado();
		$objDA = new ComprobantePagoDA();
		$objDA->Registrar($obj);
			
		$comp = $this->RetornarXKey($obj->auto_key);
			
		if(!is_null($comp))
		{
			$resultado->id = $comp->id;
			$resultado->codigo = "02";
			$resultado->isOK = TRUE;
			$resultado->mensaje = "Comprobante Creado!";
		}
		else
		{
			$resultado->id = 0;
			$resultado->codigo = "03";
			$resultado->isOK = FALSE;
			$resultado->mensaje = "Comprobante NO Creado!";
		}
			
		return $resultado;
	}
		
	public function ListarTiposTodos()
	{
		return $this->ListarTipos("");
	}
	
	public function ListarSerieXIdComprobantePagoTipo($id_comprobante_pago_tipo, $id_centro)
	{
		$objDA = new ComprobantePagoDA();
		return $objDA->ListarSeries("id_comprobante_pago_tipo = $id_comprobante_pago_tipo AND id_centro = $id_centro AND flag_habilitado = 1");
	}
	
	public function ListarXRangoNroComprobante($id_centro, $id_tipo_origen, $nro_serie, $id_comprobante_pago_tipo, $nro_inicio, $nro_fin, $fecha_inicio, $fecha_fin)
	{
		$filtro = "cp.id_centro = $id_centro AND LEFT(cp.nro_comprobante, 4) = '$nro_serie' AND CAST(RIGHT(cp.nro_comprobante,6) AS UNSIGNED) >= $nro_inicio AND
		CAST(RIGHT(cp.nro_comprobante,6) AS UNSIGNED) <= $nro_fin AND cp.id_comprobante_pago_tipo = $id_comprobante_pago_tipo AND cp.fecha_hora_registro >= '$fecha_inicio'
		AND cp.fecha_hora_registro <= '$fecha_fin' AND cp.id_tipo_origen = $id_tipo_origen ORDER BY 1 ASC";
		
		return $this->Listar($filtro); 		
	}
	
	public function ListarXRangoNroComprobanteSinFecha($id_centro, $id_tipo_origen, $nro_serie, $id_comprobante_pago_tipo, $nro_inicio, $nro_fin )
	{
		$filtro = "cp.id_centro = $id_centro AND LEFT(cp.nro_comprobante, 4) = '$nro_serie' AND CAST(RIGHT(cp.nro_comprobante, 6) AS UNSIGNED) >= $nro_inicio AND
		CAST(RIGHT(cp.nro_comprobante, 6) AS UNSIGNED) <= $nro_fin AND cp.id_comprobante_pago_tipo = $id_comprobante_pago_tipo AND cp.id_tipo_origen = $id_tipo_origen 
		ORDER BY 1 ASC";
		
		return $this->Listar($filtro); 		
	}
	
	private function ListarResumen($filtro)
	{
		$objDA = new ComprobantePagoDA();
		return $objDA->ListarResumen($filtro);
	}
	
	public function ListarResumenCompEmitidosXRangoFechas($id_centro, $fecha_inicio, $fecha_fin)
	{
		$filtro = "cp.id_tipo_origen = 1 AND cp.flag_anulado = 0 AND cp.fecha_hora_registro >= '$fecha_inicio 00:00:00' AND cp.fecha_hora_registro <= '$fecha_fin 23:59:59' AND cp.id_centro = $id_centro";
		
		return $this->ListarResumen($filtro);
	}
	
	public function ListarComprobantePagoTipoXIdTransaccionMotivo($id_transaccion_motivo){		
		$objDA = new ComprobantePagoDA();
		return $objDA->ListarComprobantePagoTipoTransaccionMotivo("cpttm.id_transaccion_motivo = $id_transaccion_motivo AND cpttm.flag_habilitado = 1");
		
	}
			
}
	
	
?>