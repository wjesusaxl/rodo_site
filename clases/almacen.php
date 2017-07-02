<?php
    
    class Almacen
    {
        public $id;
        public $codigo;
        public $id_centro;
		public $descripcion;
        public $flag_habilitado;
		public $flag_venta;
		public $flag_principal;
		public $cod_centro;
		public $centro;
    }
	
	class AlmacenUsuario
	{
		public $id;
		public $id_almacen;
		public $cod_almacen;
		public $almacen;
		public $id_centro;
		public $id_usuario;
		public $flag_habilitado;
		public $flag_entrada;
		public $flag_salida;	
		public $almacen_habilitado;
		public $usuario_habilitado;
		public $cod_centro;
		public $centro;
		
	}
    
    class AlmacenDA
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
		
		public function ListarAlmacenUsuario($filtro)
		{
			if($filtro != "")
				$filtro = " WHERE $filtro";
			else
				$filtro = "";
			
			$query = "SELECT au.id, au.id_almacen, au.id_usuario, au.flag_habilitado, a.flag_habilitado almacen_habilitado,
				u.flag_habilitado usuario_habilitado, a.descripcion, a.id_centro, a.codigo cod_almacen, a.descripcion almacen,
				c.codigo cod_centro, c.descripcion centro, au.flag_entrada, au.flag_salida
				FROM almacen_usuario au
				INNER JOIN almacen a ON au.id_almacen = a.id
				INNER JOIN usuario u ON au.id_usuario = u.id
				INNER JOIN centro c ON a.id_centro = c.id $filtro";
				
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
                {
                    $obj = new AlmacenUsuario();
	                $obj->id = $row["id"];
					$obj->id_almacen = $row["id_almacen"];
					$obj->id_usuario = $row["id_usuario"];
					$obj->flag_habilitado = $row["flag_habilitado"];
					$obj->flag_entrada = $row["flag_entrada"];
					$obj->flag_salida = $row["flag_salida"];
					$obj->almacen_habilitado = $row["almacen_habilitado"];
					$obj->usuario_habilitado = $row["usuario_habilitado"];
					$obj->id_centro  = $row["id_centro"];
					$obj->cod_almacen  = $row["cod_almacen"];
					$obj->almacen  = strtoupper($row["almacen"]);
					$obj->cod_centro  = $row["cod_centro"];
					$obj->centro  = strtoupper($row["centro"]);
						
                    $lista[] = $obj;
                }               
                
            }
            
			return $lista;
		}
		
    	public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT a.id, a.codigo, a.id_centro, a.descripcion, a.flag_habilitado, a.flag_venta, c.codigo cod_centro, c.descripcion centro, a.flag_principal
			 	FROM almacen a
				INNER JOIN centro c ON a.id_centro = c.id $filtro";
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
                        $obj = new Almacen();
                        $obj->id = $row["id"];
				        $obj->codigo = $row["codigo"];
				        $obj->id_centro = $row["id_centro"];
				        $obj->descripcion = strtoupper($row["descripcion"]);
				        $obj->flag_habilitado = $row["flag_habilitado"];
						$obj->flag_venta = $row["flag_venta"];
						$obj->cod_centro = $row["cod_centro"];
						$obj->centro = strtoupper($row["centro"]);
						$obj->flag_principal = $row["flag_principal"];
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}
		
		public function RegistrarAlmacenUsuario($obj)
		{
			$query = "INSERT INTO almacen_usuario (id_almacen, id_usuario, flag_habilitado, flag_entrada, flag_salida ) VALUES ( $obj->id_almacen, $obj->id_usuario, 
			$obj->flag_habilitado, $obj->flag_entrada, $obj->flag_salida)";
			
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
		
		public function ModificarAlmacenUsuario($obj)
		{
			$query = "UPDATE almacen_usuario
				SET flag_habilitado = $obj->flag_habilitado, 
				flag_entrada = $obj->flag_entrada,
				flag_salida = $obj->flag_salida 
				WHERE id = $obj->id";
				
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
    }

	class AlmacenBLO
	{
	    private $objDA;
		
        
		private function Listar($filtro)
		{
			$objDA = new AlmacenDA();
						
			return $objDA->Listar($filtro);
		}
		
		public function ListarHabilitados()
		{
			return $this->Listar("flag_habilitado = 1");
		}
		
		private function ListarAlmacenUsuario($filtro)
		{
			$objDA = new AlmacenDA();
			
			return $objDA->ListarAlmacenUsuario($filtro);
		}
        
        public function RetornarXId($id)
        {
        	$objDA = new AlmacenDA();
            $lista = $objDA->Listar("a.id = $id");
            
            if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else 
					return $lista[0];
			else 
				return $lista[0];
        }
		
		public function RetornarPrincipalXIdCentro($id_centro)
		{
			$filtro = "a.id_centro = $id_centro AND a.flag_principal = 1 AND a.flag_habilitado = 1";
			$lista = $this->Listar($filtro);
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else 
					return $lista[0];
			else 
				return $lista[0];
		}
        
        public function RetornarXCodigo($codigo)
        {
        	$objDA = new AlmacenDA();
            return $objDA->Listar("codigo = '$codigo'");
        }
		
		public function ListarAlmacenXIdUsuarioIdCentroHabilitado($id_usuario, $id_centro)
		{
			
			$filtro = "au.id_usuario = $id_usuario AND au.flag_habilitado = 1 AND a.flag_habilitado = 1 AND u.flag_habilitado = 1 AND a.id_centro = $id_centro" ;
			return $this->ListarAlmacenUsuario($filtro);				
		}
		
		public function ListarAlmacenXIdUsuarioIdCentro($id_usuario, $id_centro)
		{
			
			$filtro = "au.id_usuario = $id_usuario AND a.id_centro = $id_centro" ;
			return $this->ListarAlmacenUsuario($filtro);				
		}
		
		
		public function ListarAlmacenXIdUsuarioIdCentro_Venta($id_usuario, $id_centro)
		{
			
			$filtro = "au.id_usuario = $id_usuario AND au.flag_habilitado = 1 AND a.flag_habilitado = 1 AND u.flag_habilitado = 1 AND a.id_centro = $id_centro AND
				a.flag_venta = 1" ;
			$lista = $this->ListarAlmacenUsuario($filtro);
			
			return $lista;
		}
		
		
		public function ListarAlmacenXIdCentro($id_centro)
		{
			$filtro = "id_centro = $id_centro";
			return $this->Listar($filtro);
		}
		
		public function RetornarAlmacenUsuarioXIdUsuarioIdAlmacen($id_usuario, $id_almacen)
		{
			$filtro = "au.id_usuario = $id_usuario AND au.id_almacen = $id_almacen";
			$lista = $this->ListarAlmacenUsuario($filtro);
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
		
		public function RegistrarAlmacenUsuario($obj)
		{
			$objDA = new AlmacenDA();
			$objDA->RegistrarAlmacenUsuario($obj);
		}
		
		public function ModificarAlmacenUsuario($obj)
		{
			$objDA = new AlmacenDA();
			$objDA->ModificarAlmacenUsuario($obj);
		}
		
		public function ListarAlmacenEntradaXIdUsuario($id_usuario, $id_centro)
		{
			$filtro = "au.id_usuario = $id_usuario AND a.id_centro = $id_centro AND au.flag_entrada = 1 AND au.flag_habilitado = 1 AND u.flag_habilitado = 1 AND
				a.flag_habilitado = 1";
			return $this->ListarAlmacenUsuario($filtro);
		}
		
		public function ListarAlmacenSalidaXIdUsuario($id_usuario, $id_centro)
		{
			$filtro = "au.id_usuario = $id_usuario AND a.id_centro = $id_centro AND au.flag_salida = 1 AND au.flag_habilitado = 1 AND u.flag_habilitado = 1 AND
				a.flag_habilitado =1";
			return $this->ListarAlmacenUsuario($filtro);
		}
		
		public function ListarAlmacenXIdUsuario($id_usuario, $id_centro)
		{
			$filtro = "au.id_usuario = $id_usuario AND a.id_centro = $id_centro AND (au.flag_salida = 1 OR au.flag_entrada = 1 OR au.flag_habilitado = 1 ) AND u.flag_habilitado = 1 AND
				a.flag_habilitado =1";
			return $this->ListarAlmacenUsuario($filtro);
		}
	}
?>