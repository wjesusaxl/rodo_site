<?php
    
    class Test
    {
        public $tiempo;
        public $msg;
    }
	
	
    class TestDA
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
		
		public function Registrar($obj)
		{
			$query = "INSERT test(tiempo, msg) VALUES('$obj->tiempo', '$obj->msg')";
			mysql_query($query) or die(mysql_error());
		}
		
    	public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT * 
			FROM test $filtro";
			$result = mysql_query($query) or die(mysql_error());
			$lista = NULL;
            
            if(!is_null($result))
            {
                $lista = array();
                
                if(mysql_num_rows($result) > 0)
                {
                    while($row = mysql_fetch_array($result))
                    {
                        $obj = new Test();
                        $obj->tiempo = $row["tiempo"];
                        $obj->msg = $row["msg"];                                         
                        $lista[] = $obj;
                    }               
                }
            }
            
			return $lista;
		}
		
		
    }

	class TestBLO
	{
		public function Registrar($obj)
		{
			$tDA = new TestDA();
			$tDA->Registrar($obj);
		}
	}
?>