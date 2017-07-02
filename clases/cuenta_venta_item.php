<?php
    
    
    
    class CuentaVentaDetalle
    {
    	public $id;
    	public $auto_key;
		public $id_centro;
		public $fecha_hora;
		public $id_usuario_creacion;
		public $fecha_hora_cierre;
		public $id_usuario_cierre;
		public $estado;
		public $total;
		public $id_lugar_atencion;
		public $id_cliente;
    }
	
    class CuentaVentaDetalleDA
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
		
    	public function RegistrarDetalle($auto_key, $id_centro, $id_usuario_creacion, $estado, $id_lugar_atencion, $id_cliente)
		{
			$query = "INSERT INTO cuenta_venta(auto_key, id_centro, id_usuario_creacion, estado, id_lugar_atencion, total)
			VALUES('$auto_key', $id_centro, $id_usuario_creacion, 0, $id_lugar_atencion, 0, 0.0)";
			
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
			
			$query = "SELECT id FROM cuenta_venta WHERE auto_key = '$auto_key'";
            
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
                $row = $result->fetch();
                if($auto_key == $row['auto_key'])
                    $resultado = true;
                else
                    $resultado = false;
                   
            }
    
		}
			
    }

	

?>