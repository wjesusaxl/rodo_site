<?php
    
    class CajaTurno
    {
    	public $id;        	
        public $auto_key;
        public $id_centro;
		public $id_caja;
		public $cod_caja;
		public $caja;
		public $cod_centro;
		public $centro;
		public $id_usuario;
		public $id_almacen;
		public $usuario;
		public $fecha_hora_inicio;
		public $fecha_hora_fin;
		public $id_transaccion_inicio;
		public $id_transaccion_fin;
		public $saldo_inicial_mn; 
		public $total_ingreso_efectivo_mn;
		public $total_egreso_efectivo_mn;
		public $total_transacciones_mn;
		public $total_credito_mn; 
		public $comentarios;
		public $id_estado;
		public $cod_estado;
		public $estado;        
    }
    
    class CajaTurnoDA
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
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT ct.id, ct.auto_key, ct.id_centro, ce.codigo cod_centro, ce.descripcion centro, ct.id_usuario, u.login usuario,
				ct.fecha_hora_inicio, ct.fecha_hora_fin, ct.id_transaccion_inicio, ct.id_transaccion_fin, ct.saldo_inicial_mn, 
				ct.total_ingreso_efectivo_mn, ct.total_egreso_efectivo_mn, ct.total_transacciones_mn, ct.total_credito_mn, 
				ct.comentarios, ct.id_estado, cte.codigo cod_estado, cte.descripcion estado, ct.id_caja, ca.codigo cod_caja, ca.descripcion caja,
				id_almacen_default
				FROM caja_turno ct
				INNER JOIN caja ca ON ct.id_caja = ca.id
				INNER JOIN centro ce ON ct.id_centro = ce.id
				INNER JOIN usuario u ON u.id = ct.id_usuario
				INNER JOIN caja_turno_estado cte ON ct.id_estado = cte.id $filtro";
			
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
                        $obj = new CajaTurno();
                        $obj->id = $row["id"];        	
				        $obj->auto_key = $row["auto_key"];
				        $obj->id_centro = $row["id_centro"];
						$obj->cod_centro = $row["cod_centro"];
						$obj->centro = $row["centro"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario = $row["usuario"];
						$obj->id_almacen = $row["id_almacen_default"];
						$obj->fecha_hora_inicio = $row["fecha_hora_inicio"];
						$obj->fecha_hora_fin = $row["fecha_hora_fin"];
						$obj->id_transaccion_inicio = $row["id_transaccion_inicio"];
						$obj->id_transaccion_fin = $row["id_transaccion_fin"];
						$obj->saldo_inicial_mn = $row["saldo_inicial_mn"]; 
						$obj->total_ingreso_efectivo_mn = $row["total_ingreso_efectivo_mn"];
						$obj->total_egreso_efectivo_mn = $row["total_egreso_efectivo_mn"];
						$obj->total_transacciones_mn = $row["total_transacciones_mn"];
						$obj->total_credito_mn = $row["total_credito_mn"]; 
						$obj->comentarios = $row["comentarios"];
						$obj->id_estado = $row["id_estado"];
						$obj->cod_estado = $row["cod_estado"];
						$obj->estado = $row["estado"];
						$obj->id_caja = $row["id_caja"];
						$obj->cod_caja = $row["cod_caja"];
						$obj->caja = $row["caja"];
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}

		public function Registrar($obj)
		{
			
			if(!is_null($obj->fecha_hora_inicio))
				$fecha_hora_inicio = "'$obj->fecha_hora_inicio'";
			else
				$fecha_hora_inicio = "NULL";
			
			if(!is_null($obj->fecha_hora_fin))
				$fecha_hora_fin = "'$obj->fecha_hora_fin'";
			else
				$fecha_hora_fin = "NULL";
			
			if(!is_null($obj->id_transaccion_inicio))
				$id_transaccion_inicio = $obj->id_transaccion_inicio;
			else 
				$id_transaccion_inicio = "NULL";
			
			if(!is_null($obj->id_transaccion_fin))
				$id_transaccion_fin = $obj->id_transaccion_fin;
			else 
				$id_transaccion_fin = "NULL";
			
			if(!is_null($obj->saldo_inicial_mn))
				$saldo_inicial_mn = $obj->saldo_inicial_mn;
			else 
				$saldo_inicial_mn = "NULL";
			
			if(!is_null($obj->total_ingreso_efectivo_mn))
				$total_ingreso_efectivo_mn = $obj->total_ingreso_efectivo_mn;
			else 
				$total_ingreso_efectivo_mn = "NULL";
			
			if(!is_null($obj->total_egreso_efectivo_mn))
				$total_egreso_efectivo_mn = $obj->total_egreso_efectivo_mn;
			else 
				$total_egreso_efectivo_mn = "NULL";
			
			if(!is_null($obj->total_transacciones_mn))
				$total_transacciones_mn = $obj->total_transacciones_mn;
			else 
				$total_transacciones_mn = "NULL";
			
			if(!is_null($obj->total_credito_mn))
				$total_credito_mn = $obj->total_credito_mn;
			else 
				$total_credito_mn = "NULL";
			
			if(!is_null($obj->id_almacen))
				$id_almacen = $obj->id_almacen;
			else
				$id_almacen = "NULL";
			
			$obj->comentarios = mysql_escape_string($obj->comentarios);
			
			$query = "INSERT INTO caja_turno(auto_key, id_centro, id_caja, id_usuario, fecha_hora_inicio, fecha_hora_fin, id_transaccion_inicio,
			id_transaccion_fin, saldo_inicial_mn, total_ingreso_efectivo_mn, total_egreso_efectivo_mn, total_transacciones_mn, total_credito_mn,
			comentarios, id_estado, id_almacen_default) VALUES(
			'$obj->auto_key', $obj->id_centro, $obj->id_caja, $obj->id_usuario, $fecha_hora_inicio, $fecha_hora_fin, $id_transaccion_inicio,
			$id_transaccion_fin, $saldo_inicial_mn, $total_ingreso_efectivo_mn, $total_egreso_efectivo_mn, $total_transacciones_mn, $total_credito_mn,
			'$obj->comentarios', $obj->id_estado, $id_almacen)";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result = $this->conn->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		}	

		public function Actualizar($obj)
		{
			if(!is_null($obj->fecha_hora_inicio))
				$fecha_hora_inicio = "'$obj->fecha_hora_inicio'";
			else
				$fecha_hora_inicio = "NULL";
			
			if(!is_null($obj->fecha_hora_fin))
				$fecha_hora_fin = "'$obj->fecha_hora_fin'";
			else
				$fecha_hora_fin = "NULL";
			
			if(!is_null($obj->id_transaccion_inicio))
				$id_transaccion_inicio = $obj->id_transaccion_inicio;
			else 
				$id_transaccion_inicio = "NULL";
			
			if(!is_null($obj->saldo_inicial))
				$saldo_inicial = $obj->saldo_inicial;
			else 
				$saldo_inicial = "NULL";
			
			if(!is_null($obj->total_ingreso_efectivo_mn))
				$total_ingreso_efectivo_mn = $obj->total_ingreso_efectivo_mn;
			else 
				$total_ingreso_efectivo_mn = "NULL";
			
			if(!is_null($obj->total_egreso_efectivo_mn))
				$total_egreso_efectivo_mn = $obj->total_egreso_efectivo_mn;
			else 
				$total_egreso_efectivo_mn = "NULL";
			
			if(!is_null($obj->total_transacciones_mn))
				$total_transacciones_mn = $obj->total_transacciones_mn;
			else 
				$total_transacciones_mn = "NULL";
			
			if(!is_null($obj->total_credito_mn))
				$total_credito_mn = $obj->total_credito_mn;
			else 
				$total_credito_mn = "NULL";
			
			if(!is_null($obj->id_almacen))
				$id_almacen = $obj->id_almacen;
			else
				$id_almacen = "NULL";
			
			$obj->comentarios = mysql_escape_string($obj->comentarios);
			
			/*$query = "UPDATE caja_turno
			SET auto_key = '$obj->auto_key',
			id_centro = $obj->id_centro,
			id_caja = $obj->id_caja,
			id_usuario = $obj->id_usuario,
			fecha_hora_inicio = $fecha_hora_inicio,*/
			$query= "UPDATE caja_turno
			SET fecha_hora_fin = $fecha_hora_fin,
			id_transaccion_inicio = $id_transaccion_inicio,
			id_transaccion_fin = $id_transaccion_fin,
			saldo_inicial_mn = $saldo_inicial_mn, 
			total_ingreso_efectivo_mn = $total_ingreso_efectivo_mn,
			total_egreso_efectivo_mn = $total_egreso_efectivo_mn,
			total_transacciones_mn = $total_transacciones_mn,
			total_credito_mn = $total_credito_mn,
			comentarios = '$obj->comentarios',
			id_estado = $obj->id_estado,
			id_almacen_default = $id_almacen;
			WHERE id = $obj->id";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result = $this->conn->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}
    }

	class CajaTurnoBLO
	{
		public function Listar($filtro)
		{
			$objDA = new CajaTurnoDA();
			$lista = $objDA->Listar($filtro);
			return $lista;
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("ct.id = $id");
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;
		}
		
		public function Registrar($obj)
		{
			$objDA = new CajaTurnoDA();
			$objDA->Registrar($obj);
		}
		
		public function Actualizar($obj)
		{
			$objDA = new CajaTurnoDA();
			$objDA->Actualizar($obj);
		}
		
		public function RetornarCajaTurnoActual($id_caja)
		{
			
			$lista = $this->Listar("ct.id_estado = 1 AND ct.id_caja = $id_caja");			
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;
		}
		
		public function ListarTurnosXDia($id_caja)
		{
			$lista = $this->Listar("ct.fecha_hora_inicio >= CONCAT(CAST(CURDATE() AS CHAR),' 00:00:00') AND ct.id_caja = $id_caja ORDER BY ct.id DESC");
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;			
		}
	}
?>