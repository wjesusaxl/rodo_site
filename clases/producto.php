<?php
	
	class Pais
	{
		public $id;
		public $nombre;
	}
		
	class Marca
	{
		public $id;
		public $codigo;
		public $nombre;
	}
		
	class Producto
	{
	   	public $id;
	   	public $producto_categoria;
		public $id_producto_categoria;
		public $id_producto_categoria2;		
		public $id_producto_categoria3;
		public $id_producto_categoria4;		
		public $codigo;
		public $descripcion_corta;
		public $descripcion_larga;
		public $pais_origen;
		public $id_pais_origen;
		public $nro_serie;
		public $dimension;
		public $unidad_medida;
		public $id_unidad_medida;
		public $codigo_unidad_medida;
		public $opcion_cantidad;
		public $usuario;
		public $id_usuario;
		public $id_marca;
		public $marca;
		public $id_cantidad_default;
		public $flag_venta;
		public $flag_pack;
		public $tipo_objeto = "producto";
	   }
		
	class ProductoPrecio
	{
		public $id;
		public $id_producto;
		public $codigo;
		public $id_centro;
		public $centro;
		public $fecha_inicio;
		public $fecha_fin;
		public $precio_neto_mn;
		public $impuesto_mn;
		public $precio_total_mn;
		public $usuario;
		public $id_usuario;
		public $flag_habilitado;
		public $id_producto_categoria;
		public $producto_categoria;
		public $id_producto_precio_tipo;
		public $cod_producto_precio_tipo;
		public $producto_precio_tipo;
		public $descripcion_corta;
		public $id_marca;
		public $marca;
	}
    
   	class ProductoCategoria
    {
        public $id;
        public $descripcion;
        public $id_categoria_padre;
		public $tipo_objeto = "producto_categoria";
    }
	
	class ProductoPackItem
	{
		public $id;
		public $id_producto_pack; 
		public $pack_descripcion_corta; 
		public $pack_descripcion_larga; 
		public $pack_nro_serie; 
		public $pack_opcion_cantidad; 
		public $pack_marca; 
		public $pack_cantidad_default; 
		public $pack_flag_venta; 
		public $id_producto;
		public $producto_descripcion_corta; 
		public $producto_descripcion_larga; 
		public $producto_marca; 
		public $producto_nro_serie; 
		public $cantidad;
		public $flag_habilitado;
		public $tipo_objeto = "producto_pack_item";
	}
	
	class ProductoPrecioTipo
	{
		public $id;
		public $codigo;
        public $descripcion;
	}
	
	class UnidadMedida
	{
		public $id;
		public $codigo;
		public $descripcion;		
	}
	
	class ProductoDA
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
        
        public function ListarUnidadMedida($filtro)
        {
            if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
                        
            $query = "SELECT id, codigo, descripcion FROM unidad_medida $filtro";
            
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
                	
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
                        $obj = new UnidadMedida();
                        $obj->id = $row['id'];
                        $obj->codigo = $row['codigo'];
                        $obj->descripcion = $row['descripcion'];          
                        $lista[] = $obj;
                    }                    
                }                
            
            return $lista;
        }
        
		public function ListarPais($filtro)
        {
            if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
            
            $query = "SELECT id, nombre FROM pais $filtro";
                
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
                        $obj = new Pais();
                        $obj->id = $row['id'];
                        $obj->nombre = $row['nombre'];                            
                        $lista[] = $obj;
                    }                    
                }                
            

            return $lista;
        }
		

		public function RegistrarPrecio($obj)
		{			
			
			$query = "INSERT INTO producto_precio(
			id_producto_precio_tipo, id_centro, id_producto, fecha_inicio, fecha_fin, precio_neto_mn, impuesto_mn, precio_total_mn, id_usuario, flag_habilitado, codigo)
			VALUES ($obj->id_producto_precio_tipo, $obj->id_centro, $obj->id_producto, '$obj->fecha_inicio', '$obj->fecha_fin', $obj->precio_neto_mn, 
			$obj->impuesto_mn, $obj->precio_total_mn, $obj->id_usuario, $obj->flag_habilitado, '$obj->codigo');";
			
	
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
		
		public function Modificar($producto)
		{			
			$query = "UPDATE producto
				SET id_producto_categoria = $producto->id_producto_categoria,
				codigo = '$producto->codigo',
				descripcion_corta = '$producto->descripcion_corta',
				descripcion_larga = '$producto->descripcion_larga',
				id_pais_origen = $producto->id_pais_origen,
				nro_serie = '$producto->nro_serie',
				dimension = '$producto->dimension',
				id_unidad_medida = $producto->id_unidad_medida,
				opcion_cantidad = '$producto->opcion_cantidad',
				cantidad_default = $producto->id_cantidad_default,
				id_marca = $producto->id_marca,
				flag_venta = $producto->flag_venta,
				flag_pack = $producto->flag_pack
				WHERE id = $producto->id";
				
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
		
		public function ModificarPrecio($obj)
		{
			$query = "UPDATE producto_precio
				SET codigo = '$obj->codigo',
					fecha_inicio = '$obj->fecha_inicio',
				    fecha_fin = '$obj->fecha_fin',
				    precio_neto_mn = $obj->precio_neto_mn,
				    impuesto_mn = $obj->impuesto_mn,
				    precio_total_mn = $obj->precio_total_mn,
				    id_usuario = $obj->id_usuario,
				    id_producto_precio_tipo = $obj->id_producto_precio_tipo,
				    flag_habilitado = $obj->flag_habilitado
				WHERE id = $obj->id;";
				
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
		
		public function RegistrarUnidadMedida($obj)
        {
            $obj->codigo = mysql_escape_string($obj->codigo);
            $obj->descripcion = mysql_escape_string($obj->descripcion);
            
            $query = "INSERT INTO unidad_medida (codigo, descripcion) VALUES('$obj->codigo', '$obj->descripcion')";
            
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
        
        public function ModificarUnidadMedida($obj)
        {           
           $obj->codigo = mysql_escape_string($obj->codigo);
           $obj->descripcion = mysql_escape_string($obj->descripcion);
                      
           $query = "UPDATE unidad_medida
                SET codigo = '$obj->codigo',
                    descripcion = '$obj->descripcion'
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
		
		public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";		    
		
			$query = "SELECT id_producto, producto_categoria, codigo, descripcion_corta, descripcion_larga, pais, nro_serie, id_unidad_medida,
				dimension, unidad_medida, codigo_unidad_medida, opcion_cantidad, usuario, id_producto_categoria, IFNULL(id_producto_categoria2, 0) id_producto_categoria2,
				IFNULL(id_producto_categoria3, 0) id_producto_categoria3, IFNULL(id_producto_categoria4, 0) id_producto_categoria4, id_pais_origen, id_marca, marca,
				cantidad_default id_cantidad_default, id_usuario, flag_venta, flag_pack
				FROM v_producto $filtro";
				
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
                	
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
                        $obj = new Producto();
                        $obj->id = $row['id_producto'];
                        $obj->producto_categoria = strtoupper($row['producto_categoria']);
                        $obj->id_producto_categoria = $row['id_producto_categoria'];
                        $obj->id_producto_categoria2 = $row['id_producto_categoria2'];
                        $obj->id_producto_categoria3 = $row['id_producto_categoria3'];
                        $obj->id_producto_categoria4 = $row['id_producto_categoria4'];
                        $obj->codigo = strtoupper($row['codigo']);
                        $obj->descripcion_corta = strtoupper($row['descripcion_corta']);
                        $obj->descripcion_larga = strtoupper($row['descripcion_larga']);
                        $obj->pais_origen = strtoupper($row['pais']);
                        $obj->id_marca = $row['id_marca'];
                        $obj->marca = strtoupper($row['marca']);
                        $obj->id_pais_origen = $row['id_pais_origen'];
                        $obj->nro_serie = strtoupper($row['nro_serie']);
                        $obj->dimension = strtoupper($row['dimension']);
                        $obj->id_unidad_medida = $row['id_unidad_medida'];
                        $obj->unidad_medida = strtoupper($row['unidad_medida']);
                        $obj->codigo_unidad_medida = $row['codigo_unidad_medida'];
                        $obj->opcion_cantidad = $row['opcion_cantidad'];
                        $obj->id_usuario = strtoupper($row['id_usuario']);
                        $obj->usuario = strtoupper($row['usuario']);
                        $obj->id_cantidad_default = $row['id_cantidad_default'];
						$obj->flag_venta = $row["flag_venta"];
						$obj->flag_pack = $row["flag_pack"];
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}

		public function ListarProductoPackItem($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";		    
		
			$query = "SELECT PPA.id, PPA.id_producto_pack, PP.descripcion_corta pack_descripcion_corta, PP.descripcion_larga pack_descripcion_larga, 
				PP.nro_serie pack_nro_serie, PP.opcion_cantidad pack_opcion_cantidad, PP.marca pack_marca, PP.cantidad_default pack_cantidad_default,
				PP.flag_venta pack_flag_venta, PI.id_producto, PI.descripcion_corta producto_descripcion_corta, PI.descripcion_larga producto_descripcion_larga, 
				PI.marca producto_marca, PI.nro_serie producto_nro_serie, PPA.cantidad, PPA.flag_habilitado
				FROM producto_pack PPA
				INNER JOIN v_producto PP on PPA.id_producto_pack = PP.id_producto
				INNER JOIN v_producto PI ON PPA.id_producto = PI.id_producto $filtro";
				
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
                	
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
                        $obj = new ProductoPackItem();
                        $obj->id = $row['id']; 
						$obj->id_producto_pack = $row['id_producto_pack']; 
						$obj->pack_descripcion_corta = strtoupper($row['pack_descripcion_corta']); 
						$obj->pack_descripcion_larga = strtoupper($row['pack_descripcion_larga']); 
						$obj->pack_nro_serie = $row['pack_nro_serie']; 
						$obj->pack_opcion_cantidad = $row['pack_opcion_cantidad']; 
						$obj->pack_marca = strtoupper($row['pack_marca']); 
						$obj->pack_cantidad_default = $row['pack_cantidad_default']; 
						$obj->pack_flag_venta = $row['pack_flag_venta']; 
						$obj->id_producto = $row['id_producto'];
						$obj->producto_descripcion_corta = strtoupper($row['producto_descripcion_corta']); 
						$obj->producto_descripcion_larga = strtoupper($row['producto_descripcion_larga']); 
						$obj->producto_marca = strtoupper($row['producto_marca']); 
						$obj->producto_nro_serie = $row['producto_nro_serie']; 
						$obj->cantidad = $row['cantidad'];
						$obj->flag_habilitado = $row['flag_habilitado'];
                        $lista[] = $obj;
                    }
                    
                
			    
			}
			
			return $lista;
			
		}

		public function ListarCategoria($filtro)
		{
			if($filtro != "")
				$filtro = " WHERE $filtro";
			else
				$filtro = "";
			
			$query = "SELECT id, descripcion, id_categoria_padre FROM producto_categoria $filtro";
			
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
                        $obj = new ProductoCategoria();
                        
                        $obj->id = $row['id'];
                        $obj->descripcion = $row['descripcion'];
                        $obj->id_categoria_padre = $row['id_categoria_padre'];
						
                        $lista[] = $obj;
                    }
                    
                
            }           
            return $lista;
		}

		public function ListarCategoriasConProducto()
		{
			$query = "SELECT DISTINCT id_producto_categoria id, producto_categoria descripcion, id_producto_categoria2 id_categoria_padre FROM v_producto $filtro";
			
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
                        $obj = new ProductoCategoria();
                        
                        $obj->id = $row['id'];
                        $obj->descripcion = $row['descripcion'];
                        $obj->id_categoria_padre = $row['id_categoria_padre'];
						
                        $lista[] = $obj;
                    }
                    
                
            }           
            return $lista;
		}
		

		public function ListarPrecioTipo($filtro)
		{
			if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT id, codigo, descripcion FROM producto_precio_tipo $filtro";
				
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
                	
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
                        $obj = new ProductoPrecioTipo();
                        
                        $obj->id = $row['id'];
						$obj->codigo = $row["codigo"];
                        $obj->descripcion = $row['descripcion'];
						
                        $lista[] = $obj;
                    }
                    
                
            }           
            return $lista;
		}

        public function ListarMarca($filtro)
        {
            if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
            
            $query = "SELECT id, codigo, nombre FROM marca $filtro";
                
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
                        $obj = new Marca();
                        $obj->id = $row['id'];
                        $obj->codigo = $row['codigo'];
                        $obj->nombre = $row['nombre'];
                        $lista[] = $obj;
                    }
                    
                
            }           
            return $lista;
        }

        public function ListarPrecio($filtro)
        {
            if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";    
            
            $query = "SELECT pp.id, pp.id_centro, p.descripcion_corta, pp.id_producto, pp.fecha_inicio, pp.fecha_fin, pp.precio_neto_mn, pp.impuesto_mn, pp.codigo,
            pp.precio_total_mn, pp.id_usuario, pp.flag_habilitado, pp.id_producto_precio_tipo, ppt.codigo cod_producto_precio_tipo, ppt.descripcion producto_precio_tipo,
            p.producto_categoria, p.id_producto_categoria, p.id_marca, p.marca, c.descripcion centro, u.login usuario
            FROM producto_precio pp
            INNER JOIN v_producto p ON pp.id_producto = p.id_producto
            INNER JOIN producto_precio_tipo ppt ON pp.id_producto_precio_tipo = ppt.id 
            INNER JOIN centro c ON pp.id_centro = c.id 
            INNER JOIN usuario u ON pp.id_usuario = u.id $filtro";
			
			//echo "Precio: $query</br>";
			
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
                    	$obj = new ProductoPrecio();             
                        $obj->id = $row['id'];
                        $obj->id_centro = $row['id_centro'];
                        $obj->id_marca = $row['id_marca'];
                        $obj->marca = strtoupper($row['marca']);
                        $obj->codigo = strtoupper($row["codigo"]);
                        $obj->centro = strtoupper($row['centro']);
                        $obj->id_producto = $row['id_producto'];
                        $obj->descripcion_corta = strtoupper($row['descripcion_corta']);
                        $obj->id_producto_categoria = $row['id_producto_categoria'];
                        $obj->producto_categoria = strtoupper($row['producto_categoria']);
                        $obj->fecha_inicio = $row['fecha_inicio'];
                        $obj->fecha_fin = $row['fecha_fin'];
                        $obj->precio_neto_mn = $row['precio_neto_mn'];
                        $obj->impuesto_mn = $row['impuesto_mn'];
                        $obj->precio_total_mn = $row['precio_total_mn'];
                        $obj->id_usuario = $row['id_usuario'];
						$obj->usuario = $row['usuario'];
						$obj->flag_habilitado = $row['flag_habilitado'];
						$obj->id_producto_precio_tipo = $row['id_producto_precio_tipo'];
						$obj->cod_producto_precio_tipo = $row['cod_producto_precio_tipo'];
						$obj->producto_precio_tipo = $row['producto_precio_tipo'];
                        
                        $lista[] = $obj;
                    }
                              
            }
			
            return $lista;
        }

		public function Registrar($obj)
		{
			$query = "INSERT INTO producto (id_producto_categoria, codigo, descripcion_corta, descripcion_larga, id_pais_origen, nro_serie, dimension, id_unidad_medida, 
				opcion_cantidad, id_usuario, id_marca, cantidad_default, flag_venta, flag_pack)
			VALUES ( $obj->id_producto_categoria, '$obj->codigo', '$obj->descripcion_corta', '$obj->descripcion_larga', $obj->id_pais_origen, '$obj->nro_serie',
				$obj->dimension, $obj->id_unidad_medida, '$obj->opcion_cantidad', $obj->id_usuario, $obj->id_marca, $obj->id_cantidad_default, $obj->flag_venta,
				$obj->flag_pack )";
			
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
		
		public function RegistrarMarca($obj)
		{
			$query = "INSERT INTO marca(codigo, nombre) VALUES ('$obj->codigo', '$obj->nombre' )";
			
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
		public function RegistrarPackItem($obj)
		{
			$query = "INSERT INTO producto_pack(id_producto_pack, id_producto, cantidad, flag_habilitado) 
			VALUES ($obj->id_producto_pack, $obj->id_producto, $obj->cantidad, $obj->flag_habilitado )";
			
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
			//echo "Query: ".$query;
		}
		
		public function ModificarPackItem($obj)
		{
			$query = "UPDATE producto_pack
			SET cantidad = $obj->cantidad,
			flag_habilitado = $obj->flag_habilitado
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

        
			
    }

class ProductoBLO
{
	private function ListarUnidadMedida($filtro)
	{
		$objDA = new ProductoDA();
		return $objDA->ListarUnidadMedida($filtro);
	}
		
	public function ListarUnidadMedidaTodas()
	{
		return $this->ListarUnidadMedida("");
	}
        
	private function ListarPais($filtro)
	{
		$objDA = new ProductoDA();
		return $objDA->ListarPais($filtro);
	}
		
	public function ListarPaisTodos()
	{
		return $this->ListarPais("");
	}
  
	public function Listar($filtro)
	{
		$pDA = new ProductoDA();
		return $pDA->Listar($filtro);
	}
		
	private function ListarMarca($filtro)
	{
		$pDA = new ProductoDA();
		return $pDA->ListarMarca($filtro);
	}
		
	public function ListarMarcaTodas()
	{
		return $this->ListarMarca("id > 0 ORDER BY nombre");
	}
		
	public function RetornarProductoXId($id_producto)
	{
		$pDA = new ProductoDA();
		$prods = $pDA->Listar("id_producto = $id_producto");
		return $prods[0]; 
	}
        
	public function RetornarUnidadMedidaXId($id)
	{
		$pDA = new ProductoDA();
		$lista = $pDA->ListarUnidadMedida("id = $id");
		if(!is_null($lista))
			if(count($lista) == 1)
				return $lista[0];
			else
				return null;
            else
                return null; 
	}
		
	public function RetornarProductoXNroSerie($nro_serie)
	{
		$pDA = new ProductoDA();
		$prods = $pDA->Listar("nro_serie = '$nro_serie' AND LTRIM(RTRIM(nro_serie)) <> ''");
		return $prods[0]; 
	}
		
	public function CategoriaXIdCategoria($id_producto_categoria)
	{
		$pcDA = new ProductoDA();
		$filtro = "WHERE id_producto_categoria = $id_producto_categoria OR 
		id_producto_categoria2 = $id_producto_categoria OR 
		id_producto_categoria3 = $id_producto_categoria OR 
		id_producto_categoria4 = $id_producto_categoria";
		return $pcDA->Listar($filtro);
	}
		
	public function Modificar($producto)
	{
		$pDA = new ProductoDA();
		$pDA->Modificar($producto);	
	}
		
	public function ListarPreciosXIdProducto($id_producto, $id_centro)
	{
		$filtro = "p.id_producto = $id_producto AND pp.id_centro = $id_centro AND pp.flag_habilitado = 1 AND 
		pp.fecha_inicio <= now() AND pp.fecha_fin >= now()";
		
		return $this->ListarPrecio($filtro);
	}
	
	public function RetornarPrecioXIdProductoIdCentroIdPrecioTipo($id_producto, $id_centro, $id_producto_precio_tipo)
	{
		$filtro = "p.id_producto = $id_producto AND pp.id_centro = $id_centro AND pp.flag_habilitado = 1 AND
		pp.fecha_inicio <= now() AND pp.fecha_fin >= now() AND pp.id_producto_precio_tipo = $id_producto_precio_tipo";
				
		$lista = $this->ListarPrecio($filtro);
		
		if(!is_null($lista))
			if(count($lista))
				return $lista[0];
			else
				return NULL;
		else
			return NULL; 
	}
		
	private function ListarPrecioTipo($filtro)
	{
		$objDA = new ProductoDA();
		return $objDA->ListarPrecioTipo($filtro);
	}
		
	public function ListarPrecioTipoTodos()
	{
		return $this->ListarPrecioTipo("");
	}
		
	public function ListarPrecio($filtro)
	{
		$objDA = new ProductoDA();
		return $objDA->ListarPrecio($filtro);
	}
		
	public function RetornarPrecioXId($id_producto_precio)
	{
		$filtro = "pp.id = $id_producto_precio";
		$lista = $this->ListarPrecio($filtro);
			
		if(!is_null($lista))
		{
			if(count($lista))
				return $lista[0];
			else 
				return NULL;
		}
		else 
			return NULL;
			
			
			//return $objs[0];
	}
		
	public function ModificarPrecio($obj)
	{
			//echo "HABLAAAAAAAAA!";
		$pDA = new ProductoDA();
		$pDA->ModificarPrecio($obj);
	}
		
	public function RegistrarPrecio($obj)
	{
	   $objDA = new ProductoDA();
	   $objDA->RegistrarPrecio($obj);
	}
        
	public function RegistrarUnidadMedida($obj)
	{
		$pDA = new ProductoDA();
		$pDA->RegistrarUnidadMedida($obj);  
	}
        
	public function ModificarUnidadMedida($obj)
	{
		$pDA = new ProductoDA();
		$pDA->ModificarUnidadMedida($obj);  
	}
		
	private function ListarCategoria($filtro)
	{
		$pcDA = new ProductoDA();
		return $pcDA->ListarCategoria($filtro);			
	}
		
	public function ListarCategoriaTodas()
	{
		return $this->ListarCategoria("");
	}
		
	public function ListarCategoriaXCategoriaPadre($id_categoria_padre)
	{
		return $this->ListarCategoria("IFNULL(id_categoria_padre, 0) = $id_categoria_padre");
	}
		
	public function ListarCategoriasPrincipales()
	{
		return $this->ListarCategoria("IFNULL(id_categoria_padre, 0) = 0");
	}
		
	public function ListarCategoriasConProductos()
	{
		$objDA = new ProductoDA();
		return $objDA->ListarCategoriasConProducto();
	}
		
	public function Registrar($obj)
	{
		$objDA = new ProductoDA();
		$objDA->Registrar($obj);
	}
		
	public function RegistrarMarca($obj)
	{
		$objDA = new ProductoDA();
		$objDA->RegistrarMarca($obj);
	}
	
	public function ListarProductoPackItem($filtro)
	{
		$objDA = new ProductoDA();
		return $objDA->ListarProductoPackItem($filtro);
			
	}
	
	public function RegistrarPackItem($obj)
	{
		$objDA = new ProductoDA();
		$objDA->RegistrarPackItem($obj);
	}
	
	public function ModificarPackItem($obj)
	{
		$objDA = new ProductoDA();
		$objDA->ModificarPackItem($obj);
	}
}
    

	

	
?>