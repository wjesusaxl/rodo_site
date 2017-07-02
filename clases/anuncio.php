<?php
    
    class Anuncio
    {
        public $id;
        public $id_centro;
        public $id_usuario;
        public $usuario;
		public $usuario_nombres_apellidos;
        public $fecha_hora_inicio;
        public $fecha_hora_fin;
		public $mensaje;
		public $flag_anulado;
    }
    
    class AnuncioDA
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
		
		public function Registrar($obj)
		{
			$query = "INSERT INTO anuncio(id_centro, id_usuario, fecha_hora_inicio, fecha_hora_fin, mensaje, flag_anulado)
				VALUES($obj->id_centro, $obj->id_usuario, '$obj->fecha_hora_inicio', '$obj->fecha_hora_fin', '$obj->mensaje', $obj->flag_anulado)";
				
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
			$query = "UPDATE anuncio
			SET fecha_hora_inicio = '$obj->fecha_hora_inicio',
			fecha_hora_fin = '$obj->fecha_hora_fin',
			mensaje = '$obj->mensaje',
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
		
    	public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT a.id, a.id_centro, a.id_usuario, u.login usuario, a.fecha_hora_inicio, a.fecha_hora_fin, a.mensaje, a.flag_anulado,
				CONCAT(u.nombres,' ', u.apellidos) usuario_nombres_apellidos
				FROM anuncio a
				INNER JOIN usuario u ON a.id_usuario = u.id $filtro";
			
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
                        $obj = new Anuncio();
                        $obj->id = $row["id"];
				        $obj->id_centro = $row["id_centro"];
				        $obj->id_usuario = $row["id_usuario"];
				        $obj->usuario = $row["usuario"];
						$obj->usuario_nombres_apellidos = $row["usuario_nombres_apellidos"];
				        $obj->fecha_hora_inicio = $row["fecha_hora_inicio"];
				        $obj->fecha_hora_fin = $row["fecha_hora_fin"];
				        $obj->mensaje = $row["mensaje"];
						$obj->flag_anulado = $row["flag_anulado"];
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}
    }

	class AnuncioBLO
	{
		private function Listar($filtro)
		{
			$cCenDA = new AnuncioDA();
			return $cCenDA->Listar($filtro);
		}
		
		public function Registrar($obj)
		{
			$objDA = new AnuncioDA();
			$objDA->Registrar($obj);
		}
		
		public function Modificar($obj)
		{
			$objDA = new AnuncioDA();
			$objDA->Modificar($obj);
		}
		
		public function ListarActivos($id_centro)
		{
			$fecha_hora = date('Y-m-d H:i:s');
			
			$filtro = "a.fecha_hora_inicio <= '$fecha_hora' AND
			a.fecha_hora_fin >= '$fecha_hora' and a.id_centro = $id_centro and a.flag_anulado = 0 ORDER BY a.id DESC";
			
			return $this->Listar($filtro);			
		}		
		
		public function ListarXIdUsuario($id_usuario, $id_centro)
		{
			$filtro = "a.id_usuario = $id_usuario AND a.id_centro = $id_centro AND a.flag_anulado = 0 ORDER BY a.id DESC";
			return $this->Listar($filtro);
		}
		
		public function ListarTodos($id_centro)
		{
			$filtro = "a.id_centro = $id_centro ORDER BY a.id DESC";
			return $this->Listar($filtro);
		}
		
		public function ListarMisAnuncios($id_centro, $id_usuario)
		{
			$filtro = "a.id_usuario = $id_usuario AND a.id_centro = $id_centro AND a.flag_anulado = 0 ORDER BY a.id DESC";
			return $this->Listar($filtro);
		}
		
		public function RetornarXId($id_anuncio)
		{
			$filtro = "a.id = $id_anuncio";
			
			$lista = $this->Listar($filtro);
			
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			else
				return NULL; 
		}
	}
?>