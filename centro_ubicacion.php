<?php
    
    class CentroUbicacionDA
    {
    	
		public function __construct()
		{
			
			$file = "config.xml";
			$xml = simplexml_load_file($file);
			 
			#coneccion con el MDB en MySql
			//$link = mysql_connect("localhost","peruingc","banner2010", false, 65536)
			$link = mysql_connect(
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
						
			$query = "SELECT id, codigo, descripcion FROM centro_ubicacion ".$filtro;			
			$result = mysql_query($query) or die(mysql_error());
			$ubicaciones = NULL;			
			if(mysql_num_rows($result) > 0)
			{
				$ubicaciones = array();
				
				while($row = mysql_fetch_array($result))
				{
					$ubicacion = new CentroUbicacion();
					$ubicacion->id = $row['id'];
					$ubicacion->codigo = $row['codigo'];
					$ubicacion->descripcion = $row['descripcion'];										
					$ubicaciones[] = $ubicacion;
					
				}				
			}
			return $ubicaciones;
		}	
    }

	class CentroUbicacionBLO
	{
		public function Listar()
		{
			$cCenUbDA = new CentroUbicacionDA();			
			$ubicaciones = $cCenUbDA->Listar('');			
			return $ubicaciones;			
		}
		
	}
	
	class CentroUbicacion
	{
		public $id;
		public $codigo;
		public $descripcion;
		
	}
?>