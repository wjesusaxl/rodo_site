<?php
    
    class Moneda
    {
        public $id;
        public $codigo;
        public $nombre;
        public $simbolo;
    }
    
    class MonedaDA
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
		
    	public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT id, codigo, nombre, simbolo FROM moneda $filtro";
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
                        $obj = new Moneda();
                        $obj->id = $row['id'];
                        $obj->codigo = $row['codigo'];
                        $obj->nombre = $row['nombre'];
                        $obj->simbolo = $row['simbolo'];                 
                        $lista[] = $obj;
                   }               
                
            }
            
			return $lista;
		}
    }

	class MonedaBLO
	{
	    private $objDA;
        
	    public function __construct()
        {
            $objDA = new MonedaDA();            
        }
        
		public function Listar($filtro)
		{
			return $objDA->Listar($filtro);
		}
        
        public function RetornarXId($id)
        {
            return $objDA->Listar("id = $id");
        }
        
        public function RetornarXCodigo($codigo)
        {
            return $objDA->Listar("codigo = '$codigo'");
        }
		
		
	}
?>