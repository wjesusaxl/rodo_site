<?php

    class Movimiento
    {
		public $id;
		public $id_motivo;
		public $movimiento_key;
		public $usuario;
		public $cod_motivo;
		public $motivo; 
		public $id_almacen_origen;
		public $id_almacen_destino;
		public $cod_almacen_origen;
		public $almacen_origen; 
		public $cod_almacen_destino;
		public $fecha_hora;
		public $almacen_destino;
		public $comentarios;
		public $id_usuario;
		public $usuario_nombres;
		public $usuario_apellidos;
		public $id_centro;
		public $cod_centro;
		public $centro;
    }
    
	class MovimientoMotivo
	{
		public $id;
		public $codigo;
		public $descripcion;
		public $factor;
	}
    
	class MovimientoMotivoUsuario
	{
		public $id;
		public $id_centro;
		public $id_movimiento_motivo;
		public $cod_movimiento_motivo;
		public $movimiento_motivo;
		public $factor;
		public $usuario;
		public $id_usuario;
		public $flag_habilitado;
	}
	
	class MovimientoItem
	{
		public $id;
		public $id_movimiento;
		public $id_producto;
		public $movimiento_key;
		public $descripcion_corta;
		public $producto_categoria;
		public $marca;
		public $cantidad;
		public $usuario;
		public $cod_motivo;
		public $motivo;
		public $id_almacen_origen;
		public $id_almacen_destino;
		public $cod_almacen_origen;
		public $almacen_origen;
		public $cod_almacen_destino;
		public $almacen_destino;
		public $auto_key;
		public $flag_anulado;
	}	

	
	class MovimientoDA
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
                $filtro = "WHERE $filtro";
            
            $query = "SELECT mo.id, mo.id_motivo, mo.auto_key movimiento_key, u.login usuario, mm.codigo cod_motivo, mm.descripcion motivo, 
				mo.id_almacen_origen, mo.id_almacen_destino, ao.codigo cod_almacen_origen, ao.descripcion almacen_origen, 
				ad.codigo cod_almacen_destino, mo.fecha_hora, ad.descripcion almacen_destino, mo.comentarios, mo.id_usuario, 
				mo.id_centro, u.nombres usuario_nombres, u.apellidos usuario_apellidos, c.codigo cod_centro, c.descripcion centro
				FROM movimiento mo
				INNER JOIN usuario u ON mo.id_usuario = u.id
				INNER JOIN movimiento_motivo mm ON mo.id_motivo = mm.id
				INNER JOIN centro c ON mo.id_centro = c.id
				LEFT JOIN almacen ao ON mo.id_almacen_origen = ao.id
				LEFT JOIN almacen ad ON mo.id_almacen_destino = ad.id $filtro";
				
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
	                    $obj = new Movimiento();
	                        
	                    $obj->id = $row["id"];
						$obj->id_motivo = $row["id_motivo"];
						$obj->movimiento_key = $row["movimiento_key"];
						$obj->usuario = $row["usuario"];
						$obj->cod_motivo = $row["cod_motivo"];
						$obj->motivo = $row["motivo"];
						$obj->id_almacen_origen = $row["id_almacen_origen"];
						$obj->id_almacen_destino = $row["id_almacen_destino"];
						$obj->cod_almacen_origen = $row["cod_almacen_origen"];
						$obj->almacen_origen = $row["almacen_origen"];
						$obj->cod_almacen_destino = $row["cod_almacen_destino"];
						$obj->fecha_hora = $row["fecha_hora"];
						$obj->almacen_destino = $row["almacen_destino"];
						$obj->comentarios = $row["comentarios"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario_nombres = $row["usuario_nombres"];
						$obj->usuario_apellidos = $row["usuario_apellidos"];
						$obj->id_centro = $row["id_centro"];
						$obj->cod_centro = $row["cod_centro"];
						$obj->centro = $row["centro"];
	
	                    
	                    $lista[] = $obj;
	                }               
                
            }
            
			return $lista;
		}

       	public function ListarMotivo($filtro)
		{
			
			if($filtro != "")
				$filtro = "WHERE $filtro";
			
			$query = "SELECT mm.id, mm.codigo, mm.descripcion, mm.factor 
				FROM movimiento_motivo mm $filtro";
				
			//echo "Query: $query";
			
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
						$obj = new MovimientoMotivo();
						
						$obj->id = $row['id'];
						$obj->codigo = $row['codigo'];
						$obj->descripcion = $row['descripcion'];
						$obj->factor = $row['factor'];
						
						$lista[] = $obj;
						
					}               
                
            }
            
			return $lista;
		}
		
		public function ListarMotivoUsuario($filtro)
		{
			
			if($filtro != "")
				$filtro = "WHERE $filtro";
			
			$query = "SELECT mmu.id, mmu.id_centro, mmu.id_movimiento_motivo, mm.codigo cod_movimiento_motivo, mm.descripcion movimiento_motivo, mm.factor, 
				u.login usuario, mmu.id_usuario, mmu.flag_habilitado
				FROM movimiento_motivo_usuario mmu
				INNER JOIN movimiento_motivo mm ON mmu.id_movimiento_motivo = mm.id
				INNER JOIN usuario u ON mmu.id_usuario = u.id $filtro LIMIT 100";
				
			//echo "Query: $query";
			
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
						$obj = new MovimientoMotivoUsuario();
						
						$obj->id = $row["id"];
						$obj->id_centro = $row["id_centro"];
						$obj->id_movimiento_motivo = $row["id_movimiento_motivo"];
						$obj->cod_movimiento_motivo = $row["cod_movimiento_motivo"];
						$obj->movimiento_motivo = $row["movimiento_motivo"];
						$obj->factor = $row["factor"];
						$obj->usuario = $row["usuario"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->flag_habilitado = $row["flag_habilitado"];
						
						$lista[] = $obj;
						
					 }               
                
            }
            
			return $lista;
		}
		
		public function ListarItem($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT md.id, md.id_movimiento, md.id_producto, mo.auto_key movimiento_key, p.descripcion_corta, p.producto_categoria,
            md.cantidad, u.login usuario, mm.codigo cod_motivo, mm.descripcion motivo, mo.id_almacen_origen, mo.id_almacen_destino,
			ao.codigo cod_almacen_origen, ao.descripcion almacen_origen, ad.codigo cod_almacen_destino, ad.descripcion almacen_destino, p.marca,
			md.auto_key, md.flag_anulado
            FROM movimiento mo
            INNER JOIN movimiento_detalle md ON mo.id = md.id_movimiento
			INNER JOIN movimiento_motivo mm ON mo.id_motivo = mm.id
            INNER JOIN v_producto p ON md.id_producto = p.id_producto
            INNER JOIN usuario u ON mo.id_usuario = u.id
            LEFT JOIN almacen ao ON mo.id_almacen_origen = ao.id
            LEFT JOIN almacen ad ON mo.id_almacen_destino = ad.id $filtro";
            
            //echo $query;
            
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
	                    $obj = new MovimientoItem();
	                        
	                    $obj->id = $row["id"];
						$obj->id_movimiento = $row["id_movimiento"];
						$obj->id_producto = $row["id_producto"];
						$obj->movimiento_key = $row["movimiento_key"];
						$obj->id_producto = $row["id_producto"];
						$obj->descripcion_corta = $row["descripcion_corta"];
						$obj->producto_categoria = $row["producto_categoria"];
						$obj->marca = $row["marca"];
						$obj->cantidad = $row["cantidad"];
						$obj->usuario = $row["usuario"];
						$obj->cod_motivo = $row["cod_motivo"];
						$obj->motivo = $row["motivo"];
						$obj->id_almacen_origen = $row["id_almacen_origen"];
						$obj->id_almacen_destino = $row["id_almacen_destino"];
						$obj->cod_almacen_origen = $row["cod_almacen_origen"];
						$obj->almacen_origen = $row["almacen_origen"];
						$obj->cod_almacen_destino = $row["cod_almacen_destino"];
						$obj->almacen_destino = $row["almacen_destino"];
						$obj->auto_key = $row["auto_key"];
						$obj->flag_anulado = $row["flag_anulado"];
	                    
	                    $lista[] = $obj;
	                }               
                
            }
            
			return $lista;
		}
		

        public function Registrar($obj)
        {
            
            if(!is_null($obj->id_almacen_origen))
                $id_almacen_origen = $obj->id_almacen_origen;
            else 
                $id_almacen_origen = "NULL";
            
            if(!is_null($obj->id_almacen_destino))
                $id_almacen_destino = $obj->id_almacen_destino;
            else 
                $id_almacen_destino = "NULL";
			
			$query = "INSERT INTO movimiento (id_centro, auto_key, fecha_hora, id_usuario, id_motivo, id_almacen_origen, id_almacen_destino, 
	           comentarios) 
	           VALUES ($obj->id_centro, '$obj->movimiento_key', '$obj->fecha_hora', $obj->id_usuario, $obj->id_motivo, $id_almacen_origen, $id_almacen_destino, 
	           '$obj->comentarios')";
			               
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
            $obj->comentarios = mysql_escape_string($obj->comentarios);
            
            $query = "UPDATE movimiento
            SET id_motivo = $obj->id_motivo,
            comentarios = '$obj->comentarios'
            WHERE id = $obj->id ";
                                    
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
        
        public function GenerarMovimientoCompra($id_compra)
		{
			$query = "INSERT INTO movimiento_detalle (id_movimiento, id_producto, cantidad, flag_anulado, auto_key)
				SELECT c.id_movimiento, cd.id_producto, cd.cantidad, 0,
				UPPER(CAST(SUBSTRING(MD5(RAND()) FROM 1 FOR 8) AS CHAR(8) CHARACTER SET utf8)) 
				FROM compra_detalle cd
					INNER JOIN compra c ON cd.id_compra = c.id
				WHERE id_compra = $id_compra AND flag_anulado = 0";
				
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
		
		public function RegistrarMotivoUsuario($obj)
		{
			$query = "INSERT INTO movimiento_motivo_usuario (id_centro, id_movimiento_motivo, id_usuario, flag_habilitado) 
				VALUES($obj->id_centro, $obj->id_movimiento_motivo, $obj->id_usuario, $obj->flag_habilitado)";
			
			//echo "Registrando: $query</br>";
			
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
		
		public function RegistrarItem($obj)
		{
			$id_movimiento_detalle = "NULL";
			
			$query = "INSERT INTO movimiento_detalle(id_movimiento, id_producto, cantidad, auto_key, flag_anulado) 
			VALUES ($obj->id_movimiento, $obj->id_producto, $obj->cantidad, '$obj->auto_key', $obj->flag_anulado)";
			
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
		
		public function ModificarItem($obj)
		{
			$query = "UPDATE movimiento_detalle
			SET cantidad = $obj->cantidad,
			flag_anulado = $obj->flag_anulado
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
		
		public function ModificarMotivoUsuario($obj)
		{
			$query = "UPDATE movimiento_motivo_usuario
				SET flag_habilitado = $obj->flag_habilitado
				WHERE id = $obj->id";
			
			//echo "Registrando: $query</br>";
			
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
    
    
    class MovimientoBLO
    {
        
        private function Listar($filtro)
        {
            $objDA = new MovimientoDA();
            return $objDA->Listar($filtro);
        }
        
        public function RetornarXId($ud)
        {
            $lista = $this->Listar("mo.id = $id");
            if($lista != NULL)
                return $lista[0];
            else 
                return NULL;
        }
        
        public function RetornarXKey($key)
        {
            $lista = $this->Listar("mo.auto_key = '$key'");
            if($lista != NULL)
			{
				if(count($lista) > 0)
                	return $lista[0];
				else 
					return NULL;
			}
            else 
                return NULL;
        }
        
      
        
		public function Registrar($obj)
        {
            $resultado = new OperacionResultado();
            
            $objDA = new MovimientoDA($this->id_centro);
            $objDA->Registrar($obj);
            
            $mov = $this->RetornarXKey($obj->movimiento_key);
            if($mov != NULL)
            {
                $resultado->id = $mov->id;
                $resultado->codigo = "02";
                $resultado->mensaje = "Movimiento Creado exitosamente!";
                $resultado->isOK = TRUE;
            }
            else
            {
                $resultado->id = 0;
                $resultado->codigo = "03";
                $resultado->mensaje = "Movimiento no se ha creado. Favor revisar!";
                $resultado->isOK = FALSE;
            }
            return $resultado;
        }
        
        private function ListarMovimiento($filtro)
		{
			
			$objDA = new MovimientoDA();			
			$lista = $objDA->ListarMotivo($filtro);
						
			return $lista;			
		}

		public function RetornarMovimientoMotivoXId($id)
		{
			if($id == 0)
				$filtro = "";
			else
				$filtro = "mm.id = $id";
			$lista = $this->ListarMovimiento($filtro);
			return $lista;
		}
		

		
		public function RetornarItemXKey($auto_key)
		{
			$lista = $this->ListarItem("md.auto_key = '$auto_key'");
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else 
					return NULL;
			}
			else
				return NULL;
		}
		
		public function RetornarItemXId($id)
		{
			$lista = $this->ListarItem("md.id = $id");
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else 
					return NULL;
			}
			else
				return NULL;
		}
		
		public function RetornarMovimientoMotivoXCodigo($codigo)
		{
			if($codigo != "")
				$filtro = "mm.codigo = '$codigo'";
			
			$lista = $this->Listar($filtro);
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else 
					return NULL;
			}
			else
				return NULL;
		}
		
		private function ListarMotivo($filtro)
		{
			$objDA = new MovimientoDA();
			return $objDA->ListarMotivo($filtro);
		}
		
		public function ListarMotivoTodos()
		{
			return $this->ListarMotivo("");
		}
        
		public function GenerarMovimientoCompra($id_compra)
		{
			$objDA = new MovimientoDA();
			$objDA->GenerarMovimientoCompra($id_compra);
		}
		
		private function ListarMotivoUsuario($filtro)
		{
			$objDA = new MovimientoDA();
			return $objDA->ListarMotivoUsuario($filtro);
		}
		
		public function ListarMotivoHabilitadoXIdUsuario($id_usuario, $id_centro)
		{
			return $this->ListarMotivoUsuario("mmu.id_usuario = $id_usuario AND mmu.flag_habilitado = 1 AND u.flag_habilitado = 1 AND mmu.id_centro = $id_centro");
		}
		
		public function ListarMotivoXIdUsuario($id_usuario, $id_centro)
		{
			return $this->ListarMotivoUsuario("mmu.id_usuario = $id_usuario AND mmu.id_centro = $id_centro");
		}
		
		public function RetornarMotivoUsuarioXIdUsuarioIdMotivo($id_usuario, $id_movimiento_motivo, $id_centro)
		{
			$lista = $this->ListarMotivoUsuario("mmu.id_usuario = $id_usuario AND mmu.id_centro = $id_centro AND mmu.id_movimiento_motivo = $id_movimiento_motivo");
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else 
					return NULL;
			}
			else
				return NULL;
		}
		
		public function ModificarMotivoUsuario($obj)
		{
			$objDA = new MovimientoDA();
			$objDA->ModificarMotivoUsuario($obj);
		}
		
		public function RegistrarMotivoUsuario($obj)
		{
			$objDA = new MovimientoDA();
			$objDA->RegistrarMotivoUsuario($obj);
		}
		
		public function RegistrarItem($obj)
		{
			$objDA = new MovimientoDA();
			$objDA->RegistrarItem($obj);
		}
		
		public function ListarXIdCentro($id_centro)
		{
			return $this->Listar("mo.id_centro = $id_centro ORDER BY mo.id DESC");
		}
		
		private function ListarItem($filtro)
		{
			$objDA = new MovimientoDA();
			return $objDA->ListarItem($filtro);
		}
		
		public function ListarItemsXIdMovimiento($id_movimiento)
		{
			return $this->ListarItem("mo.id = $id_movimiento");
			
		}
		
		public function ModificarItem($obj)
		{
			$objDA = new MovimientoDA();
			$objDA->ModificarItem($obj);
		}
    }
    
?>