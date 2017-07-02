<?php
    
    class TipoDocumento
	{
		public $id;
		public $codigo;
		public $descripcion;		
		
	}
    class TipoDocumentoDA
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
			
			/*#Seleccion de la base de datos a utilizar
			$db = $xml->database_configuration[0]->database[0];
			
			mysql_select_db($db) or die("Error al tratar de selecccionar esta base ". $db);*/
			
			if ($this->conn->connect_error) {
			  trigger_error('Database connection failed: '  . $this->conn->connect_error, E_USER_ERROR);
			}
		}
		
		public function Listar($filtro)
		{
			
			if($filtro != "")
				$filtro = "WHERE $filtro";
			
			$query = "SELECT id, codigo, descripcion FROM tipo_documento $filtro";
			
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
						$obj = new TipoDocumento();
						
						$obj->id = $row['id'];
						$obj->codigo = $row['codigo'];
						$obj->descripcion = $row['descripcion'];					
						
						$lista[] = $obj;
						
					}				
				
				
			}
			return $lista;
		}
		
		public function Registrar($obj)
		{
			$query = "INSERT INTO tipo_documento (codigo, descripcion) 
			VALUES ('$obj->codigo', '$obj->descripcion');";
				
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
			$query = "UPDATE tipo_documento 
			SET codigo = '$obj->codigo',
				descripcion = '$obj->descripcion'
			WHERE id = $obj->id";
			/*
			?>
			<script type="text/javascript">
				alert('<?php echo $query;?>');
			</script>
			
			<?php*/
			
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
    }

	class TipoDocumentoBLO
	{
		private function Listar($filtro)
		{
			$objDA = new TipoDocumentoDA();			
			return$objDA->Listar($filtro);
		}

		public function RetornarTipoDocumentoXId($id)
		{
			if($id == 0)
				$filtro = "";
			else
				$filtro = "WHERE id = $id";
			$lista = $this->Listar($filtro);
			return $lista;
		}
		
		public function RetornarTipoDocumentoXCodigo($codigo)
		{
			if($codigo != "")
				$filtro = "codigo = '$codigo'";
			
			$lista = $this->Listar($filtro);
			
			if($lista!= null)
				$obj = $lista[0];
			else 
				$obj = null;
			
			return $obj;
		}
		
		public function ListarTodos()
		{
			return $this->Listar("");
		}
		
		public function Modificar($obj)
		{
			$resultado = new OperacionResultado();
			
			if($obj != null)
			{
				if($obj->id > 0)
				{
					$objDA = new TipoDocumentoDA();
					$objDA->Modificar($obj);
					
					$resultado->id = $obj->id;
					$resultado->codigo = "02";
					$resultado->isOK = true;
					$resultado->mensaje = "Tipo de Documento Actualizado Correctamente";	
				}
				else
				{
					$resultado->id = 0;
					$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "Tipo de Documento NO fue Actualizado. Favor revisar!";
				}	
			}
			else
			{
				$resultado->id = 0;
				$resultado->codigo = "01";
				$resultado->isOK = false;
				$resultado->mensaje = "Tipo de Documento a Actualizar no tiene valores!";
			}			
			return $resultado;						
		}
		
		public function Registrar($obj)
		{
			$resultado = new OperacionResultado();
			 
			$objDA = new TipoDocumentoDA();
			
			$objX = $this->RetornarTipoDocumentoXCodigo($obj->codigo);
			if($objX != null)
			{
				$resultado->id = $objX->id;
				$resultado->codigo = "01";
				$resultado->isOK = false;
				$resultado->mensaje = "Cod. de Tipo de Documento Ya Existe";
			}
			else
			{
				$objDA->Registrar($obj);
				
				$objX = $this->RetornarTipoDocumentoXCodigo($obj->codigo);
				if($objX != null)
				{
					$resultado->id = $objX->id;
					$resultado->codigo = "02";
					$resultado->isOK = true;
					$resultado->mensaje = "Tipo de Documento creado exitosamente!";					
				}
				else
				{
					$resultado->id = 0;
					$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "No se creó el Tipo de Documento!";		
				}
			}
			
			return $resultado;
		}
	}
	
	
?>