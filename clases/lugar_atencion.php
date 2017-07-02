<?php
    
    class LugarAtencionDA
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
			
			$query = "SELECT la.id, la.id_centro, la.id_centro_ubicacion, la.codigo, la.descripcion, la.estado,
				cu.codigo cod_centro_ubicacion, cu.descripcion centro_ubicacion
				FROM lugar_atencion la
				INNER JOIN centro_ubicacion cu ON la.id_centro_ubicacion = cu.id ".$filtro;
			
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
						$obj = new LugarAtencion();
						$obj->id = $row["id"];
						$obj->id_centro = $row["id_centro"];
						$obj->id_centro_ubicacion = $row["id_centro_ubicacion"];
						$obj->codigo = $row["codigo"];
						$obj->descripcion = $row["descripcion"];
						$obj->estado = $row["estado"];
						$obj->cod_centro_ubicacion = $row["cod_centro_ubicacion"];
						$obj->centro_ubicacion = $row["centro_ubicacion"];
						$lista[] = $obj;
						
					 }               
                
            }
            
			return $lista;
		}	
    }

	class LugarAtencionBLO
	{
		public function Listar($filtro)
		{
			$cLuAtDA = new LugarAtencionDA();			
			$luatencion = $cLuAtDA->Listar($filtro);			
			return $luatencion;			
		}
		
		public function ListarXIdCentroUbicacion($id)
		{
			return $this->Listar("la.id_centro_ubicacion = $id ORDER BY la.id");
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("la.id = $id");
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
				
			}
			else
				return null;
		}
		
	}
	
	class LugarAtencion
	{
		public $id;
		public $id_centro;
		public $id_centro_ubicacion;
		public $codigo;
		public $descripcion;
		public $estado;
		public $cod_centro_ubicacion;
		public $centro_ubicacion;
		
	}
?>