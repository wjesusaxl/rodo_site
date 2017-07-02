<?php

class CajaTipo
{
	public $id;
	public $codigo;
	public $descripcion;
}
    
    class Caja
    {
        public $id;
        public $codigo;
		public $descripcion;
		public $id_centro;
		public $cod_centro;
		public $centro;
		public $id_caja_tipo;
		public $cod_caja_tipo;
		public $caja_tipo;
    }
	
	class CajaUsuario
	{
		public $id;
		public $id_usuario;
		public $usuario;
		public $id_caja;
		public $cod_caja;
		public $caja;
		public $habilitado;
		public $flag_ingreso;
		public $flag_salida;
		public $flag_responsable;
		public $id_centro;
		public $cod_centro;	
	}
    
    class CajaDA
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
			
			$query = "SELECT ca.id, ca.codigo, ca.id_centro, ce.codigo cod_centro, ce.descripcion centro, ca.descripcion,
			ct.codigo cod_caja_tipo, ct.descripcion caja_tipo 
			FROM caja ca
			INNER JOIN centro ce ON ca.id_centro = ce.id
			INNER JOIN caja_tipo ct ON ca.id_caja_tipo = ct.id $filtro";
            
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
                        $obj = new Caja();
                        $obj->id = $row['id'];
                        $obj->codigo = $row['codigo'];
                        $obj->id_centro = $row['id_centro'];
                        $obj->cod_centro = $row['cod_centro'];
                        $obj->centro = strtoupper($row['centro']);
						$obj->descripcion = strtoupper($row['descripcion']);
						$obj->id_caja_tipo = $row["id_caja_tipo"];
						$obj->cod_caja_tipo = strtoupper($row["cod_caja_tipo"]);
						$obj->caja_tipo = strtoupper($row["caja_tipo"]);
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}
		
		public function ListarCajaUsuario($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
			
			$query = "SELECT cu.id, cu.id_caja, cu.id_usuario, u.login usuario, ca.codigo cod_caja, ca.descripcion caja, cu.habilitado,
				ca.id_centro, ce.codigo cod_centro, cu.flag_responsable, cu.flag_ingreso, cu.flag_salida
				FROM caja_usuario cu
				INNER JOIN caja ca ON cu.id_caja = ca.id
				INNER JOIN usuario u ON cu.id_usuario = u.id
				INNER JOIN centro ce ON ca.id_centro = ce.id $filtro";
				
			//echo "Query: $query</br>";
							
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
                        $obj = new CajaUsuario();
                        $obj->id = $row["id"];
						$obj->id_caja = $row["id_caja"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario = strtoupper($row["usuario"]);
						$obj->cod_caja = $row["cod_caja"];
						$obj->caja = strtoupper($row["caja"]);
						$obj->habilitado = $row["habilitado"];
						$obj->id_centro = $row["id_centro"];
						$obj->cod_centro  = $row["centro"];
						$obj->flag_responsable = $row["flag_responsable"];
						$obj->flag_ingreso = $row["flag_ingreso"];
						$obj->flag_salida = $row["flag_salida"];
                        $lista[] = $obj;
                    }               
                
            }
            
			return $lista;
		}

		public function ModificarCajaUsuario($obj)
		{
			$query = "UPDATE caja_usuario
				SET habilitado = $obj->habilitado,
					flag_responsable = $obj->flag_responsable,
					flag_ingreso = $obj->flag_ingreso,
					flag_salida = $obj->flag_salida
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
		
		public function RegistrarCajaUsuario($obj)
		{
			$query = "INSERT caja_usuario (id_caja, id_usuario, habilitado, flag_responsable, flag_ingreso, flag_salida)
			VALUES ($obj->id_caja, $obj->id_usuario, $obj->habilitado, $obj->flag_responsable, $obj->flag_ingreso, $obj->flag_salida)";
			
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

	class CajaBLO
	{
		private function Listar($filtro)
		{
			$objDA = new CajaDA();
			$lista = $objDA->Listar($filtro);
			return $lista;
		}
		
		private function ListarCajaUsuario($filtro)
		{
			$objDA = new CajaDA();
			$lista = $objDA->ListarCajaUsuario($filtro);
			return $lista;
		}
		
		public function ListarCajaXIdCentro($id_centro)
		{
			$objDA = new CajaDA();
			$lista = $objDA->Listar("ca.id_centro = $id_centro");
			return $lista;
		}
		
		public function ListarCajaHabilitadaXIdUsuario($id_usuario, $id_centro)
		{
			$objDA = new CajaDA();
			$lista = $objDA->ListarCajaUsuario("cu.id_usuario = $id_usuario AND cu.habilitado = 1 AND ca.id_centro = $id_centro");
			return $lista;
		}
		
		public function ListarCajaXIdUsuario($id_usuario, $id_centro)
		{
			$objDA = new CajaDA();
			$lista = $objDA->ListarCajaUsuario("cu.id_usuario = $id_usuario AND ca.id_centro = $id_centro");
			return $lista;
		}
		
		public function ListarCajaHabilitadaIngresoXIdUsuario($id_usuario, $id_centro)
		{
			$objDA = new CajaDA();
			$lista = $objDA->ListarCajaUsuario("cu.id_usuario = $id_usuario AND cu.habilitado = 1 AND (cu.flag_responsable = 1 OR cu.flag_ingreso ) AND 
				ca.id_centro = $id_centro");
			return $lista;
		}
		
		public function ListarCajaHabilitadaSalidaXIdUsuario($id_usuario, $id_centro)
		{
			$objDA = new CajaDA();
			$lista = $this->ListarCajaUsuario("cu.id_usuario = $id_usuario AND cu.habilitado = 1 AND (cu.flag_responsable = 1 OR cu.flag_salida ) AND 
				ca.id_centro = $id_centro");
			return $lista;
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("id = $id");
			if(!is_null($lista))
				return $lista[0];
			else 
				return NULL;
		}
		
		public function ListarCajaTodas()
		{
			return $this->Listar("");
		}
		
		public function RetornarCajaUsuarioXIdCajaIdUsuario($id_caja, $id_usuario)
		{
			$lista = $this->ListarCajaUsuario("cu.id_caja = $id_caja AND cu.id_usuario = $id_usuario");
			if(!is_null($lista))
			{
				if(count($lista)> 0)
					return $lista[0];
				else
					return NULL;
			}
			else
				return NULL;
		}
		
		public function ModificarCajaUsuario($obj)
		{
			$objDA = new CajaDA();
			$objDA->ModificarCajaUsuario($obj);
		}
		
		public function RegistrarCajaUsuario($obj)
		{
			$objDA = new CajaDA();
			$objDA->RegistrarCajaUsuario($obj);
		}
	}
?>