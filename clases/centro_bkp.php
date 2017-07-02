<?php
    
    class Centro
    {
        public $id;
        public $codigo;
        public $descripcion;
        public $direccion;
    }
    
    class CentroDA
    {
    	
		public function __construct()
		{
			
			$file = "config.xml";
			$xml = simplexml_load_file($file);
			 
			#coneccion con el MDB en MySql
			//$link = mysql_connect("localhost","peruingc","banner2010", false, 65536)
			$this->conn = mysql_connect(
				$xml->database_configuration[0]->host[0], 
				$xml->database_configuration[0]->username[0], 
				$xml->database_configuration[0]->password[0], TRUE, 131074)	
			or die ("no se ha podido conectar al host: ".$xml->database_configuration[0]->host[0]." usuario: ".$xml->database_configuration[0]->username[0]);
		
			#Seleccion de la base de datos a utilizar
			$db = $xml->database_configuration[0]->database[0];
			
			mysql_select_db($db) or die("Error al tratar de selecccionar esta base ". $db);
		}
		
    	public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT id, codigo, descripcion, direccion FROM centro $filtro";
			$result = mysql_query($query) or die(mysql_error());
			$lista = NULL;
            
            if(!is_null($result))
            {
                $lista = array();
                
                if(mysql_num_rows($result) > 0)
                {
                    while($row = mysql_fetch_array($result))
                    {
                        $obj = new Centro();
                        $obj->id = $row['id'];
                        $obj->codigo = $row['codigo'];
                        $obj->descripcion = $row['descripcion'];
                        $obj->direccion = $row['direccion'];                 
                        $lista[] = $obj;
                    }               
                }
            }
            
			return $lista;
		}	
    }

	class CentroBLO
	{
		private function Listar($filtro)
		{
			$cCenDA = new CentroDA();
			$centros = $cCenDA->Listar($filtro);
			return $centros;
		}
		
		public function ListarTodos()
		{
			return $this->Listar("id > 0 ORDER BY 1");
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("id = $id");
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;
		}
		public function ListarUbicacionesXCentro($id_centro)
		{
			$cCenUbBLO = new CentroUbicacionBLO();			
			$ubicaciones = $cCenUbBLO->Listar("WHERE id_centro = $id_centro");			 
			return $ubicaciones;			
		}
		
		public function ListarLugaresAtencionXUbicacion($id_centro_ubicacion)
		{
			$cLugAtBLO = new LugarAtencionBLO();
			$lugaresatencion = $cLugAtBLO->Listar("WHERE id_centro_ubicacion = $id_centro_ubicacion AND estado = 1");
			return $lugaresatencion;			
		}
	}
?>