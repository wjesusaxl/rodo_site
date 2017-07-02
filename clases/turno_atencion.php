<?php
    
    class TurnoAtencion
    {
    	public $id;
		public $codigo;
    	public $auto_key;
    	public $id_centro;
    	public $cod_centro;
    	public $centro;
    	public $id_usuario;
    	public $usuario;
		public $fecha_hora_inicio;
		public $fecha_hora_fin;
		public $saldo_inicial_mn; 
		public $total_ingreso_efectivo_mn;
		public $total_egreso_efectivo_mn;
		public $total_transacciones_mn;
		public $total_credito_mn; 
		public $comentarios;
		public $id_estado;
		public $cod_estado;
		public $estado;
		public $id_caja;
		public $cod_caja;
		public $caja;
		public $id_almacen;
		public $cod_almacen;
		public $almacen;
		public $usuario_nombres_apellidos;
    }
    
    class TurnoAtencionDA
    {
    	private $conn;
		public function __construct()
		{
			
			$file = "config.xml";
			$xml = simplexml_load_file($file);
			 
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
			
			$query = "SELECT ta.id, ta.auto_key, ta.id_centro, ce.codigo cod_centro, ce.descripcion centro, ta.id_usuario, u.login usuario,
				ta.fecha_hora_inicio, ta.fecha_hora_fin, ta.saldo_inicial_mn, 
				ta.total_ingreso_efectivo_mn, ta.total_egreso_efectivo_mn, ta.total_transacciones_mn, ta.total_credito_mn, 
				ta.comentarios, ta.id_estado, tae.codigo cod_estado, tae.descripcion estado, ta.id_caja, ca.codigo cod_caja, ca.descripcion caja,
				ta.id_almacen, a.codigo cod_almacen, a.descripcion almacen, CONCAT(CONCAT(u.nombres, ' '), u.apellidos) usuario_nombres_apellidos
				FROM turno_atencion ta
				INNER JOIN caja ca ON ta.id_caja = ca.id
				INNER JOIN centro ce ON ta.id_centro = ce.id
				INNER JOIN usuario u ON u.id = ta.id_usuario
				INNER JOIN turno_atencion_estado tae ON ta.id_estado = tae.id
				INNER JOIN almacen a ON ta.id_almacen = a.id $filtro";
				
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
                        $obj = new TurnoAtencion();
                        $obj->id = $row["id"];
                        $obj->codigo = str_pad($obj->id, 6, "0", STR_PAD_LEFT);
				    	$obj->auto_key = $row["auto_key"];
				    	$obj->id_centro = $row["id_centro"];
				    	$obj->cod_centro = $row["cod_centro"];
				    	$obj->centro = $row["centro"];
				    	$obj->id_usuario = $row["id_usuario"];
				    	$obj->usuario = strtoupper($row["usuario"]);
						$obj->fecha_hora_inicio = $row["fecha_hora_inicio"];
						$obj->fecha_hora_fin = $row["fecha_hora_fin"];
						$obj->saldo_inicial_mn = $row["saldo_inicial_mn"]; 
						$obj->total_ingreso_efectivo_mn = $row["total_ingreso_efectivo_mn"];
						$obj->total_egreso_efectivo_mn = $row["total_egreso_efectivo_mn"];
						$obj->total_transacciones_mn = $row["total_transacciones_mn"];
						$obj->total_credito_mn = $row["total_credito_mn"]; 
						$obj->comentarios = strtoupper($row["comentarios"]);
						$obj->id_estado = $row["id_estado"];
						$obj->cod_estado = $row["cod_estado"];
						$obj->estado = strtoupper($row["estado"]);
						$obj->id_caja = $row["id_caja"];
						$obj->cod_caja = $row["cod_caja"];
						$obj->caja = strtoupper($row["caja"]);
						$obj->id_almacen = $row["id_almacen"];
						$obj->cod_almacen = $row["cod_almacen"];
						$obj->almacen = strtoupper($row["almacen"]);
						$obj->usuario_nombres_apellidos = strtoupper($row["usuario_nombres_apellidos"]);
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}

		public function Registrar($obj)
		{
			$fecha_hora_inicio = "NULL";
			$fecha_hora_fin = "NULL";
			$total_ingreso_efectivo_mn = "NULL";
			$total_egreso_efectivo_mn = "NULL";
			$total_transacciones_mn = "NULL";
			$total_credito_mn = "NULL";
			$saldo_inicial_mn = "NULL";
			$id_almacen = "NULL";
			
			if(!is_null($obj->fecha_hora_inicio))
				$fecha_hora_inicio = "'$obj->fecha_hora_inicio'";
			
			if(!is_null($obj->fecha_hora_fin))
				$fecha_hora_fin = "'$obj->fecha_hora_fin'";
			
			if(!is_null($obj->total_ingreso_efectivo_mn))
				$total_ingreso_efectivo_mn = $obj->total_ingreso_efectivo_mn;
			
			if(!is_null($obj->total_egreso_efectivo_mn))
				$total_egreso_efectivo_mn = $obj->total_egreso_efectivo_mn;
			
			if(!is_null($obj->total_transacciones_mn))
				$total_transacciones_mn = $obj->total_transacciones_mn;
			
			if(!is_null($obj->total_credito_mn))
				$total_credito_mn = $obj->total_credito_mn;
			
			if(!is_null($obj->id_almacen))
				$id_almacen = $obj->id_almacen;
			
			if(!is_null($obj->saldo_inicial_mn))
				$saldo_inicial_mn = $obj->saldo_inicial_mn;
			
			$obj->comentarios = mysql_escape_string($obj->comentarios);
			
			$query = "INSERT INTO turno_atencion(auto_key, id_centro, id_caja, id_usuario, fecha_hora_inicio, fecha_hora_fin, saldo_inicial_mn, total_ingreso_efectivo_mn, 
			total_egreso_efectivo_mn, total_transacciones_mn, total_credito_mn, comentarios, id_estado, id_almacen) VALUES(
			'$obj->auto_key', $obj->id_centro, $obj->id_caja, $obj->id_usuario, $fecha_hora_inicio, $fecha_hora_fin, $saldo_inicial_mn, $total_ingreso_efectivo_mn, 
			$total_egreso_efectivo_mn, $total_transacciones_mn, $total_credito_mn, '$obj->comentarios', $obj->id_estado, $id_almacen)";
			
			//echo $query."</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		}	

		public function Modificar($obj)
		{
			$fecha_hora_inicio = "NULL";
			$fecha_hora_fin = "NULL";
			$total_ingreso_efectivo_mn = "NULL";
			$total_egreso_efectivo_mn = "NULL";
			$total_transacciones_mn = "NULL";
			$total_credito_mn = "NULL";
			$saldo_inicial_mn = "NULL";
			$id_almacen = "NULL";
			
			
			if(!is_null($obj->fecha_hora_inicio))
				$fecha_hora_inicio = "'$obj->fecha_hora_inicio'";
			
			if(!is_null($obj->fecha_hora_fin))
				$fecha_hora_fin = "'$obj->fecha_hora_fin'";
			
			if(!is_null($obj->total_ingreso_efectivo_mn))
				$total_ingreso_efectivo_mn = $obj->total_ingreso_efectivo_mn;
			
			if(!is_null($obj->total_egreso_efectivo_mn))
				$total_egreso_efectivo_mn = $obj->total_egreso_efectivo_mn;
			
			if(!is_null($obj->total_transacciones_mn))
				$total_transacciones_mn = $obj->total_transacciones_mn;
			
			if(!is_null($obj->total_credito_mn))
				$total_credito_mn = $obj->total_credito_mn;
			
			if(!is_null($obj->id_almacen))
				$id_almacen = $obj->id_almacen;
			
			if(!is_null($obj->saldo_inicial_mn))
				$saldo_inicial_mn = $obj->saldo_inicial_mn;
			
			
			$obj->comentarios = mysql_escape_string($obj->comentarios);
			
			/*$query = "UPDATE turno_atencion
			SET auto_key = '$obj->auto_key',
			id_centro = $obj->id_centro,
			id_caja = $obj->id_caja,
			id_usuario = $obj->id_usuario,
			fecha_hora_inicio = $fecha_hora_inicio,*/
			$query= "UPDATE turno_atencion
			SET fecha_hora_fin = $fecha_hora_fin,
			saldo_inicial_mn = $saldo_inicial_mn, 
			total_ingreso_efectivo_mn = $total_ingreso_efectivo_mn,
			total_egreso_efectivo_mn = $total_egreso_efectivo_mn,
			total_transacciones_mn = $total_transacciones_mn,
			total_credito_mn = $total_credito_mn,
			comentarios = '$obj->comentarios',
			id_estado = $obj->id_estado,
			id_almacen = $id_almacen
			WHERE id = $obj->id";
			
			//echo "Query: $query</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}

		public function RetornarMontoTotalCuentaVentaXIdTurnoAtencion($filtro)
		{
		    $monto_total_mn = NULL;
            
			if($filtro != "")
				$filtro = " WHERE $filtro";
			
			$query = "SELECT IFNULL(SUM(cvd.cantidad * cvd.precio_total_mn),0) monto_total_mn
				FROM cuenta_venta_detalle cvd
				INNER JOIN cuenta_venta cv ON cvd.id_cuenta_venta = cv.id
				INNER JOIN turno_atencion ta ON cv.id_turno_atencion = ta.id $filtro";
				
			//echo "Query: $query</br>";
			
			   
            try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            if($result->rowCount()>0)
            {
                $row = $result->fetch();
                $monto_total_mn = $row["monto_total_mn"];
            }
            
            return $monto_total_mn;
		}
		
		public function RetornarMontoTotalTransacciones($filtro)
		{
		    $monto_total_mn = NULL;
			    
			if($filtro != "")
				$filtro = " WHERE $filtro";
			
			$query = "SELECT IFNULL(SUM(t.monto_total_mn), 0) monto_total_mn
				FROM transaccion t
				INNER JOIN turno_atencion ta ON t.id_turno_atencion = ta.id
				INNER JOIN transaccion_grupo tg ON t.id_transaccion_grupo = tg.id $filtro";
				
			 try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            if($result->rowCount()>0)
            {
                $row = $result->fetch();
                $monto_total_mn = $row["monto_total_mn"];
            }
            
            return $monto_total_mn;
		}
		
		public function ListarResumenProductosXIdTurno($id_turno_atencion)
		{
			$query = "SELECT cvd.id_producto, p.descripcion_corta, p.marca, p.nro_serie, SUM(cvd.cantidad) cantidad, SUM(cvd.cantidad * cvd.precio_total_mn) precio_total_mn
				FROM cuenta_venta_detalle cvd
				INNER JOIN v_producto p ON cvd.id_producto = p.id_producto
				INNER JOIN cuenta_venta cv ON cvd.id_cuenta_venta = cv.id
				INNER JOIN turno_atencion ta ON cv.id_turno_atencion = ta.id
				WHERE IFNULL(cvd.flag_anulado, 0) = 0 AND ta.id = $id_turno_atencion
				GROUP BY cvd.id_producto, p.descripcion_corta, p.marca, p.nro_serie
				ORDER BY p.marca, p.descripcion_corta";
				
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
						$obj = new CuentaVentaItem();
						
						$obj->id = $row["id"];
						$obj->id_producto = $row["id_producto"];
						$obj->descripcion_corta = strtoupper($row["descripcion_corta"]);
						$obj->marca = strtoupper($row["marca"]);
						$obj->nro_serie = strtoupper($row["nro_serie"]);
						$obj->cantidad = $row["cantidad"];
						$obj->precio_total_mn = $row["precio_total_mn"];
						$lista[] = $obj;
						
					}				
				
			}
			
			return $lista;
		}
		
		
    }
    
    

	class TurnoAtencionBLO
	{
		public function Listar($filtro)
		{
			$objDA = new TurnoAtencionDA();
			$lista = $objDA->Listar($filtro);
			return $lista;
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("ta.id = $id");
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;
		}
		
		public function RetornarXKey($turno_key)
		{
			$lista = $this->Listar("ta.auto_key = '$turno_key'");
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;
		}
		
		public function Registrar($obj)
		{
			$objDA = new TurnoAtencionDA();
			$objDA->Registrar($obj);
		}
		
		public function Modificar($obj)
		{
			$objDA = new TurnoAtencionDA();
			$objDA->Modificar($obj);
		}
		
		public function ListarTurnosActivos($id_caja)
		{
			return $this->Listar("ta.id_estado = 1 AND ta.id_caja = $id_caja ORDER BY ta.id DESC");	
		}
		
		public function ListarTurnosActivosXIdUsuario($id_caja, $id_usuario)
		{
			return $this->Listar("ta.id_estado = 1 AND ta.id_caja = $id_caja AND ta.id_usuario = $id_usuario ORDER BY ta.id DESC");	
		}
		
		public function ListarUltimosTurnos($id_caja)
		{
			return $this->Listar("ta.fecha_hora_inicio >= CONCAT(CAST(DATE_ADD(curdate(), INTERVAL -1 DAY) AS CHAR), ' 00:00:00') AND ta.id_caja = $id_caja ORDER BY ta.id DESC");
		}
		
		public function ListarTurnosXIdCentro($id_centro)
		{
			return $this->Listar("ta.id_centro = $id_centro ORDER BY ta.id DESC LIMIT 250");
		}
		
		public function ListarTurnosXIdUsuario($id_usuario, $id_centro)
		{
			return $this->Listar("ta.id_usuario = $id_usuario AND ta.id_centro = $id_centro ORDER BY ta.id DESC");	
		}
		
		public function RetornarMontoTotalCuentaVentaXIdTurnoAtencion($id_turno_atencion)
		{
			$objDA = new TurnoAtencionDA();
			
			$filtro = "ta.id = $id_turno_atencion AND cvd.flag_anulado = 0";
			
			return $objDA->RetornarMontoTotalCuentaVentaXIdTurnoAtencion($filtro);
		}
		
		public function ListarResumenProductosXIdTurno($id_turno_atencion)
		{
			$objDA = new TurnoAtencionDA();
			return $objDA->ListarResumenProductosXIdTurno($id_turno_atencion);
		}
		
		public function RetornarMontoTransaccionesPositivas($id_turno_atencion)
		{
			$filtro = "tg.factor > 0 AND t.flag_anulado = 0 AND t.id_turno_atencion = $id_turno_atencion";
			
			$taDA = new TurnoAtencionDA();
			
			return $taDA->RetornarMontoTotalTransacciones($filtro);
		}
		
		public function RetornarMontoTransaccionesNegativas($id_turno_atencion)
		{
			$filtro = "tg.factor < 0 AND t.flag_anulado = 0 AND t.id_turno_atencion = $id_turno_atencion";
			
			$taDA = new TurnoAtencionDA();
			
			return $taDA->RetornarMontoTotalTransacciones($filtro);	
			
		}
	}
?>