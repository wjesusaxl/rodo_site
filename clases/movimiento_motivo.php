<?php
    
    class MovimientoMotivo
	{
		public $id;
		public $codigo;
		public $descripcion;
		public $factor;
	}
    class MovimientoMotivoDA
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
		
		public function Registrar($obj)
		{
			$query = "INSERT INTO movimiento_motivo (codigo, descripcion, factor) 
			VALUES ('$obj->codigo', '$obj->nombre', '$obj->descripcion', $obj->factor);";
				
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
			$query = "UPDATE movimiento_motivo 
			SET codigo = '$obj->codigo',
				descripcion = '$obj->descripcion',
				factor = $obj->factor
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
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}
    }

	class MovimientoMotivoBLO
	{
		public function Listar($filtro)
		{
			
			$objDA = new MovimientoMotivoDA();			
			$lista = $objDA->Listar($filtro);
						
			return $lista;			
		}

		public function RetornarMovimientoMotivoXId($id)
		{
			if($id == 0)
				$filtro = "";
			else
				$filtro = "WHERE mm.id = $id";
			$lista = $this->Listar($filtro);
			return $lista;
		}
		
		public function RetornarMovimientoMotivoXCodigo($codigo)
		{
			if($codigo != "")
				$filtro = "mm.codigo = '$codigo'";
			
			$lista = $this->Listar($filtro);
			
			if($lista!= null)
				$obj = $lista[0];
			else 
				$obj = null;
			
			return $obj;
		}
		
		public function Modificar($obj)
		{
			$resultado = new OperacionResultado();
			
			if($obj != null)
			{
				if($obj->id > 0)
				{
					$objDA = new MovimientoMotivoDA();
					$objDA->Modificar($obj);
					
					$resultado->id = $obj->id;
					$resultado->codigo = "02";
					$resultado->isOK = true;
					$resultado->mensaje = "Motivo de Movimiento Actualizado Correctamente";	
				}
				else
				{
					$resultado->id = 0;
					$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "Motivo de Movimiento NO fue Actualizado. Favor revisar!";
				}	
			}
			else
			{
				$resultado->id = 0;
				$resultado->codigo = "01";
				$resultado->isOK = false;
				$resultado->mensaje = "Motivo de Movimiento a Actualizar no tiene valores!";
			}			
			return $resultado;						
		}
		
		public function Registrar($obj)
		{
			$resultado = new OperacionResultado();
			 
			$objDA = new MovimientoMotivoDA();
			
			$objX = $this->RetornarMovimientoMotivoXCodigo($obj->codigo);
			if($objX != null)
			{
				$resultado->id = $objX->id;
				$resultado->codigo = "01";
				$resultado->isOK = false;
				$resultado->mensaje = "Cod. de Motivo de Movimiento Ya Existe";
			}
			else
			{
				$objDA->Registrar($obj);
				
				$objX = $this->RetornarMovimientoMotivoXCodigo($obj->codigo);
				if($objX != null)
				{
					$resultado->id = $objX->id;
					$resultado->codigo = "02";
					$resultado->isOK = true;
					$resultado->mensaje = "Motivo de Movimiento creado exitosamente!";					
				}
				else
				{
					$resultado->id = 0;
					$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "No se creo el Motivo de Movimiento!";		
				}
			}
			
			return $resultado;
		}
	}
	
	
?>