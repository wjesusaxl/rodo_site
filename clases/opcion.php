<?php

class Opcion {
	public $id;
	public $codigo;
	public $descripcion;
	public $opcion_key;
	public $estado;
	public $id_opcion_padre;
	public $flag_general;
	public $flag_menu;
	public $enlace;
	public $flag_publica;
	public $flag_menu_principal;
	public $menu_posicion;
}

class CentroOpcion {
	public $id;
	public $id_opcion;
	public $id_centro;
	public $opcion_estado;
	public $flag_habilitado;
	public $opcion_key;
}

class UsuarioCentroOpcion {
	public $id;
	public $id_usuario;
	public $id_opcion;
	public $id_centro;
	public $opcion_estado;
	public $opcion_flag_general;
	public $flag_habilitado;
	public $usuario_habilitado;
	public $usuario;
}

class UsuarioOpcion {
	public $id;
	public $id_usuario;
	public $id_opcion;
	public $flag_habilitado;
	public $usuario_habilitado;
}

class OpcionDA 
{
	private $conn;
	public function __construct() 
	{
		$file = "config.xml";
		//$file = "../clases/config.xml";
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
		if ($filtro != "")
			$filtro = " WHERE $filtro";
		else
			$filtro = "";
		
		$query = "SELECT o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
		o.menu_posicion, o.flag_menu_principal, o.flag_general
		FROM opcion o $filtro";
		
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
					$obj = new Opcion();
					
					$obj->id = $row['id'];
					$obj->codigo = $row['codigo'];
					$obj->descripcion = $row['descripcion'];
					$obj->estado = $row['estado'];
					$obj->opcion_key = $row['opcion_key'];
					$obj->flag_menu = $row['flag_menu'];
					$obj->flag_general = $row['flag_general'];
					$obj->enlace = $row['enlace'];
					$obj->id_opcion_padre = $row['id_opcion_padre'];
					$obj->flag_publica = $row['flag_publica'];
					$obj->menu_posicion = $row["menu_posicion"];
					$obj->flag_menu_principal = $row["flag_menu_principal"];
					
					$lista[] = $obj;
				}
			
		}
	
		return $lista;
	}
	
	public function Listar2($filtro) 
	{
		if ($filtro != "")
			$filtro = " WHERE $filtro";
		else
			$filtro = "";
		
		$query = "SELECT o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
		o.menu_posicion, o.flag_menu_principal, o.flag_general
		FROM opcion o $filtro";
		
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
					$obj = new Opcion();
						
					$obj->id = $row['id'];
					$obj->codigo = $row['codigo'];
					$obj->descripcion = $row['descripcion'];
					$obj->estado = $row['estado'];
					$obj->opcion_key = $row['opcion_key'];
					$obj->flag_menu = $row['flag_menu'];
					$obj->flag_general = $row['flag_general'];
					$obj->enlace = $row['enlace'];
					$obj->id_opcion_padre = $row['id_opcion_padre'];
					$obj->flag_publica = $row['flag_publica'];
					$obj->menu_posicion = $row["menu_posicion"];
					$obj->flag_menu_principal = $row["flag_menu_principal"];
						
					$lista[] = $obj;
						
				}               
			
		}
            
		return $lista;
	}

	public function ListarCentroOpcion($filtro)
	{
		if ($filtro != "")
			$filtro = " WHERE $filtro";
		else
			$filtro = "";
	
		$query = "SELECT co.id, co.id_opcion, co.id_centro, o.estado opcion_estado, o.opcion_key, co.flag_habilitado
		FROM centro_opcion co 
		INNER JOIN opcion o ON co.id_opcion = o.id $filtro ";
		
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
                	
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
					$obj = new CentroOpcion();
					
					$obj->id = $row["id"];
					$obj->id_opcion = $row["id_opcion"];
					$obj->id_centro = $row["id_centro"];
					$obj->opcion_estado = $row["opcion_estado"];
					$obj->opcion_key = $row["opcion_key"];
					$obj->flag_habilitado = $row["flag_habilitado"];
					$lista[] = $obj;
					
				}               
			}
		
            
		return $lista;
	}

	public function ListarUsuarioCentroOpcion($filtro)
	{
		if ($filtro != "")
			$filtro = " WHERE $filtro";
		else
			$filtro = "";
		
		$query = "SELECT uco.id, uco.id_usuario, uco.id_opcion, uco.id_centro, o.estado, o.flag_general, uco.flag_habilitado,
		u.flag_habilitado usuario_habilitado, u.login usuario
		FROM usuario_centro_opcion uco 
		INNER JOIN opcion o ON uco.id_opcion = o.id
		INNER JOIN usuario u ON uco.id_usuario = u.id $filtro";
		
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
					$obj = new UsuarioCentroOpcion();
	
					$obj->id = $row["id"];
					$obj->id_usuario = $row["id_usuario"];
					$obj->id_opcion = $row["id_opcion"];
					$obj->id_centro = $row["id_centro"];
					$obj->opcion_estado = $row["opcion_estado"];
					$obj->opcion_flag_general = $row["opcion_flag_general"];
					$obj->flag_habilitado = $row["flag_habilitado"];
					$obj->usuario_habilitado = $row["usuario_habilitado"];
					$obj->usuario = $row["usuario"];
					$lista[] = $obj;
	
				}
			
		}
	
		return $lista;
	}



	public function ListarUsuarioOpcion($filtro) 
	{
		if ($filtro != "")
			$filtro = " WHERE $filtro";
		else
		$filtro = "";
	
		$query = "SELECT uo.id, uo.id_usuario, uo.id_opcion, uo.flag_habilitado, u.flag_habilitado usuario_habilitado 
		FROM usuario_opcion uo 
		INNER JOIN opcion o ON uo.id_opcion = o.id 
		INNER JOIN usuario u ON uo.id_usuario = u.id $filtro";
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
					$obj = new UsuarioOpcion();
					$obj->id = $row['id'];
					$obj->id_usuario = $row['id_usuario'];
					$obj->id_opcion = $row['id_opcion'];
					$obj->flag_habilitado = $row['flag_habilitado'];
					$obj->usuario_habilitado = $row['usuario_habilitado'];
					$lista[] = $obj;
				}               
			
		}
            
		return $lista;
	}

	public function ListarMenuSubItemsXIdUsuarioIdCentro($id_usuario, $id_centro)
	{
		$query = "SELECT DISTINCT o.id_opcion_padre, o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.enlace, o.flag_menu, o.flag_publica,
		o.flag_menu_principal, o.flag_general
		FROM opcion o
		WHERE o.flag_general = 1 AND o.flag_publica = 1 AND o.estado = 1 AND
		o.id_opcion_padre IS NOT NULL AND o.flag_menu_principal = 1
		UNION
		SELECT DISTINCT o.id_opcion_padre, o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.enlace, o.flag_menu, o.flag_publica,
		o.flag_menu_principal, o.flag_general
		FROM opcion o
		INNER JOIN usuario_opcion uo ON o.id = uo.id_opcion
		INNER JOIN usuario u ON uo.id_usuario = u.id
		WHERE o.flag_general = 1 AND o.flag_publica = 0 AND o.estado = 1 AND
		o.id_opcion_padre IS NOT NULL AND uo.flag_habilitado = 1 AND
		uo.id_usuario = $id_usuario AND o.flag_menu_principal = 1 AND u.flag_habilitado = 1
		UNION
		SELECT DISTINCT o.id_opcion_padre, o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.enlace, o.flag_menu, o.flag_publica,
		o.flag_menu_principal, o.flag_general
		FROM opcion o
		INNER JOIN centro_opcion co ON o.id = co.id_opcion
		WHERE o.flag_general = 0 AND o.flag_publica = 1 AND o.estado = 1 AND
		co.flag_habilitado = 1 AND o.flag_menu_principal = 1 AND o.id_opcion_padre IS NOT NULL AND
		co.id_centro= $id_centro
		UNION
		SELECT DISTINCT o.id_opcion_padre, o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.enlace, o.flag_menu, o.flag_publica,
		o.flag_menu_principal, o.flag_general
		FROM opcion o
		INNER JOIN centro_opcion co ON o.id = co.id_opcion
		INNER JOIN usuario_centro_opcion uco ON co.id_opcion = uco.id_opcion AND uco.id_centro = co.id_centro
		INNER JOIN usuario u ON uco.id_usuario = u.id
		WHERE o.flag_general = 0 AND o.flag_publica = 0 AND o.estado = 1 
		AND co.flag_habilitado = 1 AND o.flag_menu_principal = 1 AND 
		o.id_opcion_padre IS NOT NULL AND co.id_centro= $id_centro AND 
		uco.flag_habilitado = 1 AND uco.id_usuario = $id_usuario AND u.flag_habilitado = 1
		ORDER BY 1,2";

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
					$obj = new Opcion();
					
					$obj->id = $row['id'];
					$obj->codigo = $row['codigo'];
					$obj->descripcion = $row['descripcion'];
					$obj->estado = $row['estado'];
					$obj->opcion_key = $row['opcion_key'];
					$obj->flag_menu = $row['flag_menu'];
					$obj->enlace = $row['enlace'];
					$obj->id_opcion_padre = $row['id_opcion_padre'];
					$obj->flag_publica = $row['flag_publica'];
					$obj->cod_centro = $row['cod_centro'];
					$obj->centro = $row['centro'];
					$obj->menu_posicion = $row["menu_posicion"];
					$obj->flag_menu_principal = $row["flag_menu_principal"];
					
					$lista[] = $obj;

				}               
			
		}
            
		return $lista;
	}

	public function ListarMenuTopItemsXIdUsuarioIdCentro($id_usuario, $id_centro)
	{
		$query = "SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN opcion oh ON o.id = oh.id_opcion_padre
			WHERE oh.flag_menu_principal = 1 AND o.id_opcion_padre IS NULL 
			AND oh.flag_publica = 1 AND oh.flag_general = 1 AND oh.estado = 1 AND
			o.estado = 1
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN opcion oh ON o.id = oh.id_opcion_padre
			INNER JOIN usuario_opcion uo ON oh.id = uo.id_opcion 
			INNER JOIN usuario u ON uo.id_usuario = u.id
			WHERE oh.flag_menu_principal = 1 AND o.id_opcion_padre IS NULL AND 
			oh.flag_publica = 0 AND oh.flag_general = 1 AND uo.id_usuario = $id_usuario AND 
			u.flag_habilitado = 1 AND uo.flag_habilitado = 1 AND oh.estado = 1 AND
			o.estado = 1 AND u.flag_habilitado = 1
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			WHERE o.id_opcion_padre IS NULL AND o.flag_publica = 1 AND
			o.flag_general = 1 AND o.flag_menu_principal = 1 AND o.estado = 1
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN usuario_opcion uo ON o.id = uo.id_opcion
			INNER JOIN usuario u ON uo.id_usuario = u.id
			WHERE o.id_opcion_padre IS NULL AND o.flag_publica = 0 AND
			o.flag_general = 1 AND o.flag_menu_principal = 1 AND
			u.flag_habilitado = 1 AND uo.id_usuario = $id_usuario AND o.estado = 1
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN opcion oh ON oh.id_opcion_padre = o.id
			INNER JOIN centro_opcion co ON oh.id = co.id_opcion 
			WHERE co.flag_habilitado = 1 AND oh.estado = 1 AND
			oh.flag_general = 0 AND oh.flag_menu_principal = 1 AND
			oh.flag_publica = 1 AND oh.flag_general = 0 AND o.estado = 1 AND
			co.id_centro= $id_centro
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN opcion oh ON oh.id_opcion_padre = o.id
			INNER JOIN centro_opcion co ON oh.id = co.id_opcion
			INNER JOIN usuario_centro_opcion uco ON oh.id = uco.id_opcion AND co.id_centro = uco.id_centro
			INNER JOIN usuario u ON uco.id_usuario = u.id
			WHERE oh.estado = 1 AND oh.flag_menu_principal = 1 AND
			co.flag_habilitado = 1 AND uco.flag_habilitado = 1 AND 
			u.flag_habilitado = 1 AND uco.id_usuario = $id_usuario AND 
			oh.flag_general = 0 AND oh.flag_publica = 0 AND
			o.estado = 1 AND co.id_centro= $id_centro
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN centro_opcion co ON o.id = co.id_opcion
			WHERE o.id_opcion_padre IS NULL AND o.flag_publica = 1 AND
			o.flag_general = 0 AND o.estado = 1 AND co.flag_habilitado = 1 AND
			co.id_centro= $id_centro AND o.flag_menu_principal = 1
			UNION
			SELECT DISTINCT o.menu_posicion, o.id, o.opcion_key, o.codigo, o.descripcion, o.estado, o.id_opcion_padre, o.enlace, o.flag_menu, o.flag_publica,
			o.flag_menu_principal, o.flag_general
			FROM opcion o
			INNER JOIN centro_opcion co ON o.id = co.id_opcion
			INNER JOIN usuario_centro_opcion uco ON o.id = uco.id_opcion AND co.id_centro = uco.id_centro
			INNER JOIN usuario u ON uco.id_usuario = u.id
			WHERE o.id_opcion_padre IS NULL AND o.estado = 1 AND
			o.flag_menu_principal = 1 AND co.flag_habilitado = 1 AND 
			uco.flag_habilitado = 1 AND u.flag_habilitado = 1 AND 
			uco.id_usuario = $id_usuario AND o.flag_general = 0 AND o.flag_publica = 0 AND
			co.id_centro= $id_centro
			ORDER BY 1";
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
					$obj = new Opcion();

					$obj->id = $row['id'];
					$obj->codigo = $row['codigo'];
					$obj->descripcion = $row['descripcion'];
					$obj->estado = $row['estado'];
					$obj->opcion_key = $row['opcion_key'];
					$obj->flag_menu = $row['flag_menu'];
					$obj->enlace = $row['enlace'];
					$obj->id_opcion_padre = $row['id_opcion_padre'];
					$obj->flag_publica = $row['flag_publica'];
					$obj->cod_centro = $row['cod_centro'];
					$obj->centro = $row['centro'];
					$obj->menu_posicion = $row["menu_posicion"];
					$obj->flag_menu_principal = $row["flag_menu_principal"];
					
					$lista[] = $obj;
				}
			
		}

		return $lista;
	}

	public function Registrar($opcion) 
	{
	
		if ($opcion->id_opcion_padre != NULL)
			$iop = "$opcion->id_opcion_padre";
		else
			$iop = "NULL";
		
		$query = "INSERT INTO opcion (opcion_key, codigo, descripcion, flag_menu, estado, enlace, flag_publica, flag_general, id_opcion_padre, flag_menu_principal, menu_posicion) 
			VALUES ('$opcion->opcion_key', '$opcion->codigo', '$opcion->descripcion', $opcion->flag_menu, $opcion->estado, '$opcion->enlace', $opcion->flag_publica, $opcion->flag_general, 
			$iop, $opcion->flag_menu_principal, $opcion->menu_posicion);";
			
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

	public function DesahibilitarOpcionesXIdUsuario($id_usuario)
	{
		$query = "UPDATE usuario_opcion SET flag_habilitado = 0 WHERE id_usuario = $id_usuario AND id > 0"; 
		
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

	public function ModificarUsuarioOpcion($obj)
	{
		$query = "UPDATE usuario_opcion SET flag_habilitado = $obj->flag_habilitado WHERE id = $obj->id"; 	
		//echo "Update: $query</br>";
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

	public function RegistrarUsuarioOpcion($obj)
	{
		$query = "INSERT INTO usuario_opcion (id_usuario, id_opcion, flag_habilitado) VALUES($obj->id_usuario, $obj->id_opcion, $obj->flag_habilitado)";
	
		//echo "insert: $query</br>"; 
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
		$query = "UPDATE opcion
		SET	estado = $obj->estado,
		flag_menu = $obj->flag_menu,
		flag_menu_principal = $obj->flag_menu_principal,
		flag_publica = $obj->flag_publica,
		flag_general = $obj->flag_general
		WHERE id = $obj->id";
		
		//echo "Query: $query</br>";
		
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
	
	public function RegistrarCentroOpcion($obj)
	{
		$query = "INSERT centro_opcion (id_centro, id_opcion, flag_habilitado)
		VALUES ($obj->id_centro, $obj->id_opcion, $obj->flag_habilitado)";
		
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
	
	public function RegistrarUsuarioCentroOpcion($obj)
	{
		$query = "INSERT usuario_centro_opcion (id_usuario, id_centro, id_opcion, flag_habilitado)
		VALUES ($obj->id_usuario, $obj->id_centro, $obj->id_opcion, $obj->flag_habilitado)";
		
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
	
	public function ModificarCentroOpcion($obj)
	{
		$query = "UPDATE centro_opcion
			SET flag_habilitado = $obj->flag_habilitado
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
	
	public function ModificarUsuarioCentroOpcion($obj)
	{
		$query = "UPDATE usuario_centro_opcion
			SET flag_habilitado = $obj->flag_habilitado
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

}

class OpcionBLO 
{

	private function Listar($filtro) 
	{
		$opcDA = new OpcionDA();
		return $opcDA->Listar($filtro); 
	}
	
	private function Listar2($filtro) 
	{
		$opcDA = new OpcionDA();
		return $opcDA->Listar2($filtro); 
	}

	public function ListarTodas()
	{
		return $this->Listar("");
	}
	
	public function ListarActivas()
	{
		return $this->Listar("o.estado = 1");
	}

	/*public function ListarOpcionPadreXIdCentro($id_centro)
	{
		return $this->Listar("id_opcion_padre IS NULL AND estado = 1 AND id_centro = $id_centro ORDER BY descripcion");
	}*/
	
	private function ListarCentroOpcion($filtro)
	{
		$opcDA = new OpcionDA();
		return $opcDA->ListarCentroOpcion($filtro);		
	}

	public function ListarOpcionesHijasXOpcionPadreId($id_opcion_padre)
	{
		return $this->Listar("o.id_opcion_padre = $id_opcion_padre ORDER BY o.descripcion");
	}

	public function ListarUsuarioOpcionXIdUsuario($id_usuario)
	{
		$opcDA = new OpcionDA();
		$lista = NULL; 
		if($id_usuario > 0) 
			$lista = $opcDA->ListarUsuarioOpcion("uo.id_usuario = $id_usuario AND o.estado = 1 ");
		return $lista;
	}
	
	public function ListarUsuarioCentroOpcionXIdUsuario($id_usuario)
	{
		$opcDA = new OpcionDA();
		return $opcDA->ListarUsuarioCentroOpcion("uco.id_usuario = $id_usuario AND o.estado = 1");
	}

	public function ListarOpcionesPadreMenu()
	{
		return $this->Listar("id_opcion_padre IS NULL AND flag_menu_principal = 1 AND estado = 1");
	}

	public function ListarOpcionesPadre()
	{
		return $this->Listar("o.id_opcion_padre IS NULL ORDER BY o.codigo");
	}

	private function ListarUsuarioOpcion($filtro) 
	{
		$opcDA = new OpcionDA();		
		return $opcDA->ListarUsuarioOpcion($filtro);
		 
	}

	public function RetornarOpcionXId($id) 
	{
		$obj = null;
		
		$lista = $this->Listar("o.id = $id");
		if (!is_null($lista))
			if (count($lista) == 1)
				return $lista[0];
			else 
				return NULL;
		else 
			return NULL;
	}

	public function RetornarOpcionXKey($key) 
	{
		$lista = $this->Listar("o.opcion_key = '$key'");
		if (!is_null($lista))
			if (count($lista) == 1)
				return $lista[0];
			else 
				return NULL;
		else 
			return NULL;		
	}
	
	public function ListarOpcionesXIdOpcionPadre($id) 
	{
		$filtro = "o.id_opcion_padre = $id AND o.estado = 1";
		$opciones = $this->Listar($filtro);
		return $opciones;
	}
	
	public function RetornarUsuarioOpcion($id_usuario, $id_opcion) 
	{
		$opcDA = new OpcionDA();
		$opcion = NULL;
		if ($id_usuario > 0 && $id_opcion > 0) 
		{ 
			$lista = $opcDA->ListarUsuarioOpcion("uo.id_usuario = $id_usuario AND uo.id_opcion = $id_opcion AND o.estado = 1");
	
			if (count($lista) > 0)
				$opcion = $lista[0];
		} 
		else
			echo "Id Usuario: $id_usuario - Id Opcion: $id_opcion";
		return $opcion;	
	}
	
	public function RetornarUsuarioCentroOpcion($id_usuario, $id_opcion, $id_centro )
	{
		$opcDA = new OpcionDA();
		$opcion = NULL;
		if ($id_usuario > 0 && $id_opcion > 0 && $id_centro > 0) 
		{ 
			$lista = $opcDA->ListarUsuarioCentroOpcion("uco.id_usuario = $id_usuario AND uco.id_opcion = $id_opcion AND 
				uco.id_centro = $id_centro AND o.estado = 1");
			
			if(!is_null($lista))			
				if (count($lista) > 0)
					$opcion = $lista[0];						
		} 
		else
			echo "Id Usuario: $id_usuario - Id Opcion: $id_opcion - Id Centro: $id_centro";
		return $opcion;
	}
	
	public function Registrar($opcion) 
	{
		$resultado = new OperacionResultado();
		$opcDA = new OpcionDA();
	
		$opcDA->Registrar($opcion);
	
		$opc = $this->RetornarOpcionXKey($opcion->opcion_key);
		if ($opc != null) 
		{
			$resultado->id = $opc->id;
			$resultado->codigo = "02";
			$resultado->isOK = true;
			$resultado->mensaje = "Opcion creada!";
		} 
		else 
		{
			$resultado->id = 0;
			$resultado->codigo = "01";
			$resultado->isOK = false;
			$resultado->mensaje = "No se creó la opción!";
		}
	
	return $resultado;
	}
	
	public function ValidarOpcionXIdUsuario($opcion_key, $id_usuario, $id_centro) 
	{
	
		$resultado = new OperacionResultado();
		$opcion = $this->RetornarOpcionXKey($opcion_key);
		
		if (is_null($opcion)) 
		{
			$resultado->id = 0;
			$resultado->codigo = "00";
			$resultado->isOK = FALSE;
			$resultado->mensaje = "No se encontró la opción"; 
		} 
		else 
		{ 
			if ($opcion->flag_publica) 
			{
				$permiso = true;
	
				$resultado->id = 0;
				$resultado->codigo = "02";
				$resultado->isOK = TRUE;
				$resultado->mensaje = "Permiso concedido";
			} 
			else 
			{
				if($opcion->flag_general)
				{
					$permiso = $this->RetornarUsuarioOpcion($id_usuario, $opcion->id); 
					if (!is_null($permiso)) 
					{
		
						if ($permiso->flag_habilitado) 
						{
							$resultado->id = $permiso->id;
							$resultado->codigo = "02";
							$resultado->isOK = TRUE;
							$resultado->mensaje = "Permiso concedido";
						} 
						else 
						{
						
							$resultado->id = 0;
							$resultado->codigo = "03";
							$resultado->isOK = FALSE;
							$resultado->mensaje = "Usuario Deshabilitado para esta opcion!";
						}
					} 
					else 
					{ 
						$resultado->id = 0;
						$resultado->codigo = "05";
						$resultado->isOK = FALSE;
						$resultado->mensaje = "Opcion No habilitada!";
					
					}	
					
				}
				else
				{
					$permiso = $this->RetornarUsuarioCentroOpcion($id_usuario, $opcion->id, $id_centro);
					if (!is_null($permiso)) 
					{
		
						if ($permiso->flag_habilitado) 
						{
							$resultado->id = $permiso->id;
							$resultado->codigo = "02";
							$resultado->isOK = TRUE;
							$resultado->mensaje = "Permiso concedido";
						} 
						else 
						{						
							$resultado->id = 0;
							$resultado->codigo = "03";
							$resultado->isOK = FALSE;
							$resultado->mensaje = "Usuario Deshabilitado para esta opcion!";
						}
					} 
					else 
					{ 
						$resultado->id = 0;
						$resultado->codigo = "05";
						$resultado->isOK = FALSE;
						$resultado->mensaje = "Opcion No habilitada!";
					
					}
					
				}
				 
				
			}
		}
	
		return $resultado;
	}
	
	public function ValidarOpcionXIdOpcionIdUsuario($id_opcion, $id_usuario, $id_centro) 
	{
		$permiso = false;
	
		$resultado = new OperacionResultado();
		$opcion = $this->RetornarOpcionXId($id_opcion);
	
		if (!is_null($opcion)) 
		{
			$resultado->id = 0;
			$resultado->codigo = "00";
			$resultado->isOK = false;
			$resultado->mensaje = "No se encontró la opción";
						
		} 
		else 
			$resultadoo = $this->ValidarOpcionXIdUsuario($opcion->opcion_key, $id_usuario, $id_centro);
		
		return $resultado;
	
	}
	
	public function ListarMenuTopItemsXIdUsuarioIdCentro($id_usuario, $id_centro)
	{
		$opcDA = new OpcionDA();
		return $opcDA->ListarMenuTopItemsXIdUsuarioIdCentro($id_usuario, $id_centro);
	}
	
	public function ListarMenuSubItemsXIdUsuarioIdCentro($id_usuario, $id_centro)
	{
		$opcDA = new OpcionDA();
		return $opcDA->ListarMenuSubItemsXIdUsuarioIdCentro($id_usuario, $id_centro);
	}
	
	public function DesahibilitarOpcionesXIdUsuario($id_usuario)
	{
		$opcDA = new OpcionDA(); 
		$opcDA->DesahibilitarOpcionesXIdUsuario($id_usuario);
	}
	
	public function RegistrarUsuarioOpcion($obj)
	{
		$opcDA = new OpcionDA(); 
		$opcDA->RegistrarUsuarioOpcion($obj);
	}
	public function ModificarUsuarioOpcion($obj)
	{
		$opcDA = new OpcionDA();
		$opcDA->ModificarUsuarioOpcion($obj); 
	}
	
	public function RetornarUsuarioOpcionXIdUsuarioIdOpcion($id_usuario, $id_opcion)
	{
		$lista = $this->ListarUsuarioOpcion("uo.id_usuario = $id_usuario AND uo.id_opcion = $id_opcion");
		if(!is_null($lista))
			if(count($lista) > 0)
				return $lista[0];
			else 
				return NULL;
		else
			return NULL;	
	}
	
	public function Modificar($obj)
	{
		$opcDA = new OpcionDA();
		$opcDA->Modificar($obj);
	}
	
	public function RegistrarCentroOpcion($obj)
	{
		$opcDA = new OpcionDA();
		$opcDA->RegistrarCentroOpcion($obj);
	}
	
	public function ModificarCentroOpcion($obj)
	{
		$opcDA = new OpcionDA();
		$opcDA->ModificarCentroOpcion($obj);
	}
	
	public function RetornarCentroOpcionXIdCentroIdOpcion($id_centro, $id_opcion)
	{
		$lista = $this->ListarCentroOpcion("co.id_centro = $id_centro AND co.id_opcion = $id_opcion");
		
		if(!is_null($lista))
			if(count($lista) > 0)
				return $lista[0];
			else
				return NULL;
		else
				return NULL;		
	}
	
	public function ListarCentrosXIdOpcion($id_opcion)
	{
		return $this->ListarCentroOpcion("co.id_opcion = $id_opcion");
	}
	
	public function ListarOpcionCentrosHabilitadosXIdOpcion($id_opcion)
	{
		return $this->ListarCentroOpcion("co.id_opcion = $id_opcion AND co.flag_habilitado = 1");
	}
	
	public function ListarOpcionConCondicion($id_opcion_padre, $flag_publica, $flag_general)
	{
		return $this->Listar2("o.id_opcion_padre = $id_opcion_padre AND o.flag_publica = $flag_publica AND o.flag_general = $flag_general AND o.estado = 1");
	}
	public function ModificarUsuarioCentroOpcion($obj)
	{
		$objDA = new OpcionDA();
		$objDA->ModificarUsuarioCentroOpcion($obj);
	}
	
	public function RegistrarUsuarioCentroOpcion($obj)
	{
		$objDA = new OpcionDA();
		$objDA->RegistrarUsuarioCentroOpcion($obj);
	}
}
?>