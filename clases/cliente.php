<?php
    
    class TipoDocumento
    {
    	public $id;
		public $codigo;
		public $descripcion;
    }
	
	class Cliente
    {
    	public $id;
		public $id_tipo_documento;
		public $tipo_documento;
		public $nro_documento;
		public $nombres;
		public $apellidos;
		public $keyword;
		public $id_usuario_creacion;
		public $usuario_creacion;
		public $telefonos;
		public $email;
		public $telefonos_str;
    }
	
	class ClienteTelefono
	{
		public $id_cliente;
		public $telefono;
		public $habilitado;
	}
	
	class TipoDocumentoDA
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
                echo 'ERROR: $connection_string' . $e->getMessage();
            }
		}
		
    	public function Listar($filtro)
		{
			
			$query = "SELECT id, codigo, descripcion FROM tipo_documento ".$filtro;
			//echo "Query: ".$query;
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
                	$tipos = array();
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
						$tipo= new TipoDocumento();
						$tipo->id = $row['id'];
						$tipo->codigo = $row['codigo'];
						$tipo->descripcion = $row['descripcion'];									
						$tipos[] = $tipo;
					 }               
                
            }
            
			return $tipos;
		}	
    }
    
    
    
    class ClienteDA
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
			$query = "SELECT c.id, c.id_tipo_documento, c.nro_documento, c.nombres, c.apellidos, c.keyword,
				u.login usuario_creacion, td.codigo codigo_tipo_documento, td.descripcion tipo_documento, c.email
				FROM cliente c
				INNER JOIN tipo_documento td ON c.id_tipo_documento = td.id
				INNER JOIN usuario u ON c.id_usuario_creacion = u.id $filtro ORDER BY c.id";
			
			//echo "Query: $query</br>";



			try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }

            $clientes = NULL;

            if($result->rowCount()>0)
            {

                $clientes = array();
                	foreach($result as $row)
                    {
						$cliente = new Cliente();
						$cliente->id = $row['id'];
						$cliente->apellidos = $row['apellidos'];
						$cliente->id_tipo_documento = $row['id_tipo_documento'];
						$cliente->id_usuario_creacion = $row['id_usuario_creacion'];
						$cliente->keyword = $row['keyword'];
						$cliente->nombres = $row['nombres'];
						$cliente->nro_documento = $row['nro_documento'];
						$cliente->tipo_documento = $row['tipo_documento'];
						$cliente->usuario_creacion = $row['usuario_creacion'];
						$cliente->email = $row['email'];
						
						$tels = "";



						$query = "SELECT id_cliente, telefono, habilitado FROM cliente_telefono WHERE id_cliente = $cliente->id";						
						try
                        {

                            $result2 = $this->conn->query($query);    
                        }catch(PDOException $e)
                        {
                            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
                        }

                        $telefonos = NULL;

                        if($result2->rowCount()>0)
                        {



			                $telefonos = array();
							
			                if(count($result2) > 0)
			                {                	
								foreach($result2 as $row2)
			                    {
									$telefono = new ClienteTelefono();
									$telefono->id_cliente = $row2['id_cliente'];
									$telefono->telefono = $row2['telefono'];
									$telefono->habilitado = $row2['habilitado'];
									$telefonos[] = $telefono;
									
									$tels = $tels . " - ".$row2['telefono'];														
								}
								$cliente->telefonos = $telefonos;						
								$tels = substr($tels, 3);
								$cliente->telefonos_str = $tels;
							}
							


                            //echo "Cliente: ".json_encode($cliente)."<br>";
						}

                        $clientes[] = $cliente;
					}
				

			}

            //echo "Cliente: ".json_encode($clientes)."<br>";

			return $clientes;
			
		}	

		public function Registrar2($cliente)
		{
			$resultado = new OperacionResultado();
			$resultado->id = 0;
			$resultado->mensaje = "Prueba";
			$resultado->codigo = "01";
			$resultado->isOK = false;
			return $resultado;
		}

		public function Registrar($cliente)
		{
			$resultado = new OperacionResultado();
			
			$query = "INSERT INTO cliente (id_tipo_documento, nro_documento, nombres, apellidos, keyword, id_usuario_creacion, email) 
			VALUES($cliente->id_tipo_documento, '$cliente->nro_documento', '$cliente->nombres', '$cliente->apellidos', '$cliente->keyword', $cliente->id_usuario_creacion,
			'$cliente->email')";
            
            try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
                $resultado->id = 0;
                $resultado->isOK = true;
                $resultado->codigo = 1;
                $resultado->mensaje = "Cliente Registrado Exitosamente!";
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            return $resultado;
		}
		
		public function RegistrarTelefono($obj)
		{
			$query = "INSERT INTO cliente_telefono (id_cliente, telefono, habilitado) VALUES ($obj->id_cliente, '$obj->telefono', $obj->habilitado)";
			
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
		
		public function BorrarTelefonos($id_cliente)
		{
			$query = "DELETE FROM cliente_telefono WHERE id_cliente = $id_cliente AND id > 0";
					
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

		public function Actualizar($cliente_nuevo)
		{
			$update = "";
			if($cliente->nombres != $cliente_nuevo->nombres)
				$update = $update.", nombres = '$cliente_nuevo->nombres'";
			if($cliente->apellidos != $cliente_nuevo->apellidos)
				$update = $update.", apellidos = '$cliente_nuevo->apellidos'";
			if($cliente->id_tipo_documento != $cliente_nuevo->id_tipo_documento)
				$update = $update.", id_tipo_documento = $cliente_nuevo->id_tipo_documento";
			if($cliente->nro_documento != $cliente_nuevo->nro_documento)
				$update = $update.", nro_documento = '$cliente_nuevo->nro_documento'";
			if($cliente->keyword != $cliente_nuevo->keyword)
				$update = $update.", keyword = '$cliente_nuevo->keyword'";
			if($cliente->email != $cliente_nuevo->email)
				$update = $update.", email = '$cliente_nuevo->email'";
					
			if($update != "")
			{
				$update = substr($update, 2);
				$query = "UPDATE cliente SET ".$update. " WHERE id = $cliente->id";
                
                
                try
                {
                    //echo "Beginning...";
                    $result = $this->conn->prepare($query);
                    $result->execute();
                    
                    $last_inserted_id = $this->conn->lastInsertId();
                    $affected_rows = $result->rowCount();
                    
                    $query = "DELETE FROM cliente_telefono WHERE id_cliente = $cliente->id";
                    
                    try
                    {
                        //echo "Beginning...";
                        $result = $this->conn->prepare($query);
                        $result->execute();
                        
                        $last_inserted_id = $this->conn->lastInsertId();
                        $affected_rows = $result->rowCount();
                        
                        $query = "INSERT INTO cliente_telefono (id_cliente, telefono, habilitado) VALUES";
                    
                        $valores = "";                  
                        
                        if(!is_null($cliente_nuevo->telefonos))
                        {
                            
                            if(count($cliente_nuevo->telefonos) > 0)
                            {                       
                                foreach ($cliente_nuevo->telefonos as $t)                       
                                    $valores = $valores. ", ($cliente_nuevo->id, '$t->telefono', 1)";
                                                            
                                $query = $query . substr($valores, 2);
                                
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
                        
                    }catch(PDOException $e)
                    {
                        trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
                    }
                    
                }catch(PDOException $e)
                {
                    trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
                }
                
			}
					
		}		

    }
    
	class TipoDocumentoBLO
	{
		public function Listar($filtro)
		{
			$cTipoDocDA = new TipoDocumentoDA();
			$tipos = $cTipoDocDA->Listar($filtro);
			return $tipos;
		}
	}

	class ClienteBLO
	{
		
		public function Listar($filtro)
		{
			$cCliDA = new ClienteDA();
			$clientes = $cCliDA->Listar($filtro);
			return $clientes;
		}
		
		public function Registrar($cliente)
		{
			$cCliDA = new ClienteDA();
			return $cCliDA->Registrar($cliente);			
		}
		
		public function Actualizar($cliente)
		{
			$cCliDA = new ClienteDA();
			return $cCliDA->Actualizar($cliente);
		}
		
		public function RetornarClienteXId($id_cliente)
		{
			$cCliDA = new ClienteDA();
			$clientes = $cCliDA->Listar("c.id = $id_cliente");
			if(!is_null($clientes))
			{
				if(count($clientes) > 0)
					return $clientes[0];
				else 
					return null;									
			}
			else
				return null;			
		}
		
		public function RetornarClienteXNombresYApellidos($nombres, $apellidos)
		{
			$cCliDA = new ClienteDA();
			return $cCliDA->Listar("c.nombres = '$nombres' AND c.apellidos = '$apellidos'");
		}
			
		public function ListarXKeyword($keyword)
		{
			$cCliDA = new ClienteDA();
			$clientes = $cCliDA->Listar("c.keyword = $keyword");			 
			return $clientes;
		}
		
		public function RetornarClienteXNroDocumento($id_tipo_documento, $nro_documento)
		{
			$cCliDA = new ClienteDA();
			$clientes = $cCliDA->Listar("c.id_tipo_documento = $id_tipo_documento AND nro_documento = '$nro_documento'");

			if(count($clientes) > 0)
					return $clientes[0];
				else 
					return null;									

		}
		
		public function ListarXCondiciones($id_tipo_documento, $nro_documento, $apellidos)
		{
			$filtro = "";
			$aux = 0;
			if($id_tipo_documento > 0)
			{
				$aux = 1;
				$filtro_cond = "AND c.id_tipo_documento = $id_tipo_documento";
			}
			if($nro_documento != "")
			{
				$aux = 1;
				$filtro_cond = "AND c.nro_documento LIKE '%$nro_documento%'";
			}
			if($apellidos != "")
			{
				$aux = 1;
				$filtro_cond = "AND c.apellidos LIKE '%$apellidos%'";
			}
			if($aux == 1)
			{
				$filtro_cond = substr($filtro_cond, 4);
				$filtro = $filtro_cond;
				$cCliDA = new ClienteDA();
				$clientes = $cCliDA->Listar($filtro);			 
				return $clientes;
			}
			else
				return null;
			
		}
		
		public function RegistrarTelefono($obj)
		{
			$cliDA = new ClienteDA();
			$cliDA->RegistrarTelefono($obj);
		}
		
		public function BorrarTelefonos($id_cliente)
		{
			$cliDA = new ClienteDA();
			$cliDA->BorrarTelefonos($id_cliente);
		}
	}
	
	
?>