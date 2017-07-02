<?php

class RegistroVentaItem
{
	public $fecha_hora_registro;
	public $fecha;
	public $cod_comprobante_pago_tipo;
	public $nro_comprobante;
	public $periodo;
	public $monto_neto_mn;
	public $monto_impuesto_mn; 
	public $monto_percepcion_mn;
	public $monto_total_mn;
	public $monto_otros_impuestos_mn;
	public $id_centro;
	public $flag_anulado;
	public $flag_post; 
	public $cliente;
}



class RegistroVentaDA
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
                echo "[ERROR: ". $e->getMessage();
            }
	}
	
	public function ListarItem($filtro)
	{
		if($filtro != "")
			$filtro = "WHERE $filtro";
		else
			$filtro = "";
		
		$query = "SELECT cp.fecha_hora_registro, 
			cpt.codigo cod_comprobante_pago_tipo,  cp.nro_comprobante, cp.monto_neto_mn, cp.monto_impuesto_mn, 
			cp.monto_percepcion_mn, cp.monto_total_mn, cp.id_centro,cp.flag_anulado, cp.flag_post, 
			CASE cp.id_comp_pago_agente_tipo WHEN 1 THEN pr.razon_social WHEN 2 THEN UPPER(CONCAT(cl.nombres, ' ', cl.apellidos)) END cliente,
			cp.monto_otros_impuestos_mn
			FROM comprobante_pago cp
			INNER JOIN centro ce ON cp.id_centro = ce.id
			INNER JOIN comp_pago_tipo_origen cpto ON cp.id_tipo_origen = cpto.id
			INNER JOIN comprobante_pago_tipo cpt ON cp.id_comprobante_pago_tipo = cpt.id
			INNER JOIN comprobante_pago_agente_tipo cpat ON cp.id_comp_pago_agente_tipo = cpat.id
			INNER JOIN tipo_documento td ON cp.id_tipo_documento = td.id
			LEFT JOIN cliente cl ON cp.id_comp_pago_agente = cl.id AND cp.id_comp_pago_agente_tipo = 2
			LEFT JOIN proveedor pr ON cp.id_comp_pago_agente = pr.id AND cp.id_comp_pago_agente_tipo = 1 AND cp.flag_anulado = 0
			$filtro
			ORDER BY 1, 2, 3 ";
			
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
					$obj = new RegistroVentaItem();
					
					$obj->fecha_hora_registro = $row["fecha_hora_registro"]; 
					$obj->cod_comprobante_pago_tipo = $row["cod_comprobante_pago_tipo"];
					$obj->nro_comprobante = $row["nro_comprobante"];
					$obj->monto_neto_mn = $row["monto_neto_mn"];
					$obj->monto_impuesto_mn = $row["monto_impuesto_mn"]; 
					$obj->monto_percepcion_mn = $row["monto_percepcion_mn"];
					$obj->monto_total_mn = $row["monto_total_mn"];
					$obj->monto_otros_impuestos_mn = is_null($row["monto_otros_impuestos_mn"]) ? 0.00 : $row["monto_otros_impuestos_mn"];
					$obj->id_centro = $row["id_centro"];
					$obj->flag_anulado = $row["flag_anulado"];
					$obj->flag_post = $row["flag_post"]; 
					$obj->cliente = strtoupper($row["cliente"]);
					
					$obj->periodo = date("Y-m", strtotime( date('Y-m-d H:i:s', strtotime($obj->fecha_hora_registro)) ));
					$obj->fecha = date("d/m", strtotime( date('Y-m-d H:i:s', strtotime($obj->fecha_hora_registro)) ));
						
					$lista[] = $obj;
						
				}				
			
		}
		return $lista;
	}
		
}

class RegistroVentaBLO
{
	private function ListarItem($filtro)
	{
		$objDA = new RegistroVentaDA();
		return $objDA->ListarItem($filtro);
	}
	
	public function ListarItemXFecha($fecha_inicio, $fecha_fin)
	{
		$filtro = "cp.fecha_hora_registro between '$fecha_inicio' and '$fecha_fin'";
		$lista = $this->ListarItem($filtro);
		
		if(!is_null($lista))
			if(count($lista) > 0)	
				return $lista;
					
			else
				return NULL;
		else
			return NULL;
	}
			
}
	
	
?>