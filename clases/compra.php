<?php

    class Compra
    {
        public $id;
		public $compra_key;
        public $id_compra_tipo;
        public $cod_compra_tipo;
        public $compra_tipo;
        public $id_centro;
        public $cod_centro;
		public $centro;
		public $id_usuario;
		public $usuario;
		public $usuario_nombres;
		public $usuario_apellidos; 
		public $fecha;
		public $fecha_hora_registro;
		public $id_comprobante_pago_tipo;
		public $id_comprobante_pago;
		public $cod_comprobante_pago_tipo;
		public $comprobante_pago_tipo;
		public $id_proveedor;
		public $razon_social;
		public $id_tipo_documento;
		public $cod_proveedor_tipo_documento; 
		public $proveedor_tipo_documento;
		public $nro_documento;
		public $nro_comprobante;
		public $nombre_comercial;
		public $monto_neto_mn;
		public $monto_impuesto_mn;
		public $monto_percepcion_mn;
		public $monto_total_mn;
		public $id_transaccion;
		public $flag_anulada;
		public $id_movimiento;
		public $movimiento_key;
    }
	
	class CompraItem
	{
		public $id;
		public $id_compra;
		public $compra_key;
		public $id_producto;
		public $descripcion_corta;
		public $id_producto_categoria;
		public $producto_categoria;
		public $cantidad;
		public $id_marca;		
		public $marca;
		public $nro_serie;	
		public $precio_neto_unitario_mn;
		public $impuesto_unitario_mn;
		public $precio_total_unitario_mn;
		public $flag_anulado;	
	}
	
	class CompraTipo
	{
		public $id;
		public $codigo;
		public $descripcion;
		public $id_transaccion_motivo;
		public $cod_transaccion_motivo;
		public $transaccion_motivo;
		public $id_transaccion_grupo;
	}
    
	class CompraDA
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
        
		public function ListarTipos($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT ct.id, ct.codigo, ct.descripcion, ct.id_transaccion_motivo, tm.codigo cod_transaccion_motivo, tm.descripcion transaccion_motivo, tm.id_transaccion_grupo 
            	FROM compra_tipo ct INNER JOIN transaccion_motivo tm ON ct.id_transaccion_motivo = tm.id $filtro";
				
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
	                    $obj = new CompraTipo();
	                        
	                    $obj->id = $row["id"];
	                    $obj->codigo = $row["codigo"];
						$obj->descripcion = $row["descripcion"];
						$obj->id_transaccion_motivo = $row["id_transaccion_motivo"];
						$obj->cod_transaccion_motivo = $row["cod_transaccion_motivo"];
						$obj->transaccion_motivo = $row["transaccion_motivo"];
						$obj->id_transaccion_grupo = $row["id_transaccion_grupo"];
	                    
	                    $lista[] = $obj;
                	}               
                
            }
            
			return $lista;
		}
		
        public function Listar($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT co.id, co.compra_key, co.id_compra_tipo, ct.codigo cod_compra_tipo, ct.descripcion compra_tipo, co.id_centro, ce.codigo cod_centro,
				ce.descripcion centro, co.id_usuario, u.login usuario, u.nombres usuario_nombres, u.apellidos usuario_apellidos,  co.fecha, co.fecha_hora_registro, 
				cp.id_comprobante_pago_tipo, ctp.codigo cod_comprobante_pago_tipo, ctp.descripcion comprobante_pago_tipo, co.id_proveedor, p.razon_social, cp.id_tipo_documento, 
				td.codigo cod_proveedor_tipo_documento, td.descripcion proveedor_tipo_documento, cp.nro_comprobante, cp.monto_neto_mn, cp.monto_impuesto_mn, 
				cp.monto_percepcion_mn, cp.monto_total_mn, co.id_transaccion, co.id_comprobante_pago, co.id_comprobante_pago, co.flag_anulada, p.nombre_comercial,
				cp.nro_documento, co.id_movimiento, mo.auto_key movimiento_key
				FROM compra co
				INNER JOIN compra_tipo ct ON co.id_compra_tipo = ct.id
				INNER JOIN centro ce ON co.id_centro = ce.id
				INNER JOIN usuario u ON co.id_usuario = u.id
				INNER JOIN comprobante_pago cp ON co.id_comprobante_pago = cp.id
				INNER JOIN comprobante_pago_tipo ctp ON cp.id_comprobante_pago_tipo = ctp.id
				INNER JOIN tipo_documento td ON cp.id_tipo_documento = td.id
				INNER JOIN proveedor p ON co.id_proveedor = p.id
				LEFT JOIN movimiento mo ON co.id_movimiento = mo.id $filtro LIMIT 100";
				
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
	                    $obj = new Compra();
	                        
	                    $obj->id = $row["id"];
	                    $obj->compra_key = $row["compra_key"];
				        $obj->id_compra_tipo = $row["id_compra_tipo"];
				        $obj->cod_compra_tipo = $row["cod_compra_tipo"];
				        $obj->compra_tipo = $row["compra_tipo"];
				        $obj->id_centro = $row["id_centro"];
				        $obj->cod_centro = $row["cod_centro"];
						$obj->centro = $row["centro"];
						$obj->id_comprobante_pago = $row["id_comprobante_pago"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario = $row["usuario"];
						$obj->usuario_nombres = $row["usuario_nombres"];
						$obj->usuario_apellidos = $row["usuario_apellidos"]; 
						$obj->fecha_hora_registro = $row["fecha_hora_registro"];
						$obj->fecha = $row["fecha"];
						$obj->id_comprobante_pago_tipo = $row["id_comprobante_pago_tipo"];
						$obj->cod_comprobante_pago_tipo = $row["cod_comprobante_pago_tipo"];
						$obj->comprobante_pago_tipo = $row["comprobante_pago_tipo"];
						$obj->id_proveedor = $row["id_proveedor"];
						$obj->razon_social = $row["razon_social"];
						$obj->id_tipo_documento = $row["id_tipo_documento"];
						$obj->cod_proveedor_tipo_documento = $row["cod_proveedor_tipo_documento"]; 
						$obj->proveedor_tipo_documento = $row["proveedor_tipo_documento"];
						$obj->nro_comprobante = $row["nro_comprobante"];
						$obj->monto_neto_mn = $row["monto_neto_mn"];
						$obj->monto_impuesto_mn = $row["monto_impuesto_mn"];
						$obj->monto_percepcion_mn = $row["monto_percepcion_mn"];
						$obj->monto_total_mn = $row["monto_total_mn"];
						$obj->id_transaccion = $row["id_transaccion"];
						$obj->flag_anulada = $row["flag_anulada"];
	                    $obj->nombre_comercial = $row["nombre_comercial"];
						$obj->nro_documento = $row["nro_documento"];
						$obj->id_movimiento = $row["id_movimiento"];
						$obj->movimiento_key = $row["movimiento_key"];
						
	                    $lista[] = $obj;
                	}               
                
            }
            
			return $lista;
		}

		public function Registrar($obj)
		{
			$monto_percepcion_mn = "NULL";
			$id_movimiento = "NULL";
			
			if(!is_null($obj->monto_percepcion_mn))
				$monto_percepcion_mn = $obj->$monto_percepcion_mn;
			
			if(!is_null($obj->id_movimiento))
				$id_movimiento = $obj->id_movimiento;
			
			$query = "INSERT INTO compra (id_compra_tipo, compra_key, id_centro, id_usuario, fecha, id_comprobante_pago, flag_anulada, id_proveedor, id_transaccion,
				id_movimiento, fecha_hora_registro) 
			VALUES ( $obj->id_compra_tipo, '$obj->compra_key', $obj->id_centro, $obj->id_usuario, '$obj->fecha', $obj->id_comprobante_pago, $obj->flag_anulada, 
			$obj->id_proveedor, $obj->id_transaccion, $id_movimiento, '$obj->fecha_hora_registro')";
			
			//echo "Query: $query</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}
		
		public function Modificar($obj)
		{
			$id_movimiento = "NULL";
			if(!is_null($obj->id_movimiento))
				$id_movimiento = $obj->id_movimiento;
			
			$query = "UPDATE compra
				SET	id_comprobante_pago = $obj->id_comprobante_pago,
				flag_anulada = $obj->flag_anulada,
				id_proveedor = $obj->id_proveedor,
				id_transaccion = $obj->id_transaccion,
				id_movimiento = $id_movimiento
				WHERE id = $obj->id";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		}
		
		public function ListarItem($filtro)
		{
			if($filtro != "")
				$filtro = " WHERE $filtro";
			else
				$filtro = "";
			
			$query = "SELECT cd.id, cd.id_compra, co.compra_key, cd.id_producto id_producto, p.descripcion_corta, p.id_producto_categoria,
				p.producto_categoria, p.id_marca, p.marca, p.nro_serie, cd.precio_neto_unitario_mn, cd.impuesto_unitario_mn, 
				cd.impuesto_unitario_mn, cd.precio_total_unitario_mn, cd.flag_anulado, cd.cantidad
				FROM compra_detalle cd
				INNER JOIN compra co ON cd.id_compra = co.id
				INNER JOIN v_producto p ON cd.id_producto = p.id_producto $filtro";
				
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
	                    $obj = new CompraItem();
	                        
	                    $obj->id = $row["id"];
						$obj->id_compra = $row["id_compra"];
						$obj->compra_key = $row["compra_key"];
						$obj->id_producto = $row["id_producto"];
						$obj->descripcion_corta = $row["descripcion_corta"];
						$obj->id_producto_categoria = $row["id_producto_categoria"];
						$obj->producto_categoria = $row["producto_categoria"];
						$obj->id_marca = $row["id_marca"];				
						$obj->marca = $row["marca"];
						$obj->nro_serie = $row["nro_serie"];
						$obj->marca = $row["marca"];
						$obj->cantidad = $row["cantidad"];
						$obj->precio_neto_unitario_mn = $row["precio_neto_unitario_mn"];
						$obj->impuesto_unitario_mn = $row["impuesto_unitario_mn"];
						$obj->impuesto_unitario_mn = $row["impuesto_unitario_mn"];
						$obj->precio_total_unitario_mn = $row["precio_total_unitario_mn"];
						$obj->flag_anulado = $row["flag_anulado"];
	                    
	                    $lista[] = $obj;
	                }               
                
            }
            
			return $lista;
		}
		
		public function RegistrarItem($obj)
		{
			$query = "INSERT INTO compra_detalle (id_compra, id_producto, cantidad, precio_neto_unitario_mn, impuesto_unitario_mn, precio_total_unitario_mn, 
				flag_anulado) VALUES ($obj->id_compra, $obj->id_producto, $obj->cantidad, $obj->precio_neto_unitario_mn, $obj->impuesto_unitario_mn, 
				$obj->precio_total_unitario_mn, $obj->flag_anulado)";
				
			//echo "Item: $query</br>";
			
			try
            {
                //echo "Beginning...";
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
    
    
    class CompraBLO
    {
    	public function __construct()
		{
			
		}
  		
		private function Listar($filtro)
		{
			$objDA = new CompraDA();
			return $objDA->Listar($filtro);
		}
		
		private function ListarItem($filtro)
		{
			$objDA = new CompraDA();
			return $objDA->ListarItem($filtro);
		}
		
		private function ListarTipos($filtro)
		{
			$objDA = new CompraDA();
			return $objDA->ListarTipos($filtro);
		}
		
		public function ListarTodos($id_centro)
		{
			return $this->Listar("co.id_centro = $id_centro ORDER BY co.id DESC");
		}
		
		public function ListarTiposTodos()
		{
			return $this->ListarTipos("");
		}
		
		public function Registrar($obj)
		{
			$objDA = new CompraDA();
			$objDA->Registrar($obj);
			
			$obj = $this->RetornarXKey($obj->compra_key);
			
			$resultado = new OperacionResultado();
			if(!is_null($obj))
			{
				$resultado->id = $obj->id;
				$resultado->isOK = TRUE;
				$resultado->codigo = "02";
				$resultado->mensaje = "Compra Creada!";
			}
			else
			{
				$resultado->id = 0;
				$resultado->isOK = FALSE;
				$resultado->codigo = "03";
				$resultado->mensaje = "Compra NO Creada!";
			}
			
			return $resultado;
		}
		
		public function RetornarTipoXId($id)
		{
			$objDA = new CompraDA();
			$lista = $objDA->ListarTipos("ct.id = $id");
			
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			else
				return NULL;
			
		}
		
		public function RetornarXKey($key)
		{
			$objDA = new CompraDA();
			$lista = $objDA->Listar("co.compra_key = '$key'");
			
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
			$objDA = new CompraDA();
			$lista = $objDA->Listar("co.id = $id");
			
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			else
				return NULL;
			
		}
		
		public function RegistrarItem($obj)
		{
			$objDA = new CompraDA();
			$objDA->RegistrarItem($obj);
		}
      	
		public function ListarItemsXIdCompra($id_compra)
		{
			return $this->ListarItem("co.id = $id_compra");
		}
		
		public function Modificar($obj)
		{
			$objDA = new CompraDA();
			$objDA->Modificar($obj);
		}
    }
    
?>